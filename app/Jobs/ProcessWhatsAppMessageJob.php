<?php

namespace App\Jobs;

use App\Models\SiteSetting;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\Notifications\WhatsAppNotificationService;
use App\Services\WhatsAppBotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessWhatsAppMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $messageId) {}

    public function handle(WhatsAppBotService $botService, WhatsAppNotificationService $notificationService): void
    {
        $message = WhatsAppMessage::with('conversation')->find($this->messageId);
        if (!$message || $message->direction !== 'inbound') {
            return;
        }

        $conversation = $message->conversation;
        if (!$conversation) {
            return;
        }

        // Check 0: Is Bot Enabled globally?
        if (!filter_var(SiteSetting::get('whatsapp_bot_enabled', false), FILTER_VALIDATE_BOOLEAN)) {
            return;
        }

        // Step 1: Check conversation status (human_active or awaiting_human)
        if (in_array($conversation->status, ['human_active', 'awaiting_human'], true)) {
            return;
        }

        // Step 2: Check Business Hours
        if (!$botService->isWithinBusinessHours()) {
            $outsideHoursMsg = SiteSetting::get(
                'whatsapp_bot_outside_hours_message',
                'Our office is currently closed. We will reply to your message during standard business hours.'
            );
            $this->sendOutboundReply($conversation, $outsideHoursMsg, $notificationService);
            return;
        }

        // Step 3: Check Escalation Keywords & Handoff Affirmation
        $text = strtolower(trim($message->content));
        
        if ($conversation->awaiting_handoff_confirmation && $this->isAffirmativeResponse($text)) {
            $conversation->markAsAwaitingHuman();
            $handoffMsg = "Connecting you with a team member right away. An agent will be with you shortly.";
            $this->sendOutboundReply($conversation, $handoffMsg, $notificationService);
            $botService->notifyAdminOfEscalation($conversation, $message);
            return;
        }

        if ($botService->containsEscalationKeyword($text)) {
            $conversation->markAsAwaitingHuman();
            $escalationMsg = "Connecting you with a team member shortly. An agent has been notified.";
            $this->sendOutboundReply($conversation, $escalationMsg, $notificationService);
            $botService->notifyAdminOfEscalation($conversation, $message);
            return;
        }

        // Step 4: AI Reply Generation with Tool Calling
        $aiResult = $botService->generateReply($conversation, $message->content);
        $replyText = $aiResult['reply'] ?? SiteSetting::get('whatsapp_bot_fallback_message', 'Thank you for reaching out. How else can I assist you?');
        $toolCalls = $aiResult['tool_calls'] ?? null;

        // Auto-reply count tracking & offer handoff when max replies reached
        $conversation->increment('bot_reply_count');
        $maxReplies = (int) SiteSetting::get('whatsapp_bot_max_auto_replies', 3);

        if ($conversation->bot_reply_count >= $maxReplies) {
            $replyText .= "\n\nWould you like me to connect you with a team member?";
            $conversation->update(['awaiting_handoff_confirmation' => true]);
        }

        // Step 5: Send Reply & Store Outbound Record
        $this->sendOutboundReply($conversation, $replyText, $notificationService, 'bot', $toolCalls);
    }

    protected function isAffirmativeResponse(string $text): bool
    {
        return in_array($text, ['yes', 'yeah', 'yep', 'please', 'ok', 'okay', 'sure', 'connect', 'human', 'agent'], true)
            || str_contains($text, 'yes')
            || str_contains($text, 'please');
    }

    protected function sendOutboundReply(
        WhatsAppConversation $conversation,
        string $text,
        WhatsAppNotificationService $notificationService,
        string $senderType = 'bot',
        ?array $toolCalls = null
    ): void {
        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'sender_type' => $senderType,
            'content' => $text,
            'message_type' => 'text',
            'tool_calls' => $toolCalls,
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Send outbound text message via provider
        $notificationService->sendRawWhatsAppMessage($conversation->phone_number, $text);
    }
}
