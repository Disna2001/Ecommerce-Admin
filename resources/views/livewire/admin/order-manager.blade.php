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

    <div class="rounded-[1.9rem] bg-gradient-to-r from-slate-900 via-indigo-900 to-sky-700 p-6 text-white shadow-xl">
        <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/60">Order Operations</p>
                <h2 class="mt-3 text-3xl font-black">Manage payments, dispatch, and returns from one structured workspace.</h2>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-white/75">
                    This screen is rebuilt for operations work first: attention queues, cleaner filters, and detail panels that help the team move faster with less scrolling.
                </p>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.18em] text-white/60">Today</p>
                    <p class="mt-2 text-2xl font-black">{{ $this->stats['today'] }}</p>
                    <p class="mt-1 text-xs text-white/60">Orders created</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.18em] text-white/60">Payment Review</p>
                    <p class="mt-2 text-2xl font-black">{{ $this->stats['payment_reviews'] }}</p>
                    <p class="mt-1 text-xs text-white/60">Awaiting verification</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.18em] text-white/60">Awaiting Tracking</p>
                    <p class="mt-2 text-2xl font-black">{{ $this->stats['awaiting_tracking'] }}</p>
                    <p class="mt-1 text-xs text-white/60">Confirmed but not shipped</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.18em] text-white/60">Open Orders</p>
                    <p class="mt-2 text-2xl font-black">{{ $this->stats['pending'] + $this->stats['processing'] + $this->stats['shipped'] }}</p>
                    <p class="mt-1 text-xs text-white/60">Still active</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.18em] text-white/60">Returns</p>
                    <p class="mt-2 text-2xl font-black">{{ $this->stats['returns'] }}</p>
                    <p class="mt-1 text-xs text-white/60">Exception flow</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.18em] text-white/60">Revenue</p>
                    <p class="mt-2 text-2xl font-black">Rs {{ number_format($this->stats['revenue'], 0) }}</p>
                    <p class="mt-1 text-xs text-white/60">Completed + delivered</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <div class="space-y-6">
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-slate-900">Attention Queues</h3>
                        <p class="mt-1 text-sm text-slate-500">Start with the highest-signal items before routine browsing.</p>
                    </div>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($this->attentionQueues as $queue)
                        <button wire:click="{{ $queue['action'] }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-white">
                            <div class="flex items-start gap-4">
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl {{ $queue['tone'] === 'emerald' ? 'bg-emerald-100 text-emerald-600' : ($queue['tone'] === 'amber' ? 'bg-amber-100 text-amber-600' : ($queue['tone'] === 'sky' ? 'bg-sky-100 text-sky-600' : 'bg-rose-100 text-rose-600')) }}">
                                    <i class="fas {{ $queue['icon'] }}"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $queue['label'] }}</p>
                                    <p class="mt-2 text-3xl font-black text-slate-900">{{ $queue['count'] }}</p>
                                    <p class="mt-2 text-xs leading-6 text-slate-500">{{ $queue['description'] }}</p>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                        <i class="fas fa-sliders"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-slate-900">Filters</h3>
                        <p class="mt-1 text-sm text-slate-500">Search and narrow the queue without breaking flow.</p>
                    </div>
                </div>
                <div class="grid gap-4 xl:grid-cols-[1.4fr_repeat(4,minmax(0,0.7fr))]">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Search</label>
                        <div class="relative mt-2">
                            <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Order no, name, email, phone..." class="w-full rounded-2xl border-slate-200 pl-11 text-sm shadow-none focus:ring-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Status</label>
                        <select wire:model.live="filterStatus" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                            <option value="">All</option>
                            @foreach(\App\Models\Order::STATUSES as $key => $status)
                                <option value="{{ $key }}">{{ $status['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Payment</label>
                        <select wire:model.live="filterPayment" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                            <option value="">All</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="paid">Paid</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">From</label>
                        <input type="date" wire:model.live="dateFrom" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">To</label>
                        <input type="date" wire:model.live="dateTo" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <div class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600">Results: <span class="font-semibold text-slate-900">{{ $orders->total() }}</span></div>
                    <div class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600">
                        Per page:
                        <select wire:model.live="perPage" class="ml-2 border-0 bg-transparent pr-7 text-sm font-semibold text-slate-900 focus:ring-0">
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                    @if($search || $filterStatus || $filterPayment || $dateFrom || $dateTo)
                        <button wire:click="clearFilters" class="rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-100">
                            <i class="fas fa-xmark mr-2"></i>Clear
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
                        <i class="fas fa-list-check"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-slate-900">Queue Snapshot</h3>
                        <p class="mt-1 text-sm text-slate-500">A short list of active orders likely to need action next.</p>
                    </div>
                </div>
                <div class="mt-5 space-y-3">
                    @forelse($recentQueue as $queueOrder)
                        <button wire:click="viewOrder({{ $queueOrder->id }})" class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:border-slate-300 hover:bg-white">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $queueOrder->order_number }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $queueOrder->customer_name }}</p>
                                    <p class="mt-2 text-xs text-slate-400">{{ $queueOrder->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold" style="background:{{ $queueOrder->status_bg }}; color:{{ $queueOrder->status_color }};">{{ $queueOrder->status_label }}</span>
                                    <p class="mt-2 text-sm font-semibold text-slate-900">Rs {{ number_format($queueOrder->total, 2) }}</p>
                                </div>
                            </div>
                        </button>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-5 text-sm text-slate-500">No active queue items are available.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    <tr>
                        <th class="px-5 py-4"><button wire:click="sortBy('order_number')" class="flex items-center gap-2 hover:text-slate-900">Order <i class="fas fa-sort{{ $sortField==='order_number' ? ($sortDir==='asc' ? '-up' : '-down') : '' }}"></i></button></th>
                        <th class="px-5 py-4"><button wire:click="sortBy('customer_name')" class="flex items-center gap-2 hover:text-slate-900">Customer <i class="fas fa-sort{{ $sortField==='customer_name' ? ($sortDir==='asc' ? '-up' : '-down') : '' }}"></i></button></th>
                        <th class="px-5 py-4"><button wire:click="sortBy('created_at')" class="flex items-center gap-2 hover:text-slate-900">Created <i class="fas fa-sort{{ $sortField==='created_at' ? ($sortDir==='asc' ? '-up' : '-down') : '' }}"></i></button></th>
                        <th class="px-5 py-4"><button wire:click="sortBy('total')" class="flex items-center gap-2 hover:text-slate-900">Total <i class="fas fa-sort{{ $sortField==='total' ? ($sortDir==='asc' ? '-up' : '-down') : '' }}"></i></button></th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Payment</th>
                        <th class="px-5 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($orders as $order)
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="px-5 py-4 align-top">
                                <button wire:click="viewOrder({{ $order->id }})" class="text-left">
                                    <p class="font-mono text-xs font-bold text-indigo-600 hover:text-indigo-800">{{ $order->order_number }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $order->items->count() }} item(s)</p>
                                </button>
                            </td>
                            <td class="px-5 py-4 align-top">
                                <p class="text-sm font-semibold text-slate-900">{{ $order->customer_name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $order->customer_email }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $order->customer_phone ?: 'No phone' }}</p>
                            </td>
                            <td class="px-5 py-4 align-top">
                                <p class="text-sm text-slate-700">{{ $order->created_at->format('M d, Y') }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $order->created_at->format('H:i') }}</p>
                            </td>
                            <td class="px-5 py-4 align-top">
                                <p class="text-sm font-semibold text-slate-900">Rs {{ number_format($order->total, 2) }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ ucfirst($order->payment_method) }}</p>
                            </td>
                            <td class="px-5 py-4 align-top">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold" style="background:{{ $order->status_bg }}; color:{{ $order->status_color }};">{{ $order->status_label }}</span>
                                @if($order->tracking_number)
                                    <p class="mt-2 text-xs text-sky-600">Tracking added</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 align-top">
                                @php $paymentTone = match($order->payment_status) { 'paid' => 'bg-emerald-100 text-emerald-700', 'refunded' => 'bg-violet-100 text-violet-700', default => 'bg-amber-100 text-amber-700' }; @endphp
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $paymentTone }}">{{ ucfirst($order->payment_status) }}</span>
                                @if($order->payment_review_status)
                                    <p class="mt-2 text-xs text-slate-500">{{ ucwords(str_replace('_', ' ', $order->payment_review_status)) }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 align-top">
                                <div class="flex flex-wrap gap-2">
                                    <button wire:click="viewOrder({{ $order->id }})" class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100"><i class="fas fa-eye"></i>View</button>
                                    <button wire:click="openStatusModal({{ $order->id }})" class="inline-flex items-center gap-2 rounded-full border border-violet-200 bg-violet-50 px-3 py-2 text-xs font-semibold text-violet-700 transition hover:bg-violet-100"><i class="fas fa-arrows-rotate"></i>Status</button>
                                    <button wire:click="openTrackingModal({{ $order->id }})" class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700 transition hover:bg-sky-100"><i class="fas fa-truck"></i>Tracking</button>
                                    @if($order->needsPaymentVerification())
                                        <button wire:click="openPaymentModal({{ $order->id }})" class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100"><i class="fas fa-shield-check"></i>Payment</button>
                                    @endif
                                    @if($order->isReturnPending())
                                        <button wire:click="openReturnModal({{ $order->id }})" class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-100"><i class="fas fa-rotate-left"></i>Return</button>
                                    @endif
                                    @if($order->canBeCancelled())
                                        <button wire:click="cancelOrder({{ $order->id }})" onclick="confirm('Cancel order {{ $order->order_number }}?') || event.stopImmediatePropagation()" class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100"><i class="fas fa-ban"></i>Cancel</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-16 text-center text-sm text-slate-500">No orders match the current filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 p-4">{{ $orders->links() }}</div>
    </div>

    @if($showDetail && $viewingOrder)
        <div class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm">
            <div class="max-h-[88vh] w-full max-w-6xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Order Detail</p>
                            <h3 class="mt-2 text-2xl font-bold text-slate-900">{{ $viewingOrder->order_number }}</h3>
                            <p class="mt-2 text-sm text-slate-500">{{ $viewingOrder->created_at->format('F d, Y \a\t H:i') }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold" style="background:{{ $viewingOrder->status_bg }}; color:{{ $viewingOrder->status_color }};">{{ $viewingOrder->status_label }}</span>
                            <button wire:click="closeDetail" class="rounded-full border border-slate-200 bg-white p-3 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"><i class="fas fa-xmark"></i></button>
                        </div>
                    </div>
                </div>

                <div class="grid max-h-[calc(88vh-96px)] gap-6 overflow-y-auto p-6 xl:grid-cols-[1fr_0.9fr]">
                    <div class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Customer</p>
                                <p class="mt-3 text-sm font-semibold text-slate-900">{{ $viewingOrder->customer_name }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $viewingOrder->customer_email }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $viewingOrder->customer_phone ?: 'No phone provided' }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Shipping</p>
                                <p class="mt-3 text-sm text-slate-700">{{ $viewingOrder->shipping_address }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $viewingOrder->shipping_city }} {{ $viewingOrder->shipping_postal_code }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $viewingOrder->shipping_country }}</p>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
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
    @endif

    @if($showStatusModal)
        <div class="fixed inset-0 z-[95] flex items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm">
            <div class="w-full max-w-lg overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                <div class="border-b border-slate-200 px-6 py-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Order Control</p>
                            <h3 class="mt-2 text-xl font-semibold text-slate-900">Update Status</h3>
                        </div>
                        <button wire:click="closeStatusModal" class="rounded-full border border-slate-200 bg-slate-50 p-3 text-slate-500 transition hover:bg-slate-100"><i class="fas fa-xmark"></i></button>
                    </div>
                </div>
                <div class="space-y-4 p-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">New status</label>
                        <select wire:model="newStatus" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                            @foreach(\App\Models\Order::STATUSES as $key => $status)
                                <option value="{{ $key }}">{{ $status['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Operator note</label>
                        <textarea wire:model="statusNote" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none" placeholder="Add context for the timeline and customer notice if needed."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                    <button wire:click="closeStatusModal" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Cancel</button>
                    <button wire:click="updateStatus" class="rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Save status</button>
                </div>
            </div>
        </div>
    @endif

    @if($showTrackingModal)
        <div class="fixed inset-0 z-[95] flex items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm">
            <div class="w-full max-w-xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                <div class="border-b border-slate-200 px-6 py-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Dispatch Update</p>
                            <h3 class="mt-2 text-xl font-semibold text-slate-900">Add Tracking</h3>
                        </div>
                        <button wire:click="closeTrackingModal" class="rounded-full border border-slate-200 bg-slate-50 p-3 text-slate-500 transition hover:bg-slate-100"><i class="fas fa-xmark"></i></button>
                    </div>
                </div>
                <div class="grid gap-4 p-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Tracking number</label>
                        <input type="text" wire:model="trackingNumber" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none" placeholder="EX123456789LK">
                        @error('trackingNumber')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Courier</label>
                        <input type="text" wire:model="courier" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none" placeholder="SL Post, DHL, FedEx">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Tracking URL</label>
                        <input type="url" wire:model="trackingUrl" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none" placeholder="https://tracking.example.com/...">
                        @error('trackingUrl')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>
                    <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm leading-6 text-sky-700">
                        Saving tracking automatically moves the order into the shipped stage and notifies the customer.
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                    <button wire:click="closeTrackingModal" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Cancel</button>
                    <button wire:click="saveTracking" class="rounded-full bg-sky-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-700">Save tracking</button>
                </div>
            </div>
        </div>
    @endif

    @if($showPaymentModal)
        <div class="fixed inset-0 z-[95] flex items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm">
            <div class="w-full max-w-2xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                <div class="border-b border-slate-200 px-6 py-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Payment Control</p>
                            <h3 class="mt-2 text-xl font-semibold text-slate-900">Review Payment</h3>
                        </div>
                        <button wire:click="closePaymentModal" class="rounded-full border border-slate-200 bg-slate-50 p-3 text-slate-500 transition hover:bg-slate-100"><i class="fas fa-xmark"></i></button>
                    </div>
                </div>
                <div class="space-y-5 p-6">
                    @if($paymentReviewOrder)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="grid gap-3 md:grid-cols-2">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Order</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-900">{{ $paymentReviewOrder->order_number }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Reference</p>
                                    <p class="mt-2 font-mono text-sm text-slate-700">{{ $paymentReviewOrder->payment_reference ?: 'No reference' }}</p>
                                </div>
                            </div>
                            @if($paymentReviewOrder->payment_proof_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($paymentReviewOrder->payment_proof_path) }}" target="_blank" class="mt-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50"><i class="fas fa-image"></i><span>Open proof image</span></a>
                            @endif
                        </div>
                    @endif

                    <div class="grid gap-3">
                        @foreach([['approve', 'Approve payment', 'Marks the order as paid and verified.', 'fa-circle-check', 'emerald'], ['reject', 'Reject proof', 'Keeps payment unpaid and asks the customer to correct it.', 'fa-circle-xmark', 'rose']] as [$value, $title, $description, $icon, $tone])
                            <label class="flex cursor-pointer items-start gap-4 rounded-2xl border-2 p-4 transition {{ $paymentDecision === $value ? ($tone === 'emerald' ? 'border-emerald-300 bg-emerald-50' : 'border-rose-300 bg-rose-50') : 'border-slate-200 bg-white' }}">
                                <input type="radio" wire:model.live="paymentDecision" value="{{ $value }}" class="sr-only">
                                <div class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $tone === 'emerald' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }}"><i class="fas {{ $icon }}"></i></div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $title }}</p>
                                    <p class="mt-1 text-xs leading-6 text-slate-500">{{ $description }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Review note</label>
                        <textarea wire:model="paymentReviewNote" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none" placeholder="Add internal and customer-facing context."></textarea>
                        @error('paymentReviewNote')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                    <button wire:click="closePaymentModal" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Cancel</button>
                    <button wire:click="verifyPayment" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Save review</button>
                </div>
            </div>
        </div>
    @endif

    @if($showReturnModal)
        <div class="fixed inset-0 z-[95] flex items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm">
            <div class="w-full max-w-xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                <div class="border-b border-slate-200 px-6 py-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Return Handling</p>
                            <h3 class="mt-2 text-xl font-semibold text-slate-900">Process Return Request</h3>
                        </div>
                        <button wire:click="closeReturnModal" class="rounded-full border border-slate-200 bg-slate-50 p-3 text-slate-500 transition hover:bg-slate-100"><i class="fas fa-xmark"></i></button>
                    </div>
                </div>
                <div class="space-y-3 p-6">
                    @foreach([['approve', 'Approve return', 'Customer can move forward with the return.', 'fa-circle-check', 'emerald'], ['reject', 'Reject return', 'Close the request without a return.', 'fa-circle-xmark', 'rose'], ['refund', 'Approve and refund', 'Restore stock and mark payment as refunded.', 'fa-money-bill-wave', 'violet']] as [$value, $title, $description, $icon, $tone])
                        <label class="flex cursor-pointer items-start gap-4 rounded-2xl border-2 p-4 transition {{ $returnAction === $value ? ($tone === 'emerald' ? 'border-emerald-300 bg-emerald-50' : ($tone === 'rose' ? 'border-rose-300 bg-rose-50' : 'border-violet-300 bg-violet-50')) : 'border-slate-200 bg-white' }}">
                            <input type="radio" wire:model.live="returnAction" value="{{ $value }}" class="sr-only">
                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $tone === 'emerald' ? 'bg-emerald-100 text-emerald-600' : ($tone === 'rose' ? 'bg-rose-100 text-rose-600' : 'bg-violet-100 text-violet-600') }}"><i class="fas {{ $icon }}"></i></div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $title }}</p>
                                <p class="mt-1 text-xs leading-6 text-slate-500">{{ $description }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                    <button wire:click="closeReturnModal" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Cancel</button>
                    <button wire:click="handleReturn" class="rounded-full bg-amber-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-amber-600">Confirm action</button>
                </div>
            </div>
        </div>
    @endif
</div>
