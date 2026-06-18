<?php

namespace App\Services\Notifications;

use App\Jobs\SendInvoiceEmailJob;
use App\Jobs\SendOrderStatusEmailJob;
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

        SendOrderStatusEmailJob::dispatch(
            $order->id,
            $stage,
            $message,
            $emailOutbox->id,
            $order->tenant_id
        );

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

        if (\App\Models\SiteSetting::get('onesignal_enabled', false)) {
            $pushOutbox = NotificationOutbox::create([
                'channel' => 'push',
                'recipient' => $order->user_id ? 'customer_' . $order->user_id : $order->customer_email,
                'subject' => 'Order status update - ' . $order->order_number,
                'status' => 'queued',
                'provider' => 'onesignal',
                'related_type' => Order::class,
                'related_id' => $order->id,
                'payload' => ['stage' => $stage, 'message' => $message],
                'attempt_count' => 1,
                'last_attempt_at' => now(),
                'queued_at' => now(),
            ]);

            \App\Jobs\SendOneSignalNotificationJob::dispatch(
                $order->id,
                'order_status',
                $stage,
                $message,
                $pushOutbox->id,
                $order->tenant_id
            );
        }
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

        SendInvoiceEmailJob::dispatch($invoice->id, $outbox->id, $invoice->tenant_id);

        return true;
    }

    public function sendAdminOrderNotification(Order $order): void
    {
        $adminEmail = \App\Models\SiteSetting::get('order_notification_email');

        if (!$adminEmail) {
            try {
                $adminEmail = \App\Models\User::role('Admin')->first()?->email;
            } catch (\Throwable $e) {
                $adminEmail = null;
            }
        }

        if (!$adminEmail) {
            $adminEmail = \App\Models\User::where('user_type', 'admin')->first()?->email
                ?: config('mail.from.address');
        }

        if (!$adminEmail) {
            return;
        }

        $outbox = NotificationOutbox::create([
            'channel' => 'email',
            'recipient' => $adminEmail,
            'subject' => '[New Order Received] - ' . $order->order_number,
            'status' => 'queued',
            'provider' => config('mail.default'),
            'related_type' => Order::class,
            'related_id' => $order->id,
            'payload' => ['admin_notification' => true],
            'attempt_count' => 1,
            'last_attempt_at' => now(),
            'queued_at' => now(),
        ]);

        \App\Jobs\SendAdminOrderNotificationJob::dispatch(
            $order->id,
            $outbox->id,
            $order->tenant_id
        );

        if (\App\Models\SiteSetting::get('onesignal_enabled', false)) {
            $pushOutbox = NotificationOutbox::create([
                'channel' => 'push',
                'recipient' => 'admins',
                'subject' => '[New Order Received] - ' . $order->order_number,
                'status' => 'queued',
                'provider' => 'onesignal',
                'related_type' => Order::class,
                'related_id' => $order->id,
                'payload' => ['admin_notification' => true],
                'attempt_count' => 1,
                'last_attempt_at' => now(),
                'queued_at' => now(),
            ]);

            \App\Jobs\SendOneSignalNotificationJob::dispatch(
                $order->id,
                'admin_new_order',
                null,
                null,
                $pushOutbox->id,
                $order->tenant_id
            );
        }
    }
}
