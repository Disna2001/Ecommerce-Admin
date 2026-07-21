<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessWhatsAppMessageJob;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Receives webhook events pushed by the whatsapp-bridge Node.js service.
 * Route: POST /whatsapp/bridge-webhook  (public, secret-verified)
 */
class WhatsAppBridgeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // ── 1. Verify shared secret ───────────────────────────────────────────
        $expectedSecret = config('services.whatsapp_bridge.secret', '');
        $receivedSecret = $request->header('X-Bridge-Secret', '');

        if ($expectedSecret && !hash_equals($expectedSecret, $receivedSecret)) {
            Log::warning('WhatsApp bridge webhook: invalid secret', ['ip' => $request->ip()]);
            abort(401, 'Invalid bridge secret.');
        }

        $event   = $request->input('event');
        $payload = $request->input('payload', []);

        return match ($event) {
            'connection.update' => $this->handleConnectionUpdate($payload),
            'messages.upsert'   => $this->handleIncomingMessage($payload),
            default             => response()->json(['ignored' => true]),
        };
    }

    // ─── Connection state changes ─────────────────────────────────────────────

    protected function handleConnectionUpdate(array $payload)
    {
        $state  = $payload['state']        ?? 'disconnected';
        $phone  = $payload['phone_number'] ?? null;

        $session = WhatsAppSession::singleton();

        match ($state) {
            'connected'    => $session->markConnected($phone ?? ''),
            'connecting'   => $session->markConnecting(),
            default        => $session->markDisconnected(),
        };

        Log::info("WhatsApp bridge connection → {$state}", ['phone' => $phone]);

        return response()->json(['ok' => true, 'state' => $state]);
    }

    // ─── Incoming messages (reuse existing AI bot pipeline) ──────────────────

    protected function handleIncomingMessage(array $payload)
    {
        $from  = $payload['from']       ?? null;
        $text  = $payload['text']       ?? '';
        $msgId = $payload['message_id'] ?? null;

        if (!$from || !$text) {
            return response()->json(['ignored' => true]);
        }

        // Normalise phone (strip + prefix)
        $normalizedFrom = preg_replace('/[^\d+]/', '', $from);

        // Upsert conversation
        $conversation = WhatsAppConversation::firstOrCreate(
            ['phone_number' => $normalizedFrom],
            ['customer_name' => $normalizedFrom, 'status' => 'bot_active']
        );

        // Persist inbound message
        $message = WhatsAppMessage::create([
            'conversation_id'    => $conversation->id,
            'direction'          => 'inbound',
            'sender_type'        => 'customer',
            'content'            => $text,
            'provider_message_id' => $msgId,
        ]);

        // Dispatch into the existing AI Bot / escalation pipeline
        ProcessWhatsAppMessageJob::dispatch($message->id);

        return response()->json(['ok' => true, 'conversation_id' => $conversation->id]);
    }
}
