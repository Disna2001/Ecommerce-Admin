<?php

namespace App\Jobs;

use App\Jobs\Concerns\InitializesTenantContext;
use App\Models\Order;
use App\Services\Notifications\WhatsAppNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppNotificationJob implements ShouldQueue
{
    use Dispatchable, InitializesTenantContext, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $orderId,
        public string $stage,
        public ?string $message = null,
        public ?int $outboxId = null,
        public ?int $tenantId = null
    ) {
    }

    public function handle(WhatsAppNotificationService $whatsAppNotificationService): void
    {
        $this->initializeTenantContext($this->tenantId);

        $order = Order::find($this->orderId);

        if (!$order) {
            return;
        }

        $whatsAppNotificationService->sendOrderUpdate($order, $this->stage, $this->message, $this->outboxId);
    }
}
