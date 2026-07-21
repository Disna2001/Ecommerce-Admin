<?php

namespace App\Livewire\Admin;

use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\Notifications\WhatsAppNotificationService;
use Livewire\Component;
use Livewire\WithPagination;

class WhatsAppConversationsManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public ?int $selectedConversationId = null;

    public string $agentReplyText = '';

    protected $queryString = [
        'statusFilter' => ['except' => 'all', 'as' => 'status'],
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function selectConversation(int $id): void
    {
        $this->selectedConversationId = $id;
    }

    public function sendAgentReply(WhatsAppNotificationService $notificationService): void
    {
        if (! $this->selectedConversationId || ! filled(trim($this->agentReplyText))) {
            return;
        }

        $conversation = WhatsAppConversation::find($this->selectedConversationId);
        if (! $conversation) {
            return;
        }

        $replyContent = trim($this->agentReplyText);
        $this->agentReplyText = '';

        // Create Outbound Agent Message
        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'sender_type' => 'agent',
            'content' => $replyContent,
            'message_type' => 'text',
        ]);

        // Automatically switch conversation status to human_active
        $conversation->markAsHumanActive();
        $conversation->update(['last_message_at' => now()]);

        // Send Outbound Message via Provider
        $notificationService->sendRawWhatsAppMessage($conversation->phone_number, $replyContent);

        $this->dispatch('notify', type: 'success', message: 'Message sent to customer.');
    }

    public function returnToBot(): void
    {
        if (! $this->selectedConversationId) {
            return;
        }

        $conversation = WhatsAppConversation::find($this->selectedConversationId);
        if (! $conversation) {
            return;
        }

        $conversation->returnToBot();

        // Log system note
        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'sender_type' => 'system',
            'content' => 'Conversation handed back to AI Bot handling.',
            'message_type' => 'system_note',
        ]);

        $this->dispatch('notify', type: 'info', message: 'Conversation returned to AI Bot.');
    }

    public function updateStatus(string $newStatus): void
    {
        if (! $this->selectedConversationId || ! in_array($newStatus, ['bot_active', 'awaiting_human', 'human_active', 'closed'], true)) {
            return;
        }

        $conversation = WhatsAppConversation::find($this->selectedConversationId);
        if ($conversation) {
            $conversation->update(['status' => $newStatus]);
            $this->dispatch('notify', type: 'success', message: "Status updated to {$newStatus}.");
        }
    }

    public function render()
    {
        $conversationsQuery = WhatsAppConversation::query()
            ->when(filled($this->search), function ($q) {
                $term = '%' . trim($this->search) . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('phone_number', 'like', $term)
                        ->orWhere('customer_name', 'like', $term);
                });
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->orderBy('last_message_at', 'desc');

        $conversations = $conversationsQuery->paginate(20);

        // Auto select first conversation if none selected
        if (! $this->selectedConversationId && $conversations->count() > 0) {
            $this->selectedConversationId = $conversations->first()->id;
        }

        $selectedConversation = $this->selectedConversationId
            ? WhatsAppConversation::with(['messages'])->find($this->selectedConversationId)
            : null;

        $awaitingHumanCount = WhatsAppConversation::where('status', 'awaiting_human')->count();
        $botActiveCount = WhatsAppConversation::where('status', 'bot_active')->count();
        $humanActiveCount = WhatsAppConversation::where('status', 'human_active')->count();

        return view('livewire.admin.whats-app-conversations-manager', [
            'conversations' => $conversations,
            'selectedConversation' => $selectedConversation,
            'awaitingHumanCount' => $awaitingHumanCount,
            'botActiveCount' => $botActiveCount,
            'humanActiveCount' => $humanActiveCount,
        ]);
    }
}
