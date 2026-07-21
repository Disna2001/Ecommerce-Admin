<?php

namespace App\Services\Notifications;

use App\Models\NotificationOutbox;
use App\Models\Order;
use App\Models\SiteSetting;
use App\Models\WhatsAppSession;
use App\Services\WhatsAppBridgeService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    public function sendRawWhatsAppMessage(string $phone, string $body): bool
    {
        $normalizedPhone = $this->normalizePhone($phone);
        if (!$normalizedPhone) {
            return false;
        }

        // Try Baileys bridge first if session is connected
        $session = WhatsAppSession::singleton();
        if ($session->isConnected()) {
            try {
                $bridge = app(WhatsAppBridgeService::class);
                return $bridge->sendMessage($normalizedPhone, $body);
            } catch (\Throwable $e) {
                Log::warning('Baileys bridge sendRawMessage failed: ' . $e->getMessage(), ['phone' => $phone]);
            }
        }

        if (!SiteSetting::get('whatsapp_enabled', false)) {
            return false;
        }

        $endpoint = SiteSetting::get('whatsapp_api_url');
        $token    = SiteSetting::get('whatsapp_api_key');
        $provider = SiteSetting::get('whatsapp_provider', 'custom');

        if (!$endpoint) {
            return false;
        }

        try {
            $response = match ($provider) {
                'meta_cloud' => $this->sendMetaCloudMessage($endpoint, $token, $normalizedPhone, $body),
                default => Http::withHeaders(array_filter([
                        'Authorization' => $token ? 'Bearer ' . $token : null,
                    ]))
                    ->acceptJson()
                    ->post($endpoint, [
                        'provider' => $provider,
                        'to'      => $normalizedPhone,
                        'message' => $body,
                    ]),
            };

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Raw WhatsApp dispatch failed: ' . $e->getMessage(), ['phone' => $phone]);
            return false;
        }
    }

    public function sendOrderUpdate(Order $order, string $stage, ?string $message = null, ?int $outboxId = null): bool
    {
        $outbox = $outboxId ? NotificationOutbox::find($outboxId) : null;
        $phone  = $this->normalizePhone($order->customer_phone);
        $body   = $message ?: $this->resolveTemplateMessage($order, $stage);

        if (!$phone) {
            $this->markOutboxAsFailed($outbox, 'Customer phone number is missing.');
            return false;
        }

        // ── Baileys bridge (highest priority when connected) ──────────────────
        $session = WhatsAppSession::singleton();
        if ($session->isConnected()) {
            try {
                $bridge   = app(WhatsAppBridgeService::class);
                $success  = $bridge->sendMessage($phone, $body);
                if ($success) {
                    $this->markOutboxAsSent($outbox);
                    return true;
                }
                $this->markOutboxAsFailed($outbox, 'Baileys bridge send returned failure.');
                return false;
            } catch (\Throwable $e) {
                Log::warning('Baileys bridge order update failed: ' . $e->getMessage(), ['order_id' => $order->id]);
            }
        }

        // ── Fallback: Meta Cloud API / Custom proxy ────────────────────────────
        if (!SiteSetting::get('whatsapp_enabled', false)) {
            $this->markOutboxAsSkipped($outbox, 'WhatsApp notifications are disabled.');
            return false;
        }

        $endpoint = SiteSetting::get('whatsapp_api_url');
        $token    = SiteSetting::get('whatsapp_api_key');
        $provider = SiteSetting::get('whatsapp_provider', 'custom');

        if (!$endpoint) {
            $this->markOutboxAsFailed($outbox, 'WhatsApp endpoint is missing.');
            return false;
        }

        try {
            $response = match ($provider) {
                'meta_cloud' => $this->sendMetaCloudMessage($endpoint, $token, $phone, $body),
                default      => $this->sendWebhookMessage($endpoint, $token, $order, $stage, $phone, $body),
            };

            if ($response->failed()) {
                $this->markOutboxAsFailed($outbox, 'WhatsApp provider returned HTTP ' . $response->status() . '.');
                Log::warning('WhatsApp notification request failed.', [
                    'order_id' => $order->id,
                    'stage'    => $stage,
                    'status'   => $response->status(),
                    'body'     => $response->body(),
                ]);
                return false;
            }

            $this->captureProviderMessageId($outbox, $response->json());
            $this->markOutboxAsSent($outbox);
            return true;
        } catch (\Throwable $e) {
            $this->markOutboxAsFailed($outbox, $e->getMessage());
            Log::warning('WhatsApp notification dispatch failed.', [
                'order_id' => $order->id,
                'stage'    => $stage,
                'error'    => $e->getMessage(),
            ]);
            return false;
        }
    }

    protected function resolveTemplateMessage(Order $order, string $stage): string
    {
        $template = str_contains($stage, 'payment')
            ? SiteSetting::get('whatsapp_payment_template', 'Payment update for order {order_number}: {payment_status}.')
            : SiteSetting::get('whatsapp_order_template', 'Your order {order_number} is now {order_status}.');

        return strtr($template, [
            '{order_number}' => $order->order_number,
            '{order_status}' => $order->status_label,
            '{payment_status}' => $order->payment_status,
            '{customer_name}' => $order->customer_name,
        ]);
    }

    protected function sendMetaCloudMessage(string $endpoint, ?string $token, string $phone, string $body)
    {
        return Http::withToken($token)
            ->acceptJson()
            ->post($endpoint, [
                'messaging_product' => 'whatsapp',
                'to' => $phone,
                'type' => 'text',
                'text' => [
                    'preview_url' => false,
                    'body' => $body,
                ],
            ]);
    }

    protected function sendWebhookMessage(string $endpoint, ?string $token, Order $order, string $stage, string $phone, string $body)
    {
        return Http::withHeaders(array_filter([
                'Authorization' => $token ? 'Bearer ' . $token : null,
            ]))
            ->acceptJson()
            ->post($endpoint, [
                'provider' => SiteSetting::get('whatsapp_provider', 'custom'),
                'to' => $phone,
                'message' => $body,
                'stage' => $stage,
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'customer_name' => $order->customer_name,
                ],
            ]);
    }

    public function normalizePhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $normalized = preg_replace('/[^\d+]/', '', $phone);

        if (!$normalized) {
            return null;
        }

        if (str_starts_with($normalized, '0')) {
            return '+94' . substr($normalized, 1);
        }

        if (!str_starts_with($normalized, '+')) {
            return '+' . $normalized;
        }

        return $normalized;
    }

    protected function markOutboxAsSent(?NotificationOutbox $outbox): void
    {
        if (!$outbox) {
            return;
        }

        $outbox->update([
            'status' => 'sent',
            'sent_at' => now(),
            'last_attempt_at' => now(),
            'failed_at' => null,
            'failure_message' => null,
        ]);
    }

    protected function captureProviderMessageId(?NotificationOutbox $outbox, array $responseBody): void
    {
        if (!$outbox) {
            return;
        }

        $messageId = data_get($responseBody, 'messages.0.id');

        if (!filled($messageId)) {
            return;
        }

        $payload = $outbox->payload ?? [];
        $payload['provider_message_id'] = $messageId;
        $payload['provider_response'] = $responseBody;

        $outbox->update([
            'payload' => $payload,
        ]);
    }

    protected function markOutboxAsFailed(?NotificationOutbox $outbox, string $message): void
    {
        if (!$outbox) {
            return;
        }

        $outbox->update([
            'status' => 'failed',
            'last_attempt_at' => now(),
            'failed_at' => now(),
            'failure_message' => $message,
        ]);
    }

    protected function markOutboxAsSkipped(?NotificationOutbox $outbox, string $message): void
    {
        if (!$outbox) {
            return;
        }

        $outbox->update([
            'status' => 'skipped',
            'last_attempt_at' => now(),
            'failed_at' => now(),
            'failure_message' => $message,
        ]);
    }
}
