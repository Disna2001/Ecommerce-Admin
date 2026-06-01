<div class="space-y-6">
    <x-admin.orders.hero />
    <x-admin.orders.overview
        :orders="$orders"
        :recent-queue="$recentQueue"
        :search="$search"
        :filter-status="$filterStatus"
        :filter-payment="$filterPayment"
        :date-from="$dateFrom"
        :date-to="$dateTo"
        :per-page="$perPage"
    />
    <x-admin.orders.table
        :orders="$orders"
        :sort-field="$sortField"
        :sort-dir="$sortDir"
    />

    @if($showDetail && $viewingOrder)
        <x-admin.orders.detail-modal :viewing-order="$viewingOrder" />
    @endif

    @if($showStatusModal)
        <x-admin.orders.status-modal />
    @endif

    @if($showTrackingModal)
        <x-admin.orders.tracking-modal />
    @endif

    @if($showPaymentModal)
        <x-admin.orders.payment-modal :payment-review-order="$paymentReviewOrder" :payment-decision="$paymentDecision" />
    @endif

    @if($showReturnModal)
        <x-admin.orders.return-modal :return-action="$returnAction" />
    @endif
</div>
