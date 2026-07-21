<?php

namespace Tests\Feature;

use App\Jobs\ProcessWhatsAppMessageJob;
use App\Models\NotificationOutbox;
use App\Models\Order;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\Notifications\WhatsAppNotificationService;
use App\Services\WhatsAppBotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class WhatsAppBotTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_verification(): void
    {
        SiteSetting::set('whatsapp_webhook_verify_token', 'secret_token_123');

        $response = $this->get('/whatsapp/webhook?' . http_build_query([
            'hub_mode' => 'subscribe',
            'hub_verify_token' => 'secret_token_123',
            'hub_challenge' => 'challenge_code_abc',
        ]));

        $response->assertStatus(200);
        $this->assertEquals('challenge_code_abc', $response->getContent());
    }

    public function test_phone_number_normalization_for_whatsapp_links(): void
    {
        $service = app(WhatsAppNotificationService::class);
        $cleaned = ltrim($service->normalizePhone('+94 (70) 261-5076'), '+');
        $this->assertEquals('94702615076', $cleaned);
    }

    public function test_order_lookup_tool_privacy_enforcement(): void
    {
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-9999',
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+94771234567',
            'shipping_address' => '123 Main St',
            'shipping_city' => 'Colombo',
            'status' => 'processing',
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'subtotal' => 15000,
            'total_amount' => 15000,
        ]);

        $ownerConversation = WhatsAppConversation::create([
            'phone_number' => '+94771234567',
            'customer_name' => 'John Doe',
        ]);

        $strangerConversation = WhatsAppConversation::create([
            'phone_number' => '+94779998888',
            'customer_name' => 'Jane Smith',
        ]);

        $botService = app(WhatsAppBotService::class);

        // Owner lookup -> Success
        $ownerResult = $botService->lookupOrderStatusTool($ownerConversation, 'ORD-9999');
        $this->assertTrue($ownerResult['found']);
        $this->assertEquals('ORD-9999', $ownerResult['order_number']);

        // Stranger lookup -> Blocked by Privacy Scope
        $strangerResult = $botService->lookupOrderStatusTool($strangerConversation, 'ORD-9999');
        $this->assertFalse($strangerResult['found']);
        $this->assertEquals('Order not found under your phone number.', $strangerResult['message']);
    }

    public function test_escalation_keyword_triggers_human_handoff(): void
    {
        \Illuminate\Support\Carbon::setTestNow('2026-07-21 12:00:00');
        SiteSetting::set('whatsapp_bot_enabled', '1');
        SiteSetting::set('whatsapp_bot_escalation_keywords', json_encode(['human', 'agent', 'support']));

        $conversation = WhatsAppConversation::create([
            'phone_number' => '+94700000000',
            'status' => 'bot_active',
        ]);

        $message = WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction' => 'inbound',
            'sender_type' => 'customer',
            'content' => 'I want to speak with a human agent please',
        ]);

        $job = new ProcessWhatsAppMessageJob($message->id);
        $job->handle(app(WhatsAppBotService::class), app(WhatsAppNotificationService::class));

        $conversation->refresh();
        $this->assertEquals('awaiting_human', $conversation->status);

        // System notification outbox record created for admin
        $this->assertDatabaseHas('notification_outboxes', [
            'channel' => 'system_alert',
        ]);
    }

    public function test_agent_reply_and_return_to_bot_flow(): void
    {
        Permission::firstOrCreate(['name' => 'view whatsapp conversations', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->givePermissionTo('view whatsapp conversations');

        $conversation = WhatsAppConversation::create([
            'phone_number' => '+94711112222',
            'customer_name' => 'Test Customer',
            'status' => 'awaiting_human',
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Admin\WhatsAppConversationsManager::class)
            ->call('selectConversation', $conversation->id)
            ->set('agentReplyText', 'Hello! I am your human support representative.')
            ->call('sendAgentReply');

        $conversation->refresh();
        $this->assertEquals('human_active', $conversation->status);
        $this->assertDatabaseHas('whatsapp_messages', [
            'conversation_id' => $conversation->id,
            'sender_type' => 'agent',
            'content' => 'Hello! I am your human support representative.',
        ]);

        // Hand back to bot
        Livewire::actingAs($user)
            ->test(\App\Livewire\Admin\WhatsAppConversationsManager::class)
            ->call('selectConversation', $conversation->id)
            ->call('returnToBot');

        $conversation->refresh();
        $this->assertEquals('bot_active', $conversation->status);
    }
}
