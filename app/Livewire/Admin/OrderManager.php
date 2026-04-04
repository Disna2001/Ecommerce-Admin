<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Services\Notifications\CustomerNotificationService;
use App\Services\Orders\OrderWorkflowService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class OrderManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterPayment = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $sortField = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    public ?int $viewingOrderId = null;
    public bool $showDetail = false;
    public ?string $focusOrder = null;

    public bool $showStatusModal = false;
    public ?int $updatingOrderId = null;
    public string $newStatus = '';
    public string $statusNote = '';

    public bool $showTrackingModal = false;
    public ?int $trackingOrderId = null;
    public string $trackingNumber = '';
    public string $courier = '';
    public string $trackingUrl = '';

    public bool $showPaymentModal = false;
    public ?int $paymentOrderId = null;
    public string $paymentDecision = 'approve';
    public string $paymentReviewNote = '';

    public bool $showReturnModal = false;
    public ?int $returnOrderId = null;
    public string $returnAction = 'approve';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterPayment' => ['except' => ''],
        'focusOrder' => ['except' => null],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterPayment(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        if ($this->focusOrder && ctype_digit((string) $this->focusOrder)) {
            $this->viewOrder((int) $this->focusOrder);
        }
    }

    public function getStatsProperty(): array
    {
        return [
            'total' => Order::count(),
            'today' => Order::whereDate('created_at', today())->count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::whereIn('status', ['confirmed', 'processing'])->count(),
            'payment_reviews' => Order::where('payment_review_status', 'pending_review')->count(),
            'awaiting_tracking' => Order::whereIn('status', ['confirmed', 'processing'])
                ->whereNull('tracking_number')
                ->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'completed' => Order::whereIn('status', ['completed', 'delivered'])->count(),
            'returns' => Order::whereIn('status', ['return_requested', 'return_approved', 'returned'])->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'revenue' => Order::whereIn('status', ['completed', 'delivered'])->sum('total'),
        ];
    }

    public function getAttentionQueuesProperty(): array
    {
        return [
            [
                'label' => 'Payment Review Queue',
                'count' => Order::where('payment_review_status', 'pending_review')->count(),
                'description' => 'Orders waiting for proof verification.',
                'icon' => 'fa-money-check-dollar',
                'tone' => 'emerald',
                'action' => 'focusPaymentReviews',
            ],
            [
                'label' => 'Return Requests',
                'count' => Order::where('status', 'return_requested')->count(),
                'description' => 'Customers waiting for a return decision.',
                'icon' => 'fa-rotate-left',
                'tone' => 'amber',
                'action' => 'focusReturns',
            ],
            [
                'label' => 'Ready To Ship',
                'count' => Order::whereIn('status', ['confirmed', 'processing'])->whereNull('tracking_number')->count(),
                'description' => 'Confirmed orders without tracking details yet.',
                'icon' => 'fa-truck-fast',
                'tone' => 'sky',
                'action' => 'focusReadyToShip',
            ],
            [
                'label' => 'Cancelled Today',
                'count' => Order::where('status', 'cancelled')->whereDate('updated_at', today())->count(),
                'description' => 'Recent cancellations to double-check.',
                'icon' => 'fa-ban',
                'tone' => 'rose',
                'action' => 'focusCancelled',
            ],
        ];
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filterStatus', 'filterPayment', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function focusPaymentReviews(): void
    {
        $this->filterStatus = '';
        $this->filterPayment = '';
        $this->resetPage();
    }

    public function focusReturns(): void
    {
        $this->filterStatus = 'return_requested';
        $this->resetPage();
    }

    public function focusReadyToShip(): void
    {
        $this->filterStatus = 'confirmed';
        $this->resetPage();
    }

    public function focusCancelled(): void
    {
        $this->filterStatus = 'cancelled';
        $this->resetPage();
    }

    public function viewOrder(int $id): void
    {
        $this->viewingOrderId = $id;
        $this->showDetail = true;
        $this->focusOrder = (string) $id;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->focusOrder = null;
    }

    public function closeStatusModal(): void
    {
        $this->showStatusModal = false;
        $this->updatingOrderId = null;
        $this->newStatus = '';
        $this->statusNote = '';
    }

    public function closeTrackingModal(): void
    {
        $this->showTrackingModal = false;
        $this->trackingOrderId = null;
        $this->trackingNumber = '';
        $this->courier = '';
        $this->trackingUrl = '';
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->paymentOrderId = null;
        $this->paymentDecision = 'approve';
        $this->paymentReviewNote = '';
    }

    public function closeReturnModal(): void
    {
        $this->showReturnModal = false;
        $this->returnOrderId = null;
        $this->returnAction = 'approve';
    }

    public function openStatusModal(int $id): void
    {
        $order = Order::findOrFail($id);
        $this->updatingOrderId = $id;
        $this->newStatus = $order->status;
        $this->statusNote = '';
        $this->showStatusModal = true;
    }

    public function openTrackingModal(int $id): void
    {
        $order = Order::findOrFail($id);
        $this->trackingOrderId = $id;
        $this->trackingNumber = $order->tracking_number ?? '';
        $this->courier = $order->courier ?? '';
        $this->trackingUrl = $order->tracking_url ?? '';
        $this->showTrackingModal = true;
    }

    public function openReturnModal(int $id): void
    {
        $this->returnOrderId = $id;
        $this->returnAction = 'approve';
        $this->showReturnModal = true;
    }

    public function openPaymentModal(int $id): void
    {
        $order = Order::findOrFail($id);
        $this->paymentOrderId = $id;
        $this->paymentDecision = $order->payment_review_status === 'rejected' ? 'reject' : 'approve';
        $this->paymentReviewNote = $order->payment_review_note ?? '';
        $this->showPaymentModal = true;
    }

    public function updateStatus(
        OrderWorkflowService $orderWorkflowService,
        CustomerNotificationService $customerNotificationService
    ): void {
        $order = Order::findOrFail($this->updatingOrderId);
        $oldStatus = $order->status;

        $order = $orderWorkflowService->updateStatus($order, $this->newStatus, $this->statusNote, auth()->id());

        $customerNotificationService->sendOrderUpdate(
            $order,
            $this->newStatus,
            $this->statusNote ?: 'Your order status changed from ' . ucfirst($oldStatus) . ' to ' . $order->status_label . '.'
        );

        $this->closeStatusModal();
        $this->dispatch('notify', type: 'success', message: 'Order status updated.');
    }

    public function saveTracking(
        OrderWorkflowService $orderWorkflowService,
        CustomerNotificationService $customerNotificationService
    ): void {
        $this->validate([
            'trackingNumber' => 'required|string|max:100',
            'courier' => 'nullable|string|max:100',
            'trackingUrl' => 'nullable|url',
        ]);

        $order = Order::findOrFail($this->trackingOrderId);
        $order = $orderWorkflowService->saveTracking($order, [
            'tracking_number' => $this->trackingNumber,
            'courier' => $this->courier,
            'tracking_url' => $this->trackingUrl,
        ], auth()->id());

        $customerNotificationService->sendOrderUpdate(
            $order,
            'shipped',
            'Your order has been shipped' . ($this->courier ? ' via ' . $this->courier : '') . '. Tracking number: ' . $this->trackingNumber . '.'
        );

        $this->closeTrackingModal();
        $this->dispatch('notify', type: 'success', message: 'Tracking info saved and order marked as shipped.');
    }

    public function verifyPayment(
        OrderWorkflowService $orderWorkflowService,
        CustomerNotificationService $customerNotificationService
    ): void {
        $this->validate([
            'paymentDecision' => 'required|in:approve,reject',
            'paymentReviewNote' => 'nullable|string|max:1000',
        ]);

        $order = Order::findOrFail($this->paymentOrderId);
        $order = $orderWorkflowService->verifyPayment($order, $this->paymentDecision, $this->paymentReviewNote, auth()->id());

        $customerNotificationService->sendOrderUpdate(
            $order,
            $this->paymentDecision === 'approve' ? 'payment_approved' : 'payment_rejected',
            $this->paymentReviewNote ?: ($this->paymentDecision === 'approve'
                ? 'Your payment was approved and your order is now cleared to continue.'
                : 'We could not verify the payment proof submitted for this order. Please review the payment details and submit a corrected proof if needed.')
        );

        $this->closePaymentModal();
        $this->dispatch('notify', type: 'success', message: $this->paymentDecision === 'approve' ? 'Payment approved.' : 'Payment marked for correction.');
    }

    public function handleReturn(
        OrderWorkflowService $orderWorkflowService,
        CustomerNotificationService $customerNotificationService
    ): void {
        $order = Order::findOrFail($this->returnOrderId);
        $order = $orderWorkflowService->handleReturn($order, $this->returnAction, auth()->id());

        $resolvedMessage = match ($this->returnAction) {
            'approve' => 'Return request approved.',
            'reject' => 'Return request rejected.',
            'refund' => 'Order refunded to customer.',
            default => 'Return request approved.',
        };

        $customerNotificationService->sendOrderUpdate($order, $order->status, $resolvedMessage);

        $this->closeReturnModal();
        $this->dispatch('notify', type: 'success', message: 'Return request updated.');
    }

    public function cancelOrder(
        int $id,
        OrderWorkflowService $orderWorkflowService,
        CustomerNotificationService $customerNotificationService
    ): void {
        $order = Order::findOrFail($id);

        if (!$order->canBeCancelled()) {
            $this->dispatch('notify', type: 'error', message: 'This order cannot be cancelled.');

            return;
        }

        $order = $orderWorkflowService->updateStatus($order, 'cancelled', 'Cancelled by admin.', auth()->id());
        $customerNotificationService->sendOrderUpdate(
            $order,
            'cancelled',
            'Your order was cancelled by our support team. If you need assistance, please reply to this email.'
        );

        $this->dispatch('notify', type: 'info', message: 'Order cancelled.');
    }

    public function sortBy(string $field): void
    {
        $this->sortDir = $this->sortField === $field && $this->sortDir === 'asc' ? 'desc' : 'asc';
        $this->sortField = $field;
    }

    public function render()
    {
        $orders = Order::with(['user', 'items'])
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $searchQuery) {
                    $searchQuery->where('order_number', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_email', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, fn (Builder $query) => $query->where('status', $this->filterStatus))
            ->when($this->filterPayment, fn (Builder $query) => $query->where('payment_status', $this->filterPayment))
            ->when($this->dateFrom, fn (Builder $query) => $query->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn (Builder $query) => $query->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        $viewingOrder = $this->viewingOrderId
            ? Order::with(['items.stock', 'statusHistory.changedBy', 'user'])->find($this->viewingOrderId)
            : null;

        $paymentReviewOrder = $this->paymentOrderId ? Order::find($this->paymentOrderId) : null;

        $recentQueue = Order::query()
            ->select(['id', 'order_number', 'customer_name', 'status', 'payment_status', 'created_at', 'total'])
            ->whereIn('status', ['pending', 'confirmed', 'processing', 'return_requested'])
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.admin.order-manager', [
            'orders' => $orders,
            'viewingOrder' => $viewingOrder,
            'paymentReviewOrder' => $paymentReviewOrder,
            'recentQueue' => $recentQueue,
        ]);
    }
}
