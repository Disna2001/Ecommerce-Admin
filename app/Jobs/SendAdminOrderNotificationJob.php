<?php

namespace App\Jobs;

use App\Jobs\Concerns\InitializesTenantContext;
use App\Mail\AdminOrderReceivedMail;
use App\Models\NotificationOutbox;
use App\Models\Order;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAdminOrderNotificationJob implements ShouldQueue
{
    use Dispatchable, InitializesTenantContext, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $orderId,
        public ?int $outboxId = null,
        public ?int $tenantId = null
    ) {
        $this->afterCommit = true;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->initializeTenantContext($this->tenantId);

        $order = Order::with('items')->find($this->orderId);
        $outbox = $this->outboxId ? NotificationOutbox::find($this->outboxId) : null;

        if (!$order) {
            $this->markOutboxAsFailed($outbox, 'The order could not be found.');
            return;
        }

        $adminEmail = SiteSetting::get('order_notification_email');

        if (!$adminEmail) {
            try {
                $adminEmail = User::role('Admin')->first()?->email;
            } catch (\Throwable $e) {
                $adminEmail = null;
            }
        }

        if (!$adminEmail) {
            $adminEmail = User::where('user_type', 'admin')->first()?->email
                ?: config('mail.from.address');
        }

        if (!$adminEmail) {
            $this->markOutboxAsFailed($outbox, 'No admin notification email could be resolved.');
            return;
        }

        try {
            Mail::to($adminEmail)->send(new AdminOrderReceivedMail($order));
            $this->markOutboxAsSent($outbox);
        } catch (\Throwable $e) {
            $this->markOutboxAsFailed($outbox, $e->getMessage());
            throw $e;
        }
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
}
