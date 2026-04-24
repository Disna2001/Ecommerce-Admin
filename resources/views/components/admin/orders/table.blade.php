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
