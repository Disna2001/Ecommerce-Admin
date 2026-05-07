@props(['orders', 'sortField', 'sortDir'])

<div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm overflow-hidden">
    <div class="flex items-center gap-4 mb-8 px-2">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white shadow-lg">
            <i class="fas fa-list-check text-xs"></i>
        </div>
        <div>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Fulfillment Ledger</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Global Order Registry</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-separate border-spacing-y-4">
            <thead>
                <tr class="text-left">
                    <th class="px-6 pb-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Order Blueprint</th>
                    <th class="px-6 pb-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Registry Value</th>
                    <th class="px-6 pb-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Fulfillment Status</th>
                    <th class="px-6 pb-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 text-right">Administrative Protocol</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="group">
                        <td class="bg-slate-50 border-y border-l border-slate-100 rounded-l-[2rem] px-6 py-6 group-hover:bg-white transition-colors duration-500">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white shadow-sm border border-slate-100 group-hover:bg-slate-900 group-hover:text-white transition-all duration-500">
                                    <i class="fas fa-box-archive text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest">#{{ $order->order_number }}</h4>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter mt-1">{{ $order->customer_name }}</p>
                                    <div class="mt-2 flex items-center gap-2">
                                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">{{ $order->created_at->format('M d, Y') }}</span>
                                        <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">{{ $order->created_at->format('h:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="bg-slate-50 border-y border-slate-100 px-6 py-6 group-hover:bg-white transition-colors duration-500">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-slate-900 tracking-tight">Rs {{ number_format($order->total, 0) }}</span>
                                <div class="mt-2 flex items-center gap-2">
                                    @php
                                        $pTone = match($order->payment_status) {
                                            'paid' => 'emerald',
                                            'pending' => 'amber',
                                            'failed' => 'rose',
                                            default => 'slate'
                                        };
                                    @endphp
                                    <div class="h-1.5 w-1.5 rounded-full bg-{{ $pTone }}-500"></div>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-{{ $pTone }}-600">{{ $order->payment_status }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="bg-slate-50 border-y border-slate-100 px-6 py-6 group-hover:bg-white transition-colors duration-500">
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
                            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-xl bg-{{ $sTone }}-50 text-{{ $sTone }}-600 border border-{{ $sTone }}-100 group-hover:bg-{{ $sTone }}-600 group-hover:text-white group-hover:border-{{ $sTone }}-600 transition-all duration-500">
                                <i class="fas {{ $order->status_icon ?? 'fa-circle' }} text-[10px]"></i>
                                <span class="text-[9px] font-black uppercase tracking-widest">{{ $order->status_label }}</span>
                            </div>
                        </td>
                        <td class="bg-slate-50 border-y border-r border-slate-100 rounded-r-[2rem] px-6 py-6 text-right group-hover:bg-white transition-colors duration-500">
                            <div class="flex items-center justify-end gap-2">
                                @if($order->payment_review_status === 'pending_review')
                                    <button wire:click="openPaymentModal({{ $order->id }})" class="h-10 px-4 rounded-xl bg-amber-500 text-white text-[9px] font-black uppercase tracking-widest shadow-lg shadow-amber-500/20 hover:scale-105 transition-transform">Verify Payment</button>
                                @endif

                                @if($order->status === 'pending')
                                     <button wire:click="quickConfirm({{ $order->id }})" class="h-10 px-4 rounded-xl bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest hover:scale-105 transition-transform" title="One-click Confirmation">Quick Confirm</button>
                                     <button wire:click="openStatusModal({{ $order->id }})" class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:text-slate-900 transition-all" title="Confirm with Narrative"><i class="fas fa-ellipsis text-xs"></i></button>
                                @endif

                                @if(in_array($order->status, ['confirmed', 'processing']) && !$order->tracking_number)
                                    <button wire:click="openTrackingModal({{ $order->id }})" class="h-10 px-4 rounded-xl bg-indigo-600 text-white text-[9px] font-black uppercase tracking-widest shadow-lg shadow-indigo-600/20 hover:scale-105 transition-transform">Ship Order</button>
                                @endif

                                @if($order->status === 'shipped')
                                    <button wire:click="markAsDelivered({{ $order->id }})" class="h-10 px-4 rounded-xl bg-emerald-600 text-white text-[9px] font-black uppercase tracking-widest shadow-lg shadow-emerald-600/20 hover:scale-105 transition-transform">Delivered</button>
                                @endif

                                <button wire:click="viewOrder({{ $order->id }})" class="h-10 w-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-slate-900 hover:border-slate-900 transition-all">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-20 text-center">
                            <i class="fas fa-receipt text-4xl text-slate-100 mb-4"></i>
                            <p class="text-sm font-black text-slate-900 uppercase tracking-tight">No records identified in fulfillment ledger</p>
                            <p class="mt-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Broaden your search or adjust your registry filters.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-8">
        {{ $orders->links() }}
    </div>
</div>
