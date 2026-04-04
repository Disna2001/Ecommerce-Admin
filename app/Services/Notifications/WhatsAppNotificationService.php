<?php

namespace App\Services\Notifications;

use App\Models\NotificationOutbox;
use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    public function sendOrderUpdate(Order $order, string $stage, ?string $message = null, ?int $outboxId = null): bool
    {
        $outbox = $outboxId ? NotificationOutbox::find($outboxId) : null;

        if (!SiteSetting::get('whatsapp_enabled', false)) {
            $this->markOutboxAsSkipped($outbox, 'WhatsApp notifications are disabled.');
            return false;
        }

        $endpoint = SiteSetting::get('whatsapp_api_url');
        $token = SiteSetting::get('whatsapp_api_key');
        $provider = SiteSetting::get('whatsapp_provider', 'custom');
        $phone = $this->normalizePhone($order->customer_phone);

        if (!$endpoint || !$phone) {
            $this->markOutboxAsFailed($outbox, 'WhatsApp endpoint or customer phone number is missing.');
            return false;
        }

        $body = $message ?: $this->resolveTemplateMessage($order, $stage);

        try {
            $response = match ($provider) {
                'meta_cloud' => $this->sendMetaCloudMessage($endpoint, $token, $phone, $body),
                default => $this->sendWebhookMessage($endpoint, $token, $order, $stage, $phone, $body),
            };

            if ($response->failed()) {
                $this->markOutboxAsFailed($outbox, 'WhatsApp provider returned HTTP ' . $response->status() . '.');
                Log::warning('WhatsApp notification request failed.', [
                    'order_id' => $order->id,
                    'stage' => $stage,
                    'status' => $response->status(),
                    'body' => $response->body(),
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
                'stage' => $stage,
                'error' => $e->getMessage(),
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

    protected function normalizePhone(?string $phone): ?string
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
