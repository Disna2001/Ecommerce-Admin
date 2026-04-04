<div class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
    <div class="absolute inset-0" wire:click="$set('showDetail', false)"></div>
    <div class="relative z-10 flex max-h-[92vh] w-full max-w-6xl flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-slate-50 shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-white px-6 py-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Order Detail</p>
                <h3 class="mt-2 text-2xl font-bold text-slate-900">{{ $viewingOrder->order_number }}</h3>
                <p class="mt-2 text-sm text-slate-500">{{ $viewingOrder->created_at->format('F d, Y \a\t H:i') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold" style="background:{{ $viewingOrder->status_bg }}; color:{{ $viewingOrder->status_color }};">{{ $viewingOrder->status_label }}</span>
                <button wire:click="$set('showDetail', false)" class="rounded-full border border-slate-200 bg-slate-50 p-3 text-slate-500 transition hover:bg-slate-100"><i class="fas fa-xmark"></i></button>
            </div>
        </div>

        <div class="grid flex-1 gap-6 overflow-y-auto p-6 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="space-y-6">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Customer</p>
                        <p class="mt-3 text-sm font-semibold text-slate-900">{{ $viewingOrder->customer_name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $viewingOrder->customer_email }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $viewingOrder->customer_phone ?: 'No phone provided' }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Shipping</p>
                        <p class="mt-3 text-sm text-slate-700">{{ $viewingOrder->shipping_address }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $viewingOrder->shipping_city }} {{ $viewingOrder->shipping_postal_code }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $viewingOrder->shipping_country }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Payment</p>
                        <p class="mt-3 text-sm font-semibold text-slate-900">{{ ucfirst($viewingOrder->payment_method) }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ ucfirst($viewingOrder->payment_status) }}</p>
                        @if($viewingOrder->payment_review_status)
                            <p class="mt-2 text-xs text-slate-400">{{ ucwords(str_replace('_', ' ', $viewingOrder->payment_review_status)) }}</p>
                        @endif
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Tracking</p>
                        <p class="mt-3 text-sm font-semibold text-slate-900">{{ $viewingOrder->tracking_number ?: 'Not added yet' }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $viewingOrder->courier ?: 'No courier selected' }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total</p>
                        <p class="mt-3 text-lg font-bold text-slate-900">Rs {{ number_format($viewingOrder->total, 2) }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $viewingOrder->items->count() }} order line(s)</p>
                    </div>
                </div>

                @if($viewingOrder->payment_proof_path || $viewingOrder->payment_review_note)
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-600">Payment Verification</p>
                        @if($viewingOrder->payment_proof_path)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($viewingOrder->payment_proof_path) }}" target="_blank" class="mt-3 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100"><i class="fas fa-image"></i><span>Open payment proof</span></a>
                        @endif
                        @if($viewingOrder->payment_review_note)
                            <p class="mt-3 text-sm leading-6 text-emerald-700">{{ $viewingOrder->payment_review_note }}</p>
                        @endif
                    </div>
                @endif

                @if($viewingOrder->return_reason || $viewingOrder->return_notes)
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-600">Return Information</p>
                        @if($viewingOrder->return_reason)
                            <p class="mt-3 text-sm font-semibold text-amber-900">{{ $viewingOrder->return_reason }}</p>
                        @endif
                        @if($viewingOrder->return_notes)
                            <p class="mt-2 text-sm leading-6 text-amber-700">{{ $viewingOrder->return_notes }}</p>
                        @endif
                    </div>
                @endif

                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Order Items</p>
                    <div class="mt-4 space-y-3">
                        @foreach($viewingOrder->items as $item)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $item->product_name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $item->product_sku ?: 'No SKU snapshot' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-slate-900">Rs {{ number_format($item->subtotal, 2) }}</p>
                                        <p class="mt-1 text-xs text-slate-400">{{ $item->quantity }} x Rs {{ number_format($item->sale_price, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Timeline</p>
                    <div class="mt-4 space-y-4">
                        @foreach($viewingOrder->statusHistory as $history)
                            <div class="flex gap-4">
                                <div class="mt-1 h-3 w-3 rounded-full bg-indigo-500"></div>
                                <div class="flex-1 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="text-sm font-semibold text-slate-900">{{ \App\Models\Order::STATUSES[$history->status]['label'] ?? ucfirst($history->status) }}</p>
                                        <p class="text-xs text-slate-400">{{ $history->created_at->format('M d, H:i') }}</p>
                                    </div>
                                    @if($history->note)
                                        <p class="mt-2 text-sm text-slate-500">{{ $history->note }}</p>
                                    @endif
                                    @if($history->changedBy)
                                        <p class="mt-2 text-xs font-medium text-slate-400">by {{ $history->changedBy->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Actions</p>
                    <div class="mt-4 grid gap-3">
                        <button wire:click="openStatusModal({{ $viewingOrder->id }}); $set('showDetail', false)" class="rounded-2xl border border-violet-200 bg-white px-4 py-3 text-left text-sm font-semibold text-violet-700 transition hover:bg-violet-50"><i class="fas fa-arrows-rotate mr-2"></i>Update order status</button>
                        <button wire:click="openTrackingModal({{ $viewingOrder->id }}); $set('showDetail', false)" class="rounded-2xl border border-sky-200 bg-white px-4 py-3 text-left text-sm font-semibold text-sky-700 transition hover:bg-sky-50"><i class="fas fa-truck mr-2"></i>Add or edit tracking</button>
                        @if($viewingOrder->needsPaymentVerification())
                            <button wire:click="openPaymentModal({{ $viewingOrder->id }}); $set('showDetail', false)" class="rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-left text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50"><i class="fas fa-shield-check mr-2"></i>Review payment proof</button>
                        @endif
                        @if($viewingOrder->isReturnPending())
                            <button wire:click="openReturnModal({{ $viewingOrder->id }}); $set('showDetail', false)" class="rounded-2xl border border-amber-200 bg-white px-4 py-3 text-left text-sm font-semibold text-amber-700 transition hover:bg-amber-50"><i class="fas fa-rotate-left mr-2"></i>Handle return request</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
