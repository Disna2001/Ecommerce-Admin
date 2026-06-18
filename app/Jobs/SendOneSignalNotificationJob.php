<?php

namespace App\Jobs;

use App\Jobs\Concerns\InitializesTenantContext;
use App\Models\Order;
use App\Services\Notifications\OneSignalService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOneSignalNotificationJob implements ShouldQueue
{
    use Dispatchable, InitializesTenantContext, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $orderId,
        public string $type, // 'order_status' or 'admin_new_order'
        public ?string $stage = null,
        public ?string $message = null,
        public ?int $outboxId = null,
        public ?int $tenantId = null
    ) {
    }

    public function handle(OneSignalService $oneSignalService): void
    {
        $this->initializeTenantContext($this->tenantId);

        $order = Order::find($this->orderId);

        if (!$order) {
            return;
        }

        if ($this->type === 'order_status') {
            $oneSignalService->sendOrderUpdate($order, $this->stage ?? $order->status, $this->message, $this->outboxId);
        } else {
            $oneSignalService->sendAdminOrderNotification($order, $this->outboxId);
        }
    }
}
