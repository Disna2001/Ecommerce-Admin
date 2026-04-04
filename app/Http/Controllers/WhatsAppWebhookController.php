<?php

namespace App\Http\Controllers;

use App\Models\NotificationOutbox;
use App\Models\SiteSetting;
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
        foreach (($request->input('entry') ?? []) as $entry) {
            foreach (($entry['changes'] ?? []) as $change) {
                foreach (($change['value']['statuses'] ?? []) as $status) {
                    $this->syncStatus($status, $change['value'] ?? []);
                }
            }
        }

        return response('EVENT_RECEIVED', 200);
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
