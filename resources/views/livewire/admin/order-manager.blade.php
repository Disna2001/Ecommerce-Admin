<div class="space-y-6">
    <div x-data="{ show:false, message:'', type:'success' }"
         x-on:notify.window="show=true; message=$event.detail.message; type=$event.detail.type; setTimeout(()=>show=false,3500)"
         x-show="show"
         x-transition
         style="display:none"
         class="fixed bottom-5 right-5 z-[100] rounded-2xl px-5 py-3 text-sm font-semibold text-white shadow-2xl"
         :class="type==='success' ? 'bg-emerald-600' : (type==='error' ? 'bg-rose-600' : 'bg-indigo-600')">
        <div class="flex items-center gap-2">
            <i class="fas" :class="type==='success' ? 'fa-circle-check' : (type==='error' ? 'fa-circle-xmark' : 'fa-circle-info')"></i>
            <span x-text="message"></span>
        </div>
    </div>

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
    <x-admin.orders.table :orders="$orders" />

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
        <x-admin.orders.payment-modal :payment-review-order="$paymentReviewOrder" />
    @endif

    @if($showReturnModal)
        <x-admin.orders.return-modal />
    @endif
</div>
