<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppSession;
use App\Services\WhatsAppBridgeService;
use App\Services\Notifications\WhatsAppNotificationService;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppBridgeTest extends TestCase
{
    use RefreshDatabase;

    // ── Bridge webhook: connection state changes ──────────────────────────────

    public function test_bridge_webhook_connected_event_updates_session(): void
    {
        $secret = 'test-bridge-secret';
        config(['services.whatsapp_bridge.secret' => $secret]);

        $response = $this->withHeaders(['X-Bridge-Secret' => $secret])
            ->postJson('/whatsapp/bridge-webhook', [
                'event'   => 'connection.update',
                'payload' => ['state' => 'connected', 'phone_number' => '94712345678'],
            ]);

        $response->assertStatus(200)->assertJson(['ok' => true, 'state' => 'connected']);

        $session = WhatsAppSession::singleton();
        $this->assertEquals('connected', $session->state);
        $this->assertEquals('94712345678', $session->phone_number);
    }

    public function test_bridge_webhook_disconnected_event_updates_session(): void
    {
        $secret = 'test-bridge-secret';
        config(['services.whatsapp_bridge.secret' => $secret]);

        // First connect
        $session = WhatsAppSession::singleton();
        $session->markConnected('94712345678');

        // Now disconnect via webhook
        $this->withHeaders(['X-Bridge-Secret' => $secret])
            ->postJson('/whatsapp/bridge-webhook', [
                'event'   => 'connection.update',
                'payload' => ['state' => 'disconnected', 'phone_number' => null],
            ]);

        $session->refresh();
        $this->assertEquals('disconnected', $session->state);
        $this->assertNull($session->phone_number);
    }

    public function test_bridge_webhook_rejects_invalid_secret(): void
    {
        config(['services.whatsapp_bridge.secret' => 'correct-secret']);

        $response = $this->withHeaders(['X-Bridge-Secret' => 'wrong-secret'])
            ->postJson('/whatsapp/bridge-webhook', [
                'event'   => 'connection.update',
                'payload' => ['state' => 'connected', 'phone_number' => '94712345678'],
            ]);

        $response->assertStatus(401);
    }

    // ── Bridge webhook: incoming message → bot pipeline ──────────────────────

    public function test_bridge_webhook_incoming_message_dispatches_to_bot_pipeline(): void
    {
        SiteSetting::set('whatsapp_bot_enabled', '0');
        $secret = 'test-bridge-secret';
        config(['services.whatsapp_bridge.secret' => $secret]);

        $response = $this->withHeaders(['X-Bridge-Secret' => $secret])
            ->postJson('/whatsapp/bridge-webhook', [
                'event'   => 'messages.upsert',
                'payload' => [
                    'from'       => '+94771234567',
                    'text'       => 'What is the price of iPhone screen?',
                    'timestamp'  => time(),
                    'message_id' => 'test-msg-001',
                ],
            ]);

        $response->assertStatus(200)->assertJsonStructure(['ok', 'conversation_id']);

        // Conversation and message should be persisted
        $this->assertDatabaseHas('whatsapp_conversations', [
            'phone_number' => '+94771234567',
        ]);
        $this->assertDatabaseHas('whatsapp_messages', [
            'direction'          => 'inbound',
            'sender_type'        => 'customer',
            'content'            => 'What is the price of iPhone screen?',
            'provider_message_id' => 'test-msg-001',
        ]);
    }

    // ── WhatsAppSession model helpers ─────────────────────────────────────────

    public function test_whatsapp_session_singleton_creates_and_transitions_state(): void
    {
        $session = WhatsAppSession::singleton();
        $this->assertEquals('disconnected', $session->state);

        $session->markConnected('94799887766');
        $this->assertTrue($session->isConnected());
        $this->assertEquals('94799887766', $session->phone_number);

        $session->markDisconnected();
        $this->assertFalse($session->isConnected());
        $this->assertNull($session->phone_number);
    }

    // ── Order notification routing via Baileys bridge ─────────────────────────

    public function test_order_update_routes_through_bridge_when_connected(): void
    {
        // Mock the bridge service to simulate a connected session
        $session = WhatsAppSession::singleton();
        $session->markConnected('94712345678');

        Http::fake(['http://127.0.0.1:3000/*' => Http::response(['success' => true], 200)]);

        $user = User::factory()->create();
        $order = Order::create([
            'user_id'          => $user->id,
            'order_number'     => 'ORD-8800',
            'customer_name'    => 'Test Customer',
            'customer_email'   => 'test@example.com',
            'customer_phone'   => '+94712345678',
            'shipping_address' => '1 Test St',
            'shipping_city'    => 'Colombo',
            'status'           => 'processing',
            'payment_status'   => 'paid',
            'payment_method'   => 'card',
            'subtotal'         => 5000,
            'total_amount'     => 5000,
        ]);

        $service = app(WhatsAppNotificationService::class);
        $result  = $service->sendOrderUpdate($order, 'order_placed');

        // The bridge HTTP call was made
        Http::assertSent(fn ($req) => str_contains($req->url(), '/send-message'));
        $this->assertTrue($result);
    }
}
