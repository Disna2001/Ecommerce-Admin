<?php

namespace App\Services\Notifications;

use App\Jobs\SendWhatsAppNotificationJob;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\NotificationOutbox;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

class CustomerNotificationService
{
    public function sendOrderUpdate(Order $order, string $stage, ?string $message = null): void
    {
        $emailOutbox = NotificationOutbox::create([
            'channel' => 'email',
            'recipient' => $order->customer_email,
            'subject' => 'Order update - ' . $order->order_number,
            'status' => 'queued',
            'provider' => config('mail.default'),
            'related_type' => Order::class,
            'related_id' => $order->id,
            'payload' => ['stage' => $stage, 'message' => $message],
            'attempt_count' => 1,
            'last_attempt_at' => now(),
            'queued_at' => now(),
        ]);

        $order->sendCustomerProgressEmail($stage, $message, $emailOutbox->id);

        $whatsAppOutbox = NotificationOutbox::create([
            'channel' => 'whatsapp',
            'recipient' => $order->customer_phone,
            'subject' => 'Order WhatsApp update - ' . $order->order_number,
            'status' => 'queued',
            'provider' => \App\Models\SiteSetting::get('whatsapp_provider', 'custom'),
            'related_type' => Order::class,
            'related_id' => $order->id,
            'payload' => ['stage' => $stage, 'message' => $message],
            'attempt_count' => 1,
            'last_attempt_at' => now(),
            'queued_at' => now(),
        ]);

        SendWhatsAppNotificationJob::dispatch($order->id, $stage, $message, $whatsAppOutbox->id, $order->tenant_id);
    }

    public function sendInvoice(Invoice $invoice): bool
    {
        if (!$invoice->customer_email) {
            return false;
        }

        $outbox = NotificationOutbox::create([
            'channel' => 'email',
            'recipient' => $invoice->customer_email,
            'subject' => 'Invoice #' . $invoice->invoice_number,
            'status' => 'queued',
            'provider' => config('mail.default'),
            'related_type' => Invoice::class,
            'related_id' => $invoice->id,
            'payload' => ['invoice_number' => $invoice->invoice_number],
            'attempt_count' => 1,
            'last_attempt_at' => now(),
            'queued_at' => now(),
        ]);

        try {
            Mail::to($invoice->customer_email)->send(new InvoiceMail($invoice));

            $invoice->update(['email_sent_at' => now()]);
            $outbox->update([
                'status' => 'sent',
                'sent_at' => now(),
                'last_attempt_at' => now(),
                'failed_at' => null,
                'failure_message' => null,
            ]);

            return true;
        } catch (\Throwable $e) {
            $outbox->update([
                'status' => 'failed',
                'failed_at' => now(),
                'failure_message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
