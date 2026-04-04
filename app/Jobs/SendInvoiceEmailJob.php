<?php

namespace App\Jobs;

use App\Jobs\Concerns\InitializesTenantContext;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\NotificationOutbox;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendInvoiceEmailJob implements ShouldQueue
{
    use Dispatchable, InitializesTenantContext, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $invoiceId,
        public ?int $outboxId = null,
        public ?int $tenantId = null
    ) {
    }

    public function handle(): void
    {
        $this->initializeTenantContext($this->tenantId);

        $invoice = Invoice::find($this->invoiceId);
        $outbox = $this->outboxId ? NotificationOutbox::find($this->outboxId) : null;

        if (!$invoice || !$invoice->customer_email) {
            $this->markOutboxAsFailed($outbox, 'The invoice or customer email could not be found.');

            return;
        }

        Mail::to($invoice->customer_email)->send(new InvoiceMail($invoice));

        $invoice->update(['email_sent_at' => now()]);
        $this->markOutboxAsSent($outbox);
    }

    public function failed(\Throwable $exception): void
    {
        $this->initializeTenantContext($this->tenantId);

        $outbox = $this->outboxId ? NotificationOutbox::find($this->outboxId) : null;

        $this->markOutboxAsFailed($outbox, $exception->getMessage());
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
