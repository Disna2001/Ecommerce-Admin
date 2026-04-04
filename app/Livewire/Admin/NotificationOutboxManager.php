<?php

namespace App\Livewire\Admin;

use App\Jobs\SendWhatsAppNotificationJob;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\NotificationOutbox;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationOutboxManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $channelFilter = '';
    public string $statusFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public int $perPage = 15;
    public bool $showDetailModal = false;
    public ?int $selectedOutboxId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'channelFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'channelFilter', 'statusFilter', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function openDetailModal(int $id): void
    {
        $this->selectedOutboxId = $id;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->selectedOutboxId = null;
        $this->showDetailModal = false;
    }

    public function retryOutbox(int $id): void
    {
        $outbox = NotificationOutbox::findOrFail($id);

        try {
            $nextAttempt = max(1, (int) $outbox->attempt_count) + 1;

            $this->dispatchRetry($outbox);

            $outbox->update([
                'status' => 'queued',
                'attempt_count' => $nextAttempt,
                'last_attempt_at' => now(),
                'queued_at' => now(),
                'sent_at' => null,
                'failed_at' => null,
                'failure_message' => null,
            ]);

            session()->flash('message', 'Notification queued again successfully.');
        } catch (\Throwable $e) {
            $outbox->update([
                'status' => 'failed',
                'failed_at' => now(),
                'failure_message' => $e->getMessage(),
            ]);

            session()->flash('error', 'Retry failed: ' . $e->getMessage());
        }
    }

    public function getStatsProperty(): array
    {
        $total = NotificationOutbox::count();
        $failed = NotificationOutbox::where('status', 'failed')->count();

        return [
            'total' => $total,
            'queued' => NotificationOutbox::where('status', 'queued')->count(),
            'sent' => NotificationOutbox::where('status', 'sent')->count(),
            'failed' => $failed,
            'whatsapp' => NotificationOutbox::where('channel', 'whatsapp')->count(),
            'email' => NotificationOutbox::where('channel', 'email')->count(),
            'retried' => NotificationOutbox::where('attempt_count', '>', 1)->count(),
            'failure_rate' => $total > 0 ? round(($failed / $total) * 100, 1) : 0,
        ];
    }

    public function getAnalyticsProperty(): array
    {
        $providerHealth = NotificationOutbox::query()
            ->selectRaw("COALESCE(provider, 'unknown') as provider")
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count")
            ->selectRaw("SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent_count")
            ->groupBy('provider')
            ->orderByDesc('failed_count')
            ->limit(4)
            ->get();

        $failingRecipients = NotificationOutbox::query()
            ->selectRaw("COALESCE(recipient, 'No recipient') as recipient")
            ->selectRaw('COUNT(*) as failure_count')
            ->whereIn('status', ['failed', 'skipped'])
            ->groupBy('recipient')
            ->orderByDesc('failure_count')
            ->limit(5)
            ->get();

        $messageTypes = NotificationOutbox::query()
            ->selectRaw("COALESCE(subject, related_type, 'General') as label")
            ->selectRaw('COUNT(*) as total_count')
            ->groupBy('label')
            ->orderByDesc('total_count')
            ->limit(5)
            ->get();

        return [
            'providerHealth' => $providerHealth,
            'failingRecipients' => $failingRecipients,
            'messageTypes' => $messageTypes,
        ];
    }

    protected function dispatchRetry(NotificationOutbox $outbox): void
    {
        if ($outbox->channel === 'whatsapp') {
            $this->retryWhatsApp($outbox);

            return;
        }

        $this->retryEmail($outbox);
    }

    protected function retryWhatsApp(NotificationOutbox $outbox): void
    {
        if ($outbox->related_type !== Order::class || !$outbox->related_id) {
            throw new \RuntimeException('Only order-based WhatsApp updates can be retried.');
        }

        $payload = $outbox->payload ?? [];
        $stage = $payload['stage'] ?? 'status_update';
        $message = $payload['message'] ?? null;

        SendWhatsAppNotificationJob::dispatch((int) $outbox->related_id, $stage, $message, $outbox->id);
    }

    protected function retryEmail(NotificationOutbox $outbox): void
    {
        if (!$outbox->recipient) {
            throw new \RuntimeException('Recipient email is missing for this outbox entry.');
        }

        if ($outbox->related_type === Order::class && $outbox->related_id) {
            $order = Order::with('items')->find($outbox->related_id);

            if (!$order) {
                throw new \RuntimeException('The related order could not be found.');
            }

            $payload = $outbox->payload ?? [];
            $stage = $payload['stage'] ?? 'status_update';
            $message = $payload['message'] ?? null;
            $order->sendCustomerProgressEmail($stage, $message, $outbox->id);

            return;
        }

        if ($outbox->related_type === Invoice::class && $outbox->related_id) {
            $invoice = Invoice::find($outbox->related_id);

            if (!$invoice) {
                throw new \RuntimeException('The related invoice could not be found.');
            }

            Mail::to($invoice->customer_email)->send(new InvoiceMail($invoice));
            $invoice->update(['email_sent_at' => now()]);
            $outbox->update([
                'status' => 'sent',
                'sent_at' => now(),
                'last_attempt_at' => now(),
                'failed_at' => null,
                'failure_message' => null,
            ]);

            return;
        }

        throw new \RuntimeException('Retry is not configured for this notification type yet.');
    }

    protected function getFilteredQuery()
    {
        return NotificationOutbox::query()
            ->when($this->search, function ($query) {
                $query->where(function ($nested) {
                    $nested->where('recipient', 'like', '%' . $this->search . '%')
                        ->orWhere('subject', 'like', '%' . $this->search . '%')
                        ->orWhere('related_type', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->channelFilter, fn ($query) => $query->where('channel', $this->channelFilter))
            ->when($this->statusFilter, fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($query) => $query->whereDate('created_at', '<=', $this->dateTo));
    }

    public function render()
    {
        $notifications = $this->getFilteredQuery()
            ->latest()
            ->paginate($this->perPage);

        $selectedOutbox = $this->selectedOutboxId
            ? NotificationOutbox::find($this->selectedOutboxId)
            : null;

        return view('livewire.admin.notification-outbox-manager', [
            'notifications' => $notifications,
            'selectedOutbox' => $selectedOutbox,
            'stats' => $this->stats,
            'analytics' => $this->analytics,
        ]);
    }
}
