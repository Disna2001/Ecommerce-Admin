<?php

namespace App\Services\Notifications;

use App\Models\NotificationOutbox;
use App\Models\Order;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneSignalService
{
    public function sendOrderUpdate(Order $order, string $stage, ?string $message = null, ?int $outboxId = null): void
    {
        $enabled = SiteSetting::get('onesignal_enabled', false);
        $appId = SiteSetting::get('onesignal_app_id');
        $restKey = SiteSetting::get('onesignal_rest_api_key');

        $outbox = $outboxId ? NotificationOutbox::find($outboxId) : null;

        if (!$enabled || !$appId || !$restKey) {
            $this->markOutboxAsFailed($outbox, 'OneSignal integration is not enabled or missing credentials.');
            return;
        }

        if (!$order->user_id) {
            $this->markOutboxAsFailed($outbox, 'Order does not have a user ID associated for push notification.');
            return;
        }

        $statusLabel = $order->status_label;
        $title = "Order #{$order->order_number} Update";
        $content = $message ?: "Your order status has been updated to {$statusLabel}.";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $restKey,
                'Content-Type' => 'application/json',
            ])->post('https://onesignal.com/api/v1/notifications', [
                'app_id' => $appId,
                'include_aliases' => [
                    'external_id' => ["customer_" . $order->user_id]
                ],
                'target_channel' => 'push',
                'headings' => [
                    'en' => $title
                ],
                'contents' => [
                    'en' => $content
                ],
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'type' => 'order_status'
                ]
            ]);

            if ($response->successful()) {
                $this->markOutboxAsSent($outbox);
            } else {
                $this->markOutboxAsFailed($outbox, 'OneSignal API Error: ' . $response->body());
            }
        } catch (\Throwable $e) {
            $this->markOutboxAsFailed($outbox, $e->getMessage());
            Log::error('OneSignal Order Status Send Failure: ' . $e->getMessage());
        }
    }

    public function sendAdminOrderNotification(Order $order, ?int $outboxId = null): void
    {
        $enabled = SiteSetting::get('onesignal_enabled', false);
        $appId = SiteSetting::get('onesignal_app_id');
        $restKey = SiteSetting::get('onesignal_rest_api_key');

        $outbox = $outboxId ? NotificationOutbox::find($outboxId) : null;

        if (!$enabled || !$appId || !$restKey) {
            $this->markOutboxAsFailed($outbox, 'OneSignal integration is not enabled or missing credentials.');
            return;
        }

        $currency = $order->currency_symbol ?: 'Rs';
        $title = "New Order Received!";
        $content = "Order #{$order->order_number} has been placed. Total: {$currency} {$order->total}.";

        try {
            // Get admin user IDs
            $adminIds = User::where('user_type', 'admin')->pluck('id')->toArray();
            if (empty($adminIds)) {
                // Fallback to first user
                $firstUser = User::first();
                if ($firstUser) {
                    $adminIds[] = $firstUser->id;
                }
            }

            $externalIds = array_map(fn($id) => "admin_" . $id, $adminIds);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $restKey,
                'Content-Type' => 'application/json',
            ])->post('https://onesignal.com/api/v1/notifications', [
                'app_id' => $appId,
                'include_aliases' => [
                    'external_id' => $externalIds
                ],
                'target_channel' => 'push',
                'headings' => [
                    'en' => $title
                ],
                'contents' => [
                    'en' => $content
                ],
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'type' => 'new_order'
                ]
            ]);

            if ($response->successful()) {
                $this->markOutboxAsSent($outbox);
            } else {
                $this->markOutboxAsFailed($outbox, 'OneSignal Admin Alert API Error: ' . $response->body());
            }
        } catch (\Throwable $e) {
            $this->markOutboxAsFailed($outbox, $e->getMessage());
            Log::error('OneSignal Admin Notification Send Failure: ' . $e->getMessage());
        }
    }

    protected function markOutboxAsSent(?NotificationOutbox $outbox): void
    {
        if ($outbox) {
            $outbox->update([
                'status' => 'sent',
                'sent_at' => now(),
                'last_attempt_at' => now(),
                'failed_at' => null,
                'failure_message' => null,
            ]);
        }
    }

    protected function markOutboxAsFailed(?NotificationOutbox $outbox, string $message): void
    {
        if ($outbox) {
            $outbox->update([
                'status' => 'failed',
                'last_attempt_at' => now(),
                'failed_at' => now(),
                'failure_message' => $message,
            ]);
        }
    }
}
