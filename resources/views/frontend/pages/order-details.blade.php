@extends('layouts.shop')
@section('title', 'Protocol #'.$order->order_number)
@section('content')
@php
    $progressStages = [
        'pending' => 0,
        'confirmed' => 1,
        'processing' => 2,
        'shipped' => 3,
        'delivered' => 4,
        'completed' => 5,
    ];
    $currentProgress = $progressStages[$order->status] ?? null;
    $progressPercent = $currentProgress === null ? 0 : (int) round(($currentProgress / 5) * 100);
    $canRequestReturn = $order->canBeReturned() && ! $order->isReturnPending() && ! in_array($order->status, ['returned', 'refunded'], true);
    
    $panel = "premium-card !p-8 !rounded-[2.5rem]";
    $muted = "text-[10px] font-black uppercase tracking-[0.2em] text-slate-400";
    $input = "w-full h-14 rounded-3xl border-slate-100 bg-slate-50 px-6 text-sm font-bold text-slate-900 focus:border-[var(--primary)] focus:ring-0 transition-all dark:border-white/5 dark:bg-white/5 dark:text-white";
@endphp

<div class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
    <header class="mb-12 flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <nav class="mb-6 flex items-center gap-3 text-[10px] font-black uppercase tracking-widest text-slate-400">
                <a wire:navigate href="/" class="hover:text-[var(--primary)] transition-colors">Matrix</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
                <a wire:navigate href="{{ route('profile.index', ['tab' => 'orders']) }}" class="hover:text-[var(--primary)] transition-colors">Registry</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
                <span class="text-slate-900 dark:text-white">Protocol {{ $order->order_number }}</span>
            </nav>
            <p class="text-[var(--primary)] text-[10px] font-black uppercase tracking-[0.3em] mb-4">Transaction Intelligence</p>
            <h1 class="text-5xl font-black text-slate-900 dark:text-white tracking-tighter">Protocol #{{ $order->order_number }}</h1>
        </div>
        <div class="flex flex-wrap items-center gap-4">
            <span class="h-12 px-8 flex items-center justify-center rounded-full text-[10px] font-black uppercase tracking-widest text-white shadow-xl" style="background: {{ $order->status_color }}">
                {{ $order->status_label }}
            </span>
            <span class="h-12 px-8 flex items-center justify-center rounded-full border border-slate-100 bg-white text-[10px] font-black uppercase tracking-widest text-slate-500 shadow-sm dark:border-white/5 dark:bg-slate-900 dark:text-slate-400">
                Ledger: {{ ucfirst($order->payment_status ?? 'unpaid') }}
            </span>
        </div>
    </header>

    <div class="grid gap-12 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="space-y-12">
            <section class="{{ $panel }}">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between mb-12">
                    <div>
                        <p class="{{ $muted }} mb-2">Fulfilment Stream</p>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Execution Progress</h2>
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Current Phase: {{ $order->status_label }}</span>
                </div>
                
                @if($currentProgress !== null)
                    <div class="relative pt-4">
                        <div class="h-1.5 w-full bg-slate-100 rounded-full dark:bg-white/5">
                            <div class="h-full rounded-full bg-gradient-to-r from-[var(--primary)] to-[var(--secondary)] transition-all duration-1000" style="width: {{ $progressPercent }}%"></div>
                        </div>
                        <div class="mt-10 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                            @foreach(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'completed'] as $index => $stage)
                                @php $active = $currentProgress >= $index; @endphp
                                <div class="rounded-3xl border p-4 text-center transition-all {{ $active ? 'border-[var(--primary)]/20 bg-[var(--primary)]/5 text-[var(--primary)]' : 'border-slate-50 bg-slate-50/50 text-slate-400 dark:border-white/5 dark:bg-white/5' }}">
                                    <p class="text-[8px] font-black uppercase tracking-widest">{{ str_replace('_', ' ', $stage) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="p-8 rounded-3xl bg-amber-50/50 border border-amber-100 dark:bg-amber-500/5 dark:border-amber-500/10">
                        <p class="text-sm font-bold text-amber-700 dark:text-amber-200">Protocol Intervention Active</p>
                        <p class="mt-2 text-xs text-amber-600/80 dark:text-amber-200/60">This protocol is currently in a specialized state (Cancellation / Return / Refund Review).</p>
                    </div>
                @endif
            </section>

            <section class="{{ $panel }}">
                <p class="{{ $muted }} mb-10">Asset Ledger</p>
                <div class="space-y-6">
                    @foreach($order->items as $item)
                        @php
                            $itemName = $item->product_name ?: ($item->stock?->name ?: 'Ordered item');
                            $itemPrice = (float) ($item->sale_price ?? $item->unit_price ?? 0);
                            $itemSubtotal = (float) ($item->subtotal ?? ($itemPrice * (int) $item->quantity));
                        @endphp
                        <div class="group flex flex-col gap-6 p-6 rounded-3xl border border-slate-50 hover:bg-slate-50 transition-all dark:border-white/5 dark:hover:bg-white/5 md:flex-row md:items-center">
                            <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-2xl bg-slate-100 dark:bg-white/5">
                                @if($item->stock?->image_url)
                                    <img src="{{ $item->stock->image_url }}" alt="" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-slate-300"><i class="fas fa-box text-xl"></i></div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight line-clamp-1">{{ $itemName }}</h3>
                                <div class="mt-2 flex flex-wrap gap-4">
                                    @if($item->product_sku)<span class="text-[9px] font-black uppercase tracking-widest text-slate-400">SKU: {{ $item->product_sku }}</span>@endif
                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Qty: {{ $item->quantity }}</span>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-[var(--primary)]">Unit: Rs {{ number_format($itemPrice, 2) }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-black text-slate-900 dark:text-white tracking-tight">Rs {{ number_format($itemSubtotal, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12 pt-12 border-t border-slate-100 dark:border-white/5 grid gap-8 sm:grid-cols-3">
                    @foreach([
                        ['Subtotal Accumulation', 'Rs '.number_format((float) $order->subtotal, 2)],
                        ['Logistics Contribution', 'Rs '.number_format((float) $order->shipping_fee, 2)],
                        ['Grand Total Ledger', 'Rs '.number_format((float) $order->total, 2), true]
                    ] as [$label, $value, $bold = false])
                        <div class="p-6 rounded-3xl bg-slate-50/50 dark:bg-white/5">
                            <p class="{{ $muted }} mb-2">{{ $label }}</p>
                            <p class="text-base font-black {{ $bold ? 'text-[var(--primary)]' : 'text-slate-900 dark:text-white' }} tracking-tight">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="{{ $panel }}">
                <p class="{{ $muted }} mb-10">Audit Trail</p>
                <div class="space-y-8 relative before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-px before:bg-slate-100 dark:before:bg-white/5">
                    @forelse($order->statusHistory as $history)
                        <div class="relative pl-10">
                            <div class="absolute left-0 top-1.5 h-6 w-6 flex items-center justify-center rounded-full bg-white border-4 border-slate-100 dark:bg-slate-900 dark:border-white/5 z-10">
                                <div class="h-2 w-2 rounded-full bg-[var(--primary)]"></div>
                            </div>
                            <div class="p-6 rounded-3xl bg-slate-50/50 dark:bg-white/5 hover:bg-slate-50 transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</p>
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $history->created_at?->format('M d, Y | h:i A') }}</span>
                                </div>
                                <p class="text-[11px] font-bold text-slate-500 leading-relaxed">{{ $history->note ?: 'Operational status synchronized successfully.' }}</p>
                                @if($history->changedBy)
                                    <p class="mt-4 text-[9px] font-black uppercase tracking-widest text-[var(--primary)]">Verified by: {{ $history->changedBy->name }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center glass rounded-[2.5rem]">
                            <i class="fas fa-history text-2xl text-slate-200 mb-4"></i>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">No Historical Records</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="space-y-12">
            <section class="{{ $panel }}">
                <p class="{{ $muted }} mb-10">Entity Intelligence</p>
                <div class="space-y-8">
                    @foreach([
                        ['Identification', $order->customer_name],
                        ['Communication', $order->customer_email],
                        ['Direct Channel', $order->customer_phone ?: 'Unlisted']
                    ] as [$label, $value])
                        <div class="rounded-3xl bg-slate-50/50 p-6 dark:bg-white/5">
                            <p class="{{ $muted }} mb-2">{{ $label }}</p>
                            <p class="text-sm font-black text-slate-900 dark:text-white tracking-tight">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="{{ $panel }}">
                <p class="{{ $muted }} mb-10">Geospatial Distribution</p>
                <div class="space-y-8">
                    <div class="rounded-3xl bg-slate-50/50 p-6 dark:bg-white/5">
                        <p class="{{ $muted }} mb-4">Destination Node</p>
                        <p class="text-[11px] font-bold text-slate-400 leading-relaxed">
                            {{ $order->shipping_address ?: 'Coordinates not registered.' }}
                            @if($order->shipping_city), {{ $order->shipping_city }}@endif
                            @if($order->shipping_postal_code), {{ $order->shipping_postal_code }}@endif
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-3xl bg-slate-50/50 p-6 dark:bg-white/5">
                            <p class="{{ $muted }} mb-2">Carrier</p>
                            <p class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-widest">{{ $order->courier ?: 'Unassigned' }}</p>
                        </div>
                        <div class="rounded-3xl bg-slate-50/50 p-6 dark:bg-white/5">
                            <p class="{{ $muted }} mb-2">Index</p>
                            <p class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-widest">{{ $order->tracking_number ?: 'Pending' }}</p>
                        </div>
                    </div>
                    @if($order->tracking_url)
                        <a href="{{ $order->tracking_url }}" target="_blank" class="w-full h-12 flex items-center justify-center rounded-full bg-slate-900 text-[9px] font-black uppercase tracking-widest text-white shadow-lg hover:scale-105 transition-all">Launch Tracking Interface</a>
                    @endif
                </div>
            </section>

            <section class="{{ $panel }}">
                <p class="{{ $muted }} mb-10">Protocol Resources</p>
                <div class="grid gap-4">
                    <a href="{{ route('orders.invoice', $order) }}" class="h-12 px-8 flex items-center gap-3 rounded-full border border-slate-100 text-[9px] font-black uppercase tracking-widest text-slate-500 hover:border-[var(--primary)] hover:text-[var(--primary)] transition-all dark:border-white/5">
                        <i class="fas fa-file-invoice text-xs"></i> Acquisition Invoice
                    </a>
                    <a href="{{ route('orders.receipt', $order) }}" class="h-12 px-8 flex items-center gap-3 rounded-full border border-slate-100 text-[9px] font-black uppercase tracking-widest text-slate-500 hover:border-[var(--primary)] hover:text-[var(--primary)] transition-all dark:border-white/5">
                        <i class="fas fa-receipt text-xs"></i> Operational Receipt
                    </a>
                    <a wire:navigate href="{{ route('track-order', ['order_number' => $order->order_number, 'email' => $order->customer_email]) }}" class="h-12 px-8 flex items-center gap-3 rounded-full bg-slate-900 text-[9px] font-black uppercase tracking-widest text-white shadow-lg hover:scale-105 transition-all">
                        <i class="fas fa-satellite text-xs"></i> Public Tracking Node
                    </a>
                </div>
            </section>

            <section class="{{ $panel }} group">
                <p class="{{ $muted }} mb-10">Retraction Protocol</p>
                @if($canRequestReturn)
                    <form method="POST" action="{{ route('orders.return-request', $order) }}" class="space-y-6">
                        @csrf
                        <div class="space-y-2">
                            <label class="{{ $muted }}">Retraction Reason</label>
                            <input type="text" name="return_reason" class="{{ $input }}" placeholder="Verification issue, deployment failure">
                        </div>
                        <div class="space-y-2">
                            <label class="{{ $muted }}">Analytical Notes</label>
                            <textarea name="return_notes" rows="3" class="{{ $input }} !h-auto !py-4 resize-none" placeholder="Provide technical metadata for return review"></textarea>
                        </div>
                        <button type="submit" class="w-full h-14 rounded-full bg-rose-500 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:scale-105 transition-all">Execute Retraction Request</button>
                    </form>
                @elseif($order->isReturnPending())
                    <div class="p-8 rounded-3xl bg-amber-50/50 border border-amber-100 dark:bg-amber-500/5 dark:border-amber-500/10">
                        <p class="text-[10px] font-black uppercase tracking-widest text-amber-700 dark:text-amber-200 mb-2">Pending Review</p>
                        <p class="text-[11px] font-bold text-amber-600/80 dark:text-amber-200/60 leading-relaxed">A retraction protocol has been initialized and is currently awaiting administrative approval.</p>
                    </div>
                @else
                    <div class="p-8 rounded-3xl bg-slate-50/50 dark:bg-white/5">
                        <p class="text-[11px] font-bold text-slate-400 leading-relaxed">Retraction protocols are only available for synchronized, delivered deployments.</p>
                    </div>
                @endif
            </section>
        </aside>
    </div>
</div>
@endsection
