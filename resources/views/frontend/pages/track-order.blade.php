@extends('layouts.shop')
@section('title', 'Track Order')
@section('content')
<div class="mx-auto max-w-6xl py-8">
    <nav class="mb-6 flex items-center gap-2 text-sm text-slate-400">
        <a wire:navigate href="/" class="hover:text-slate-700 dark:hover:text-slate-100">Home</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="font-medium text-slate-700 dark:text-slate-100">Track Order</span>
    </nav>

    <div class="glass card-shadow rounded-[2rem] px-6 py-8">
        <p class="text-xs font-semibold uppercase tracking-[0.28em]" style="color:var(--primary)">Order Updates</p>
        <h1 class="mt-3 text-4xl font-black text-adapt">Track your order status</h1>
        <p class="mt-4 max-w-3xl text-sm leading-7 text-soft">Enter your order number and optionally the same email used during checkout to view the latest recorded status.</p>

        <form method="GET" action="{{ route('track-order') }}" class="mt-6 grid gap-4 md:grid-cols-[1.3fr_1fr_auto]">
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-soft">Order Number</label>
                <input type="text" name="order_number" value="{{ request('order_number') }}" class="field" placeholder="ORD-20260401-ABCDE">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-soft">Email</label>
                <input type="email" name="email" value="{{ request('email') }}" class="field" placeholder="name@example.com">
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn-gradient w-full rounded-2xl px-6 py-3 text-sm font-semibold">Track</button>
            </div>
        </form>
    </div>

    @if($searched)
        <div class="mt-8">
            @if($order)
                <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
                    <div class="space-y-6">
                        <div class="surface card-shadow rounded-[1.75rem] p-6">
                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-soft">Order Found</p>
                                    <h2 class="mt-2 text-2xl font-bold text-adapt">{{ $order->order_number }}</h2>
                                    <p class="mt-2 text-sm text-soft">{{ $order->customer_name }} · {{ $order->customer_email }}</p>
                                </div>
                                <span class="inline-flex rounded-full px-4 py-2 text-xs font-semibold text-white" style="background: {{ $order->status_color }}">
                                    {{ $order->status_label }}
                                </span>
                            </div>

                            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                                <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-white/10 dark:bg-slate-900/70">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Payment Status</p>
                                    <p class="mt-2 text-sm font-semibold text-adapt">{{ ucfirst($order->payment_status) }}</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-white/10 dark:bg-slate-900/70">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Total</p>
                                    <p class="mt-2 text-sm font-semibold text-adapt">Rs {{ number_format($order->total, 2) }}</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-white/10 dark:bg-slate-900/70">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Courier</p>
                                    <p class="mt-2 text-sm font-semibold text-adapt">{{ $order->courier ?: 'Not assigned yet' }}</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-white/10 dark:bg-slate-900/70">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Tracking Number</p>
                                    <p class="mt-2 text-sm font-semibold text-adapt">{{ $order->tracking_number ?: 'Pending' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="surface card-shadow rounded-[1.75rem] p-6">
                            <h3 class="text-lg font-bold text-adapt">Order Timeline</h3>
                            <div class="mt-5 space-y-4">
                                @forelse($order->statusHistory as $history)
                                    <div class="flex gap-4">
                                        <div class="mt-1 h-3 w-3 rounded-full" style="background:var(--primary)"></div>
                                        <div>
                                            <p class="text-sm font-semibold text-adapt">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</p>
                                            <p class="mt-1 text-sm text-soft">{{ $history->note ?: 'Status updated by the team.' }}</p>
                                            <p class="mt-1 text-xs text-slate-400">{{ $history->created_at?->format('M d, Y h:i A') }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-sm text-soft">The order exists, but no timeline items were recorded yet.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="surface card-shadow rounded-[1.75rem] p-6">
                            <h3 class="text-lg font-bold text-adapt">Items</h3>
                            <div class="mt-5 space-y-3">
                                @foreach($order->items as $item)
                                    <div class="rounded-2xl border border-slate-200 bg-white/80 px-4 py-4 dark:border-white/10 dark:bg-slate-900/70">
                                        <p class="text-sm font-semibold text-adapt">{{ $item->name }}</p>
                                        <p class="mt-1 text-xs text-soft">Qty {{ $item->quantity }} · Rs {{ number_format($item->price, 2) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="surface card-shadow rounded-[1.75rem] p-6">
                            <h3 class="text-lg font-bold text-adapt">Need help?</h3>
                            <p class="mt-3 text-sm leading-7 text-soft">If the status looks incorrect or payment verification is delayed, use the support chat button or open the help center.</p>
                            <div class="mt-4 flex flex-wrap gap-3">
                                <a wire:navigate href="{{ route('help-center') }}" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">Open Help Center</a>
                                <a wire:navigate href="{{ route('products.index') }}" class="btn-gradient rounded-full px-5 py-3 text-sm font-semibold">Continue Shopping</a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="surface card-shadow rounded-[1.75rem] px-6 py-16 text-center">
                    <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-amber-50">
                        <i class="fas fa-magnifying-glass text-3xl text-amber-500"></i>
                    </div>
                    <h2 class="text-xl font-bold text-adapt">No order matched that search</h2>
                    <p class="mt-3 text-sm leading-7 text-soft">Check the order number and email, or use the help center if you need the support team to verify it for you.</p>
                    <div class="mt-6 flex flex-wrap justify-center gap-3">
                        <a wire:navigate href="{{ route('help-center') }}" class="rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">Help Center</a>
                        <a wire:navigate href="{{ route('products.index') }}" class="btn-gradient rounded-full px-6 py-3 text-sm font-semibold">Browse Products</a>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
