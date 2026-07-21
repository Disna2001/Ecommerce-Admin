@props(['orders', 'sortField', 'sortDir'])

<div class="rounded-xl border border-slate-200 bg-white shadow-xs overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-900 text-white shadow-xs">
                <i class="fas fa-list-check text-xs"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900">All Orders</h3>
            </div>
        </div>
    </div>

    <!-- Desktop Fulfillment Table -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                <tr>
                    <th class="px-6 py-3.5">Order</th>
                    <th class="px-6 py-3.5">Total</th>
                    <th class="px-6 py-3.5">Status</th>
                    <th class="px-6 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white text-xs">
                @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 border border-slate-200 shrink-0">
                                    <i class="fas fa-box-archive text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900">#{{ $order->order_number }}</h4>
                                    <p class="text-[10px] font-medium text-slate-400">{{ $order->customer_name }}</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $order->created_at->format('M d, Y • h:i A') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-900">Rs {{ number_format($order->total, 0) }}</span>
                                <div class="mt-1 flex items-center gap-1.5">
                                    @php
                                        $pTone = match($order->payment_status) {
                                            'paid' => 'emerald',
                                            'pending' => 'amber',
                                            'failed' => 'rose',
                                            default => 'slate'
                                        };
                                    @endphp
                                    <span class="h-1.5 w-1.5 rounded-full bg-{{ $pTone }}-500"></span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-{{ $pTone }}-600">{{ $order->payment_status }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $sTone = match($order->status) {
                                    'pending' => 'amber',
                                    'confirmed', 'processing' => 'indigo',
                                    'shipped', 'delivered', 'completed' => 'emerald',
                                    'cancelled', 'rejected' => 'rose',
                                    'return_requested' => 'pink',
                                    default => 'slate'
                                };
                            @endphp
                            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-{{ $sTone }}-50 text-{{ $sTone }}-700 border border-{{ $sTone }}-100 font-semibold text-[11px]">
                                <i class="fas {{ $order->status_icon ?? 'fa-circle' }} text-[9px]"></i>
                                <span>{{ $order->status_label }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($order->payment_review_status === 'pending_review')
                                    <button wire:click="openPaymentModal({{ $order->id }})" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-amber-600 shadow-xs">Verify Payment</button>
                                @endif

                                @if($order->status === 'pending')
                                     <button wire:click="quickConfirm({{ $order->id }})" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-800 shadow-xs" title="Quick Confirm">Quick Confirm</button>
                                     <button wire:click="openStatusModal({{ $order->id }})" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white h-8 w-8 text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition-colors shadow-xs" title="Update Status"><i class="fas fa-ellipsis text-xs"></i></button>
                                @endif

                                @if(in_array($order->status, ['confirmed', 'processing']) && !$order->tracking_number)
                                    <button wire:click="openTrackingModal({{ $order->id }})" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700 shadow-xs">Ship Order</button>
                                @endif

                                @if($order->status === 'shipped')
                                    <button wire:click="markAsDelivered({{ $order->id }})" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-700 shadow-xs">Delivered</button>
                                @endif

                                <button wire:click="viewOrder({{ $order->id }})" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white h-8 w-8 text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition-colors shadow-xs" title="View Details">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <i class="fas fa-receipt text-3xl text-slate-300 mb-3"></i>
                            <p class="text-sm font-bold text-slate-900">No orders found</p>
                            <p class="mt-1 text-xs text-slate-500">Try adjusting your search or filters.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="block lg:hidden divide-y divide-slate-100">
        @forelse($orders as $order)
            <div class="p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-900 text-white shadow-xs">
                            <i class="fas fa-box-archive text-xs"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">#{{ $order->order_number }}</h4>
                            <p class="text-[10px] text-slate-400">{{ $order->customer_name }}</p>
                        </div>
                    </div>
                    @php
                        $sTone = match($order->status) {
                            'pending' => 'amber',
                            'confirmed', 'processing' => 'indigo',
                            'shipped', 'delivered', 'completed' => 'emerald',
                            'cancelled', 'rejected' => 'rose',
                            'return_requested' => 'pink',
                            default => 'slate'
                        };
                    @endphp
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-{{ $sTone }}-50 text-{{ $sTone }}-700 border border-{{ $sTone }}-100 text-[10px] font-semibold">
                        <i class="fas {{ $order->status_icon ?? 'fa-circle' }} text-[8px]"></i>
                        <span>{{ $order->status_label }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs border-t border-slate-100 pt-2">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Date</p>
                        <p class="font-semibold text-slate-700 mt-0.5">
                            {{ $order->created_at->format('M d, Y • h:i A') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total & Payment</p>
                        <p class="font-bold text-slate-900 mt-0.5">
                            Rs {{ number_format($order->total, 0) }}
                        </p>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-{{ match($order->payment_status) {
                            'paid' => 'emerald',
                            'pending' => 'amber',
                            'failed' => 'rose',
                            default => 'slate'
                        } }}-600 mt-0.5">
                            {{ $order->payment_status }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 border-t border-slate-100 pt-2">
                    @if($order->payment_review_status === 'pending_review')
                        <button wire:click="openPaymentModal({{ $order->id }})" class="flex-1 rounded-lg bg-amber-500 py-1.5 text-xs font-semibold text-white">Verify Payment</button>
                    @endif

                    @if($order->status === 'pending')
                         <button wire:click="quickConfirm({{ $order->id }})" class="flex-1 rounded-lg bg-slate-900 py-1.5 text-xs font-semibold text-white">Confirm</button>
                         <button wire:click="openStatusModal({{ $order->id }})" class="h-8 w-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500"><i class="fas fa-ellipsis text-xs"></i></button>
                    @endif

                    @if(in_array($order->status, ['confirmed', 'processing']) && !$order->tracking_number)
                        <button wire:click="openTrackingModal({{ $order->id }})" class="flex-1 rounded-lg bg-indigo-600 py-1.5 text-xs font-semibold text-white">Ship</button>
                    @endif

                    @if($order->status === 'shipped')
                        <button wire:click="markAsDelivered({{ $order->id }})" class="flex-1 rounded-lg bg-emerald-600 py-1.5 text-xs font-semibold text-white">Delivered</button>
                    @endif

                    <button wire:click="viewOrder({{ $order->id }})" class="h-8 w-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500">
                        <i class="fas fa-eye text-xs"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="p-8 text-center">
                <i class="fas fa-receipt text-3xl text-slate-300 mb-3"></i>
                <p class="text-sm font-bold text-slate-900">No orders found</p>
                <p class="mt-1 text-xs text-slate-500">Try adjusting your search or filters.</p>
            </div>
        @endforelse
    </div>

    @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $orders->links() }}
        </div>
    @endif
</div>
