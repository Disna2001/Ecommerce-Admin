@props(['viewingOrder'])

<div 
    x-data="{ show: true }" 
    x-show="show" 
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
>
    <div class="relative w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-xl bg-white shadow-xl flex flex-col border border-slate-200">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-900 text-white shadow-xs">
                    <i class="fas fa-receipt text-sm"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Order #{{ $viewingOrder->order_number }}</h2>
                    <p class="text-xs text-slate-500">Order details & status history</p>
                </div>
            </div>
            <button @click="$wire.closeDetail()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-times text-base"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-6 text-xs">
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Left: Items & Totals -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Items -->
                    <div class="space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Order Items</label>
                        <div class="space-y-3">
                            @foreach($viewingOrder->items as $item)
                                <div class="flex items-center justify-between p-3.5 rounded-lg bg-slate-50 border border-slate-100">
                                    <div class="flex items-center gap-3">
                                        <div class="h-12 w-12 rounded-lg bg-white border border-slate-200 overflow-hidden shrink-0">
                                            @php $img = collect($item->stock?->images)->first(); @endphp
                                            @if($img)
                                                <img src="{{ asset('storage/' . ($img['path'] ?? '')) }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-slate-300"><i class="fas fa-box"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <h5 class="font-bold text-slate-900 truncate max-w-[200px]">{{ $item->product_name }}</h5>
                                            <p class="text-[10px] font-mono text-slate-400">SKU: {{ $item->stock?->sku ?? 'N/A' }}</p>
                                            <div class="mt-1 flex items-center gap-2">
                                                <span class="font-bold text-slate-700">Rs {{ number_format($item->price, 0) }}</span>
                                                <span class="text-slate-400">x {{ $item->quantity }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-bold text-slate-900">Rs {{ number_format($item->subtotal, 0) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Summary Card -->
                        <div class="rounded-lg bg-slate-900 p-5 text-white space-y-2">
                            <div class="flex items-center justify-between text-slate-300">
                                <span>Subtotal</span>
                                <span class="font-semibold text-white">Rs {{ number_format($viewingOrder->subtotal, 0) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-300">
                                <span>Shipping</span>
                                <span class="font-semibold text-white">Rs {{ number_format($viewingOrder->shipping_cost, 0) }}</span>
                            </div>
                            <div class="pt-2 border-t border-slate-800 flex items-center justify-between font-bold text-sm">
                                <span>Total</span>
                                <span class="text-base font-bold text-white">Rs {{ number_format($viewingOrder->total, 0) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Logistics & Contact -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="p-4 rounded-lg bg-slate-50 border border-slate-100 space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Shipping Address</label>
                            <p class="font-semibold text-slate-800 leading-relaxed">{{ $viewingOrder->shipping_address }}</p>
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 text-[10px] font-semibold text-slate-600">{{ $viewingOrder->city }}</span>
                                <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 text-[10px] font-semibold text-slate-600">{{ $viewingOrder->postal_code }}</span>
                            </div>
                        </div>
                        <div class="p-4 rounded-lg bg-slate-50 border border-slate-100 space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Customer Contact</label>
                            <div class="space-y-1.5 font-semibold text-slate-800">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user text-[10px] text-slate-400"></i>
                                    <span>{{ $viewingOrder->customer_name }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-phone text-[10px] text-slate-400"></i>
                                    <span>{{ $viewingOrder->customer_phone }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-envelope text-[10px] text-slate-400"></i>
                                    <span class="truncate">{{ $viewingOrder->customer_email }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Actions & History -->
                <div class="space-y-6">
                    <!-- Actions -->
                    <div class="p-4 rounded-lg bg-slate-50 border border-slate-100 space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Actions</label>
                        <div class="flex flex-col gap-2">
                            @if($viewingOrder->payment_review_status === 'pending_review')
                                <button wire:click="openPaymentModal({{ $viewingOrder->id }})" class="w-full rounded-lg bg-amber-500 px-3 py-2 font-semibold text-white hover:bg-amber-600 shadow-xs">Verify Payment</button>
                            @endif

                            <button wire:click="openStatusModal({{ $viewingOrder->id }})" class="w-full rounded-lg bg-slate-900 px-3 py-2 font-semibold text-white hover:bg-slate-800 shadow-xs">Update Status</button>
                            
                            @if(in_array($viewingOrder->status, ['confirmed', 'processing']))
                                <button wire:click="openTrackingModal({{ $viewingOrder->id }})" class="w-full rounded-lg bg-indigo-600 px-3 py-2 font-semibold text-white hover:bg-indigo-700 shadow-xs">Ship Order</button>
                            @endif

                            @if($viewingOrder->status === 'return_requested')
                                <button wire:click="openReturnModal({{ $viewingOrder->id }})" class="w-full rounded-lg bg-pink-600 px-3 py-2 font-semibold text-white hover:bg-pink-700 shadow-xs">Resolve Return</button>
                            @endif

                            @if($viewingOrder->canBeCancelled())
                                <button 
                                    wire:confirm="Cancel order #{{ $viewingOrder->order_number }}?"
                                    wire:click="cancelOrder({{ $viewingOrder->id }})" 
                                    class="w-full rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 font-semibold text-rose-600 hover:bg-rose-100"
                                >
                                    Cancel Order
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Status History -->
                    <div class="space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Status History</label>
                        <div class="relative space-y-4 before:absolute before:inset-y-0 before:left-[7px] before:w-0.5 before:bg-slate-200">
                            @foreach($viewingOrder->statusHistory as $history)
                                <div class="relative flex items-start gap-3 pl-5">
                                    <div class="absolute left-0 top-1 h-3.5 w-3.5 rounded-full bg-white border-2 border-slate-400 z-10"></div>
                                    <div>
                                        <h6 class="font-bold text-slate-900 uppercase tracking-wider text-[11px]">{{ ucfirst($history->status) }}</h6>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $history->created_at->format('M d, h:i A') }}</p>
                                        @if($history->note)
                                            <p class="mt-1 p-2 rounded-md bg-slate-50 border border-slate-200 text-[10px] text-slate-600">{{ $history->note }}</p>
                                        @endif
                                        <p class="mt-1 text-[10px] font-medium text-slate-400">By: {{ $history->changedBy->name ?? 'System' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-slate-100 flex items-center justify-end bg-slate-50">
             <button @click="$wire.closeDetail()" class="rounded-lg border border-slate-200 bg-white px-4 py-2 font-semibold text-slate-700 hover:bg-slate-100 shadow-xs">
                Close
            </button>
        </div>
    </div>
</div>
