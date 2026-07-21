@extends('layouts.shop')
@section('title', 'Track Order')
@section('content')
@php
    $panel = "premium-card !p-8 !rounded-[2.5rem]";
    $muted = "text-[10px] font-black uppercase tracking-[0.2em] text-slate-400";
    $input = "w-full h-14 rounded-3xl border-slate-100 bg-slate-50 px-6 text-sm font-bold text-slate-900 focus:border-[var(--primary)] focus:ring-0 transition-all dark:border-white/5 dark:bg-white/5 dark:text-white";
@endphp

<div class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
    <header class="mb-12">
        <nav class="mb-6 flex items-center gap-3 text-[10px] font-black uppercase tracking-widest text-slate-400">
            <a wire:navigate href="/" class="hover:text-[var(--primary)] transition-colors">Home</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-slate-900 dark:text-white">Track Order</span>
        </nav>
        <p class="text-[var(--primary)] text-[10px] font-black uppercase tracking-[0.3em] mb-4">Order Status</p>
        <h1 class="text-5xl font-black text-slate-900 dark:text-white tracking-tighter">Track Order</h1>
        <p class="mt-4 max-w-3xl text-sm font-bold text-slate-400 leading-relaxed">Enter your order number and email address to check your order status and shipment details.</p>
    </header>

    <div class="glass !p-12 mb-12 rounded-[3.5rem] relative overflow-hidden">
        <form method="GET" action="{{ route('track-order') }}" class="grid gap-6 md:grid-cols-[1.3fr_1.1fr_auto] items-end">
            <div class="space-y-2">
                <label class="{{ $muted }} ml-4">Order Number</label>
                <input type="text" name="order_number" value="{{ request('order_number') }}" class="{{ $input }}" placeholder="e.g. ORD-2026-1001">
            </div>
            <div class="space-y-2">
                <label class="{{ $muted }} ml-4">Email Address</label>
                <input type="email" name="email" value="{{ request('email') }}" class="{{ $input }}" placeholder="your.email@example.com">
            </div>
            <button type="submit" class="h-14 px-12 rounded-full bg-slate-900 text-[10px] font-black uppercase tracking-widest text-white shadow-2xl hover:scale-105 transition-all">Track Order</button>
        </form>
    </div>

    @if($searched)
        <div class="mt-12 transition-all">
            @if($order)
                <div class="grid gap-12 lg:grid-cols-[1.2fr_0.8fr]">
                    <div class="space-y-12">
                        <section class="{{ $panel }}">
                            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between mb-12">
                                <div>
                                    <p class="{{ $muted }} mb-2">Order Found</p>
                                    <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Order #{{ $order->order_number }}</h2>
                                    <p class="mt-2 text-xs font-bold text-slate-400">{{ $order->customer_name }} · {{ $order->customer_email }}</p>
                                </div>
                                <span class="h-10 px-6 flex items-center justify-center rounded-full text-[9px] font-black uppercase tracking-widest text-white shadow-lg" style="background: {{ $order->status_color }}">
                                    {{ $order->status_label }}
                                </span>
                            </div>

                            <div class="grid gap-6 sm:grid-cols-2">
                                @foreach([
                                    ['Payment Status', ucfirst($order->payment_status)],
                                    ['Order Total', 'Rs '.number_format($order->total, 2)],
                                    ['Courier / Carrier', $order->courier ?: 'Unassigned'],
                                    ['Tracking Number', $order->tracking_number ?: 'Pending']
                                ] as [$label, $value])
                                    <div class="p-6 rounded-3xl bg-slate-50/50 dark:bg-white/5 border border-slate-50 dark:border-white/5">
                                        <p class="{{ $muted }} mb-2">{{ $label }}</p>
                                        <p class="text-sm font-black text-slate-900 dark:text-white tracking-tight">{{ $value }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </section>

                        <section class="{{ $panel }}">
                            <p class="{{ $muted }} mb-10">Order History & Updates</p>
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
                                            <p class="text-[11px] font-bold text-slate-500 leading-relaxed">{{ $history->note ?: 'Order status updated.' }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-12 text-center glass rounded-[2.5rem]">
                                        <i class="fas fa-history text-2xl text-slate-200 mb-4"></i>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">No History Records Available</p>
                                    </div>
                                @endforelse
                            </div>
                        </section>
                    </div>

                    <aside class="space-y-12">
                        <section class="{{ $panel }}">
                            <p class="{{ $muted }} mb-10">Order Items</p>
                            <div class="space-y-4">
                                @foreach($order->items as $item)
                                    <div class="p-5 rounded-3xl bg-slate-50/50 dark:bg-white/5 border border-slate-50 dark:border-white/5">
                                        <p class="text-xs font-black text-slate-900 dark:text-white tracking-tight mb-1">{{ $item->name }}</p>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Qty {{ $item->quantity }} · Rs {{ number_format($item->price, 2) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </section>

                        <section class="{{ $panel }}">
                            <p class="{{ $muted }} mb-8">Need Assistance?</p>
                            <p class="text-[11px] font-bold text-slate-400 leading-relaxed mb-10">If you have questions about your order status or payment verification, please contact our support team.</p>
                            <div class="grid gap-4">
                                <a wire:navigate href="{{ route('help-center') }}" class="h-12 flex items-center justify-center rounded-full border border-slate-100 text-[9px] font-black uppercase tracking-widest text-slate-500 hover:border-[var(--primary)] hover:text-[var(--primary)] transition-all dark:border-white/5">Help Center</a>
                                <a wire:navigate href="{{ route('products.index') }}" class="h-12 flex items-center justify-center rounded-full bg-slate-900 text-[9px] font-black uppercase tracking-widest text-white shadow-lg hover:scale-105 transition-all">Browse Products</a>
                            </div>
                        </section>
                    </aside>
                </div>
            @else
                <div class="glass py-24 text-center rounded-[3.5rem]">
                    <div class="mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-full bg-slate-50 text-amber-500 dark:bg-white/5">
                        <i class="fas fa-search-minus text-3xl"></i>
                    </div>
                    <h2 class="mb-4 text-2xl font-black text-slate-900 dark:text-white tracking-tight">Order Not Found</h2>
                    <p class="mx-auto mb-10 max-w-md text-sm font-bold text-slate-400 leading-relaxed">We couldn't find an order matching that order number and email address. Please check your details and try again.</p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a wire:navigate href="{{ route('help-center') }}" class="h-14 px-10 flex items-center justify-center rounded-full border border-slate-100 bg-white text-[10px] font-black uppercase tracking-widest text-slate-500 shadow-sm transition-all hover:bg-slate-50">Help Center</a>
                        <a wire:navigate href="{{ route('products.index') }}" class="h-14 px-10 flex items-center justify-center rounded-full bg-slate-900 text-[10px] font-black uppercase tracking-widest text-white shadow-2xl hover:scale-105 transition-all">Browse Products</a>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
