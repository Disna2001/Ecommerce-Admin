<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Services\AuditLogService;
use App\Services\Inventory\StockMovementService;
use App\Services\Notifications\CustomerNotificationService;
use Illuminate\Support\Facades\DB;

class OrderWorkflowService
{
    public function __construct(
        protected StockMovementService $stockMovementService,
        protected CustomerNotificationService $customerNotificationService,
        protected AuditLogService $auditLogService
    ) {
    }

    public function createOrder(array $orderData, array $cartItems, string $historyNote, ?int $actorId = null): Order
    {
        return DB::transaction(function () use ($orderData, $cartItems, $historyNote, $actorId) {
            $order = Order::create($orderData);

            foreach ($cartItems as $stockId => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'stock_id' => $stockId,
                    'product_name' => $item['name'],
                    'product_sku' => $item['sku'] ?? null,
                    'unit_price' => $item['original_price'] ?? $item['price'],
                    'sale_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'product_snapshot' => $item,
                ]);
            }

            $this->stockMovementService->reduceFromOrderItems(
                collect($cartItems)->map(fn ($item, $stockId) => [
                    'stock_id' => $stockId,
                    'quantity' => $item['quantity'],
                ]),
                'order_checkout',
                [
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'user_id' => $actorId,
                    'notes' => 'Stock reserved via customer checkout.',
                ]
            );

            $this->recordHistory($order, $order->status, $historyNote, $actorId);

            $this->auditLogService->log(
                'order.created',
                $order,
                'Order created through checkout.',
                [
                    'status' => $order->status,
                    'payment_method' => $order->payment_method,
                    'total' => $order->total,
                ],
                $actorId
            );

            return $order->fresh(['items']);
        });
    }

    public function updateStatus(Order $order, string $newStatus, ?string $note = null, ?int $actorId = null): Order
    {
        return DB::transaction(function () use ($order, $newStatus, $note, $actorId) {
            $oldStatus = $order->status;

            $payload = ['status' => $newStatus];

            if ($newStatus === 'completed') {
                $payload['payment_status'] = 'paid';
            }

            if ($newStatus === 'cancelled' && $order->status !== 'cancelled') {
                $this->stockMovementService->restoreFromOrder($order->loadMissing('items'), 'order_cancelled', [
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'user_id' => $actorId,
                    'notes' => 'Stock restored after order cancellation.',
                ]);
            }

            $order->update($payload);

            $historyNote = $note ?: 'Status changed from ' . ucfirst($oldStatus) . ' to ' . ucfirst($newStatus) . '.';
            $this->recordHistory($order, $newStatus, $historyNote, $actorId);

            $this->auditLogService->log(
                'order.status_updated',
                $order,
                $historyNote,
                ['from' => $oldStatus, 'to' => $newStatus],
                $actorId
            );

            return $order->fresh(['items']);
        });
    }

    public function saveTracking(Order $order, array $trackingData, ?int $actorId = null): Order
    {
        return DB::transaction(function () use ($order, $trackingData, $actorId) {
            $order->update([
                'tracking_number' => $trackingData['tracking_number'],
                'courier' => $trackingData['courier'] ?? null,
                'tracking_url' => $trackingData['tracking_url'] ?? null,
                'status' => 'shipped',
            ]);

            $note = 'Tracking added: ' . $trackingData['tracking_number'] . ' via ' . ($trackingData['courier'] ?: 'Unknown');
            $this->recordHistory($order, 'shipped', $note, $actorId);

            $this->auditLogService->log(
                'order.tracking_added',
                $order,
                $note,
                $trackingData,
                $actorId
            );

            return $order->fresh(['items']);
        });
    }

    public function verifyPayment(Order $order, string $decision, ?string $reviewNote = null, ?int $actorId = null): Order
    {
        return DB::transaction(function () use ($order, $decision, $reviewNote, $actorId) {
            $isApproved = $decision === 'approve';

            $order->update([
                'payment_status' => $isApproved ? 'paid' : 'unpaid',
                'payment_review_status' => $isApproved ? 'approved' : 'rejected',
                'payment_review_note' => $reviewNote ?: ($isApproved ? 'Payment proof approved by admin.' : 'Payment proof rejected. Awaiting a corrected submission.'),
                'payment_verified_at' => $isApproved ? now() : null,
                'payment_verified_by' => $actorId,
            ]);

            $note = $isApproved ? 'Payment verified and marked as paid.' : 'Payment proof rejected by admin.';
            $this->recordHistory($order, $order->status, $note, $actorId);

            $this->auditLogService->log(
                'order.payment_reviewed',
                $order,
                $note,
                ['decision' => $decision],
                $actorId
            );

            return $order->fresh(['items']);
        });
    }

    public function handleReturn(Order $order, string $action, ?int $actorId = null): Order
    {
        return DB::transaction(function () use ($order, $action, $actorId) {
            $map = [
                'approve' => ['status' => 'return_approved', 'note' => 'Return request approved.', 'payment_status' => null],
                'reject' => ['status' => 'completed', 'note' => 'Return request rejected.', 'payment_status' => null],
                'refund' => ['status' => 'refunded', 'note' => 'Order refunded to customer.', 'payment_status' => 'refunded'],
            ];

            $resolved = $map[$action] ?? $map['approve'];
            $payload = [
                'status' => $resolved['status'],
                'return_approved_at' => now(),
            ];

            if ($resolved['payment_status']) {
                $payload['payment_status'] = $resolved['payment_status'];
            }

            if ($resolved['status'] === 'refunded') {
                $this->stockMovementService->restoreFromOrder($order->loadMissing('items'), 'order_refunded', [
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'user_id' => $actorId,
                    'notes' => 'Stock restored after refund.',
                ]);
            }

            $order->update($payload);
            $this->recordHistory($order, $resolved['status'], $resolved['note'], $actorId);

            $this->auditLogService->log(
                'order.return_handled',
                $order,
                $resolved['note'],
                ['action' => $action, 'status' => $resolved['status']],
                $actorId
            );

            return $order->fresh(['items']);
        });
    }

    public function syncGatewayPayment(Order $order, string $state, array $gatewayData = [], ?int $actorId = null): Order
    {
        return DB::transaction(function () use ($order, $state, $gatewayData, $actorId) {
            if ($state === 'paid') {
                if ($order->payment_status === 'paid') {
                    return $order->fresh(['items']);
                }

                $nextStatus = $order->status === 'pending' ? 'confirmed' : $order->status;
                $paymentReference = $gatewayData['payment_reference'] ?? $order->payment_reference;
                $reviewNote = $gatewayData['note'] ?? 'Gateway payment confirmed automatically.';

                $order->update([
                    'status' => $nextStatus,
                    'payment_status' => 'paid',
                    'payment_reference' => $paymentReference,
                    'payment_review_status' => 'not_required',
                    'payment_review_note' => $reviewNote,
                    'payment_verified_at' => now(),
                    'payment_verified_by' => $actorId,
                ]);

                $this->recordHistory($order, $nextStatus, $reviewNote, $actorId);

                $this->auditLogService->log(
                    'order.gateway_payment_confirmed',
                    $order,
                    $reviewNote,
                    $gatewayData,
                    $actorId
                );

                return $order->fresh(['items']);
            }

            if ($order->status === 'cancelled') {
                return $order->fresh(['items']);
            }

            if ($order->status !== 'cancelled') {
                $this->stockMovementService->restoreFromOrder($order->loadMissing('items'), 'gateway_payment_failed', [
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'user_id' => $actorId,
                    'notes' => 'Stock restored after gateway payment was not completed.',
                ]);
            }

            $reviewNote = $gatewayData['note'] ?? 'Gateway payment was not completed.';

            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'unpaid',
                'payment_reference' => $gatewayData['payment_reference'] ?? $order->payment_reference,
                'payment_review_status' => 'not_required',
                'payment_review_note' => $reviewNote,
                'payment_verified_at' => null,
                'payment_verified_by' => null,
            ]);

            $this->recordHistory($order, 'cancelled', $reviewNote, $actorId);

            $this->auditLogService->log(
                'order.gateway_payment_failed',
                $order,
                $reviewNote,
                $gatewayData,
                $actorId
            );

            return $order->fresh(['items']);
        });
    }

    protected function recordHistory(Order $order, string $status, string $note, ?int $actorId = null): void
    {
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $status,
            'note' => $note,
            'changed_by' => $actorId,
            'created_at' => now(),
        ]);
    }
}
