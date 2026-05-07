@props(['viewingOrder'])

<div 
    x-data="{ show: true }" 
    x-show="show" 
    class="fixed inset-0 z-[100] flex items-center justify-center p-4 lg:p-8"
>
    <div 
        @click="$wire.closeDetail()" 
        class="absolute inset-0 bg-slate-900/40 backdrop-blur-md transition-opacity"
    ></div>

    <div class="relative w-full max-w-6xl max-h-[90vh] overflow-hidden rounded-[3rem] bg-white shadow-2xl flex flex-col border border-slate-200">
        <!-- Dashboard Header Protocol -->
        <div class="flex items-center justify-between p-8 border-b border-slate-50">
            <div class="flex items-center gap-5">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-xl">
                    <i class="fas fa-receipt text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">Order #{{ $viewingOrder->order_number }}</h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] mt-1">Registry Narrative & Lifecycle</p>
                </div>
            </div>
            <button @click="$wire.closeDetail()" class="h-12 w-12 flex items-center justify-center rounded-2xl bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all shadow-inner">
                <i class="fas fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-8 scrollbar-hide">
            <div class="grid gap-10 lg:grid-cols-3">
                <!-- Lineage & Logistics -->
                <div class="lg:col-span-2 space-y-10">
                    <!-- Itemization Registry -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-cubes text-[10px] text-slate-400"></i>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Payload Registry</label>
                        </div>
                        <div class="space-y-4">
                            @foreach($viewingOrder->items as $item)
                                <div class="flex items-center justify-between p-5 rounded-3xl bg-slate-50 border border-slate-100 group hover:bg-white hover:border-slate-200 transition-all">
                                    <div class="flex items-center gap-4">
                                        <div class="h-16 w-16 rounded-2xl bg-white border border-slate-100 overflow-hidden shadow-sm p-1">
                                            @php $img = collect($item->stock?->images)->first(); @endphp
                                            @if($img)
                                                <img src="{{ asset('storage/' . ($img['path'] ?? '')) }}" class="h-full w-full object-cover rounded-xl">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-slate-200"><i class="fas fa-box"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-black text-slate-900 uppercase truncate max-w-[200px]">{{ $item->product_name }}</h5>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">SKU: {{ $item->stock?->sku ?? 'N/A' }}</p>
                                            <div class="mt-2 flex items-center gap-2">
                                                <span class="text-[10px] font-black text-slate-900 tracking-tight">Rs {{ number_format($item->price, 0) }}</span>
                                                <span class="text-[9px] font-bold text-slate-400">x {{ $item->quantity }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-black text-slate-900">Rs {{ number_format($item->subtotal, 0) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="rounded-3xl bg-slate-900 p-8 shadow-xl relative overflow-hidden">
                            <div class="absolute right-0 bottom-0 -mr-8 -mb-8 h-24 w-24 rounded-full bg-white/5"></div>
                            <div class="relative z-10 flex flex-col gap-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-black text-white/40 uppercase tracking-widest">Sub-Protocol Total</span>
                                    <span class="text-xs font-bold text-white">Rs {{ number_format($viewingOrder->subtotal, 0) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-black text-white/40 uppercase tracking-widest">Logistics Surcharge</span>
                                    <span class="text-xs font-bold text-white">Rs {{ number_format($viewingOrder->shipping_cost, 0) }}</span>
                                </div>
                                <div class="pt-4 border-t border-white/10 flex items-center justify-between">
                                    <span class="text-xs font-black text-white uppercase tracking-[0.2em]">Registry Total</span>
                                    <span class="text-2xl font-black text-white">Rs {{ number_format($viewingOrder->total, 0) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Logistics Blueprint -->
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="p-6 rounded-[2rem] bg-slate-50 border border-slate-100">
                             <div class="flex items-center gap-3 mb-5">
                                <i class="fas fa-map-location-dot text-[10px] text-slate-400"></i>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Fulfillment Destination</label>
                            </div>
                            <div class="space-y-3">
                                <p class="text-xs font-black text-slate-900 leading-relaxed">{{ $viewingOrder->shipping_address }}</p>
                                <div class="flex flex-wrap gap-2 pt-2">
                                    <span class="px-3 py-1 rounded-lg bg-white border border-slate-100 text-[9px] font-black text-slate-500 uppercase tracking-widest">{{ $viewingOrder->city }}</span>
                                    <span class="px-3 py-1 rounded-lg bg-white border border-slate-100 text-[9px] font-black text-slate-500 uppercase tracking-widest">{{ $viewingOrder->postal_code }}</span>
                                </div>
                            </div>
                        </div>
                         <div class="p-6 rounded-[2rem] bg-slate-50 border border-slate-100">
                             <div class="flex items-center gap-3 mb-5">
                                <i class="fas fa-address-book text-[10px] text-slate-400"></i>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Contact Identity</label>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white shadow-sm"><i class="fas fa-user text-[10px] text-slate-400"></i></div>
                                    <span class="text-xs font-black text-slate-900">{{ $viewingOrder->customer_name }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white shadow-sm"><i class="fas fa-phone text-[10px] text-slate-400"></i></div>
                                    <span class="text-xs font-black text-slate-900">{{ $viewingOrder->customer_phone }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white shadow-sm"><i class="fas fa-envelope text-[10px] text-slate-400"></i></div>
                                    <span class="text-xs font-black text-slate-900 truncate">{{ $viewingOrder->customer_email }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Protocol Lifecycle -->
                <div class="space-y-10">
                    <!-- Workflow Authorization -->
                    <div class="p-8 rounded-[2.5rem] bg-slate-50 border border-slate-100 space-y-6">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-user-shield text-[10px] text-slate-400"></i>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Administrative Actions</label>
                        </div>
                        <div class="flex flex-col gap-3">
                            @if($viewingOrder->payment_review_status === 'pending_review')
                                <button wire:click="openPaymentModal({{ $viewingOrder->id }})" class="w-full h-12 rounded-2xl bg-amber-500 text-white text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-amber-500/20 hover:scale-105 transition-transform">Verify Payment</button>
                            @endif

                            <button wire:click="openStatusModal({{ $viewingOrder->id }})" class="w-full h-12 rounded-2xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] hover:bg-indigo-600 transition-colors">Adjust Status</button>
                            
                            @if(in_array($viewingOrder->status, ['confirmed', 'processing']))
                                <button wire:click="openTrackingModal({{ $viewingOrder->id }})" class="w-full h-12 rounded-2xl bg-indigo-600 text-white text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-indigo-600/20 hover:scale-105 transition-transform">Update Logistics</button>
                            @endif

                            @if($viewingOrder->status === 'return_requested')
                                <button wire:click="openReturnModal({{ $viewingOrder->id }})" class="w-full h-12 rounded-2xl bg-pink-600 text-white text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-pink-600/20 hover:scale-105 transition-transform">Resolve Return</button>
                            @endif

                            @if($viewingOrder->canBeCancelled())
                                <button 
                                    wire:confirm="Initialize cancellation protocol for order #{{ $viewingOrder->order_number }}?"
                                    wire:click="cancelOrder({{ $viewingOrder->id }})" 
                                    class="w-full h-12 rounded-2xl bg-white border border-rose-100 text-rose-500 text-[10px] font-black uppercase tracking-[0.2em] hover:bg-rose-50 transition-colors"
                                >
                                    Terminate Order
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Status Ledger -->
                    <div class="space-y-6 px-2">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-clock-rotate-left text-[10px] text-slate-400"></i>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Status History</label>
                        </div>
                        <div class="relative space-y-8 before:absolute before:inset-y-0 before:left-[11px] before:w-0.5 before:bg-slate-100">
                            @foreach($viewingOrder->statusHistory as $history)
                                <div class="relative flex items-start gap-5 pl-8">
                                    <div class="absolute left-0 top-1.5 h-6 w-6 rounded-full bg-white border-2 border-slate-200 flex items-center justify-center z-10">
                                        <div class="h-2 w-2 rounded-full bg-slate-400"></div>
                                    </div>
                                    <div>
                                        <h6 class="text-[10px] font-black text-slate-900 uppercase tracking-widest">{{ ucfirst($history->status) }}</h6>
                                        <p class="text-[9px] font-bold text-slate-400 mt-1 uppercase tracking-tighter">{{ $history->created_at->format('M d, h:i A') }}</p>
                                        @if($history->note)
                                            <p class="mt-2 p-3 rounded-xl bg-slate-50 border border-slate-100 text-[9px] font-bold text-slate-500 leading-relaxed">{{ $history->note }}</p>
                                        @endif
                                        <div class="mt-2 flex items-center gap-1.5">
                                            <i class="fas fa-user-circle text-[10px] text-slate-300"></i>
                                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">{{ $history->changedBy->name ?? 'System' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Footer Protocol -->
        <div class="p-8 border-t border-slate-50 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-4">
                <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em]">Protocol Active</span>
            </div>
             <button @click="$wire.closeDetail()" class="h-12 px-8 rounded-2xl bg-white border border-slate-200 text-slate-900 text-[10px] font-black uppercase tracking-[0.2em] hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                Close Narrative
            </button>
        </div>
    </div>
</div>
