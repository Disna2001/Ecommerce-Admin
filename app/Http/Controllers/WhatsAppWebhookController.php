<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessWhatsAppMessageJob;
use App\Models\NotificationOutbox;
use App\Models\SiteSetting;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\Notifications\WhatsAppNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $mode = (string) ($request->query('hub_mode') ?: $request->query('hub.mode'));
        $token = (string) ($request->query('hub_verify_token') ?: $request->query('hub.verify_token'));
        $challenge = (string) ($request->query('hub_challenge') ?: $request->query('hub.challenge'));
        $expectedToken = (string) SiteSetting::get('whatsapp_webhook_verify_token', '');

        if ($mode === 'subscribe' && filled($expectedToken) && hash_equals($expectedToken, $token)) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning('WhatsApp webhook verification failed.', [
            'mode' => $mode,
            'token_present' => filled($token),
        ]);

        return response('Forbidden', 403);
    }

    public function receive(Request $request): Response
    {
        // Signature verification if configured
        if (!$this->verifySignature($request)) {
            Log::warning('WhatsApp webhook signature verification failed.');
            return response('Invalid Signature', 401);
        }

        $notificationService = app(WhatsAppNotificationService::class);

        foreach (($request->input('entry') ?? []) as $entry) {
            foreach (($entry['changes'] ?? []) as $change) {
                $value = $change['value'] ?? [];
                
                // Status updates (sent, delivered, read, failed)
                foreach (($value['statuses'] ?? []) as $status) {
                    $this->syncStatus($status, $value);
                }

                // Inbound Messages
                $contacts = $value['contacts'] ?? [];
                foreach (($value['messages'] ?? []) as $inboundMsg) {
                    $this->handleInboundMessage($inboundMsg, $contacts, $notificationService);
                }
            }
        }

        return response('EVENT_RECEIVED', 200);
    }

    protected function handleInboundMessage(array $inboundMsg, array $contacts, WhatsAppNotificationService $notificationService): void
    {
        $rawFrom = (string) Arr::get($inboundMsg, 'from', '');
        $phone = $notificationService->normalizePhone($rawFrom);
        $providerMsgId = (string) Arr::get($inboundMsg, 'id', '');
        
        $body = (string) Arr::get($inboundMsg, 'text.body', 
            Arr::get($inboundMsg, 'button.text', 
            Arr::get($inboundMsg, 'interactive.button_reply.title', '')));

        if (!filled($phone) || !filled($body)) {
            return;
        }

        // Deduplication check
        if (filled($providerMsgId) && WhatsAppMessage::where('provider_message_id', $providerMsgId)->exists()) {
            return;
        }

        // Customer Name from Profile
        $customerName = null;
        foreach ($contacts as $contact) {
            if ((string) Arr::get($contact, 'wa_id') === preg_replace('/[^\d]/', '', $rawFrom)) {
                $customerName = Arr::get($contact, 'profile.name');
                break;
            }
        }

        // Upsert Conversation
        $conversation = WhatsAppConversation::firstOrCreate(
            ['phone_number' => $phone],
            [
                'customer_name' => $customerName,
                'status' => 'bot_active',
                'last_message_at' => now(),
            ]
        );

        if ($customerName && !$conversation->customer_name) {
            $conversation->update(['customer_name' => $customerName]);
        }

        $conversation->update(['last_message_at' => now()]);

        // Create Message
        $message = WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction' => 'inbound',
            'sender_type' => 'customer',
            'content' => $body,
            'message_type' => 'text',
            'provider_message_id' => $providerMsgId,
        ]);

        // Dispatch background job for AI processing
        ProcessWhatsAppMessageJob::dispatch($message->id);
    }

    protected function verifySignature(Request $request): bool
    {
        $signatureHeader = $request->header('x-hub-signature-256');
        $verifyToken = SiteSetting::get('whatsapp_webhook_verify_token');

        if (!filled($signatureHeader) || !filled($verifyToken)) {
            return true; // If signature header is not set by provider or verify token not enforcing HMAC, allow
        }

        $expectedHash = hash_hmac('sha256', $request->getContent(), $verifyToken);
        $providedHash = str_replace('sha256=', '', $signatureHeader);

        return hash_equals($expectedHash, $providedHash);
    }

    protected function syncStatus(array $status, array $value): void
    {
        $messageId = (string) Arr::get($status, 'id', '');
        $recipientId = preg_replace('/[^\d]/', '', (string) Arr::get($status, 'recipient_id', ''));
        $state = (string) Arr::get($status, 'status', '');
        $timestamp = Arr::get($status, 'timestamp');
        $errors = Arr::get($status, 'errors', []);

        $outbox = NotificationOutbox::query()
            ->where('channel', 'whatsapp')
            ->when(filled($messageId), fn ($query) => $query->where('payload->provider_message_id', $messageId))
            ->when(!filled($messageId) && filled($recipientId), fn ($query) => $query->where('recipient', 'like', '%' . $recipientId . '%'))
            ->latest()
            ->first();

        if (!$outbox) {
            Log::warning('WhatsApp webhook status did not match any outbox record.', [
                'message_id' => $messageId,
                'recipient_id' => $recipientId,
                'status' => $state,
            ]);

            return;
        }

        $payload = $outbox->payload ?? [];
        $payload['provider_message_id'] = $messageId ?: ($payload['provider_message_id'] ?? null);
        $payload['webhook_status'] = $state;
        $payload['webhook_value'] = $value;
        $payload['last_webhook_at'] = now()->toIso8601String();

        $update = [
            'payload' => $payload,
            'last_attempt_at' => now(),
        ];

        if ($state === 'failed') {
            $firstError = is_array($errors) ? Arr::first($errors) : null;
            $update['status'] = 'failed';
            $update['failed_at'] = $timestamp ? now()->createFromTimestamp((int) $timestamp) : now();
            $update['failure_message'] = is_array($firstError)
                ? (string) ($firstError['title'] ?? $firstError['message'] ?? 'WhatsApp delivery failed.')
                : 'WhatsApp delivery failed.';
        } else {
            $update['status'] = 'sent';
            $update['sent_at'] = $outbox->sent_at ?: ($timestamp ? now()->createFromTimestamp((int) $timestamp) : now());
            $update['failed_at'] = null;
            $update['failure_message'] = null;
        }

        $outbox->update($update);
    }
}
