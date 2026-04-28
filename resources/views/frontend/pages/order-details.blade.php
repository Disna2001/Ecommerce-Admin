@extends('layouts.shop')
@section('title', 'Order '.$order->order_number)
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
@endphp
<div class="mx-auto max-w-6xl py-8">
    <nav class="mb-6 flex flex-wrap items-center gap-2 text-sm text-slate-400">
        <a wire:navigate href="/" class="hover:text-slate-700 dark:hover:text-slate-100">Home</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <a wire:navigate href="{{ route('profile.index', ['tab' => 'orders']) }}" class="hover:text-slate-700 dark:hover:text-slate-100">My Orders</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="font-medium text-slate-700 dark:text-slate-100">{{ $order->order_number }}</span>
    </nav>

    <div class="glass card-shadow rounded-[2rem] px-6 py-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em]" style="color:var(--primary)">Order Details</p>
                <h1 class="mt-3 text-4xl font-black text-adapt">{{ $order->order_number }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-7 text-soft">Review every part of the order in one place, including payment status, item breakdown, shipping progress, and the full status timeline.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex rounded-full px-4 py-2 text-xs font-semibold text-white" style="background: {{ $order->status_color }}">
                    {{ $order->status_label }}
                </span>
                <span class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">
                    Payment: {{ ucfirst($order->payment_status ?? 'unpaid') }}
                </span>
            </div>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
        <div class="space-y-6">
            <div class="surface card-shadow rounded-[1.75rem] p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-adapt">Order progress</h2>
                        <p class="mt-2 text-sm text-soft">A quick view of where this order is in the fulfilment flow.</p>
                    </div>
                    <span class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">
                        {{ $order->status_label }}
                    </span>
                </div>
                @if($currentProgress !== null)
                    <div class="mt-6">
                        <div class="h-3 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                            <div class="h-full rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%; background: linear-gradient(90deg, var(--primary), var(--secondary));"></div>
                        </div>
                        <div class="mt-5 grid gap-3 sm:grid-cols-3 lg:grid-cols-6">
                            @foreach(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'completed'] as $index => $stage)
                                @php($active = $currentProgress >= $index)
                                <div class="rounded-2xl border px-3 py-3 text-center {{ $active ? 'border-violet-200 bg-violet-50 text-violet-800 dark:border-violet-500/20 dark:bg-violet-500/10 dark:text-violet-200' : 'border-slate-200 bg-white text-slate-400 dark:border-white/10 dark:bg-slate-900 dark:text-slate-500' }}">
                                    <p class="text-[11px] font-black uppercase tracking-[0.18em]">{{ str_replace('_', ' ', $stage) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="mt-6 rounded-2xl border border-slate-200 bg-white/80 p-4 text-sm text-soft dark:border-white/10 dark:bg-slate-900/70">
                        This order is currently in a special state such as cancellation, return review, or refund.
                    </div>
                @endif
            </div>

            <div class="surface card-shadow rounded-[1.75rem] p-6">
                <h2 class="text-xl font-bold text-adapt">Order summary</h2>
                <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-white/10 dark:bg-slate-900/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Placed on</p>
                        <p class="mt-2 text-sm font-semibold text-adapt">{{ $order->created_at?->format('M d, Y h:i A') }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-white/10 dark:bg-slate-900/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Payment method</p>
                        <p class="mt-2 text-sm font-semibold text-adapt">{{ ucfirst(str_replace('_', ' ', $order->payment_method ?: 'not set')) }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-white/10 dark:bg-slate-900/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Gateway</p>
                        <p class="mt-2 text-sm font-semibold text-adapt">{{ $order->payment_gateway ?: 'Manual / offline' }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-white/10 dark:bg-slate-900/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Subtotal</p>
                        <p class="mt-2 text-sm font-semibold text-adapt">Rs {{ number_format((float) $order->subtotal, 2) }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-white/10 dark:bg-slate-900/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Shipping fee</p>
                        <p class="mt-2 text-sm font-semibold text-adapt">Rs {{ number_format((float) $order->shipping_fee, 2) }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-white/10 dark:bg-slate-900/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Grand total</p>
                        <p class="mt-2 text-sm font-semibold text-adapt">Rs {{ number_format((float) $order->total, 2) }}</p>
                    </div>
                </div>
                @if($order->payment_reference || $order->payment_gateway_transaction_id || $order->notes)
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        @if($order->payment_reference)
                            <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-white/10 dark:bg-slate-900/70">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Payment reference</p>
                                <p class="mt-2 break-all text-sm font-semibold text-adapt">{{ $order->payment_reference }}</p>
                            </div>
                        @endif
                        @if($order->payment_gateway_transaction_id)
                            <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-white/10 dark:bg-slate-900/70">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Transaction ID</p>
                                <p class="mt-2 break-all text-sm font-semibold text-adapt">{{ $order->payment_gateway_transaction_id }}</p>
                            </div>
                        @endif
                    </div>
                    @if($order->notes)
                        <div class="mt-4 rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-white/10 dark:bg-slate-900/70">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Order note</p>
                            <p class="mt-2 text-sm leading-7 text-soft">{{ $order->notes }}</p>
                        </div>
                    @endif
                @endif
            </div>

            <div class="surface card-shadow rounded-[1.75rem] p-6">
                <h2 class="text-xl font-bold text-adapt">Items in this order</h2>
                <div class="mt-5 space-y-4">
                    @foreach($order->items as $item)
                        @php
                            $itemName = $item->product_name ?: ($item->stock?->name ?: 'Ordered item');
                            $itemPrice = (float) ($item->sale_price ?? $item->unit_price ?? 0);
                            $itemSubtotal = (float) ($item->subtotal ?? ($itemPrice * (int) $item->quantity));
                        @endphp
                        <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-white/10 dark:bg-slate-900/70">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-adapt">{{ $itemName }}</p>
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs text-soft">
                                        @if($item->product_sku)
                                            <span class="rounded-full bg-slate-100 px-3 py-1 dark:bg-slate-800">SKU: {{ $item->product_sku }}</span>
                                        @endif
                                        <span class="rounded-full bg-slate-100 px-3 py-1 dark:bg-slate-800">Qty {{ $item->quantity }}</span>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right">
                                    <p class="text-sm font-semibold text-adapt">Rs {{ number_format($itemSubtotal, 2) }}</p>
                                    <p class="mt-1 text-xs text-soft">Unit price: Rs {{ number_format($itemPrice, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="surface card-shadow rounded-[1.75rem] p-6">
                <h2 class="text-xl font-bold text-adapt">Tracking timeline</h2>
                <div class="mt-5 space-y-4">
                    @forelse($order->statusHistory as $history)
                        <div class="flex gap-4">
                            <div class="mt-1 h-3 w-3 rounded-full" style="background:var(--primary)"></div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-adapt">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</p>
                                <p class="mt-1 text-sm text-soft">{{ $history->note ?: 'Status updated by the team.' }}</p>
                                <div class="mt-1 flex flex-wrap gap-2 text-xs text-slate-400">
                                    <span>{{ $history->created_at?->format('M d, Y h:i A') }}</span>
                                    @if($history->changedBy)
                                        <span>by {{ $history->changedBy->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-sm text-soft">This order exists, but no timeline items have been recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="surface card-shadow rounded-[1.75rem] p-6">
                <h2 class="text-xl font-bold text-adapt">Customer details</h2>
                <div class="mt-5 space-y-4 text-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Name</p>
                        <p class="mt-2 font-semibold text-adapt">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Email</p>
                        <p class="mt-2 font-semibold text-adapt">{{ $order->customer_email }}</p>
                    </div>
                    @if($order->customer_phone)
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Phone</p>
                            <p class="mt-2 font-semibold text-adapt">{{ $order->customer_phone }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="surface card-shadow rounded-[1.75rem] p-6">
                <h2 class="text-xl font-bold text-adapt">Shipping details</h2>
                <div class="mt-5 space-y-4 text-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Address</p>
                        <p class="mt-2 leading-7 text-adapt">
                            {{ $order->shipping_address ?: 'No shipping address recorded.' }}
                            @if($order->shipping_city), {{ $order->shipping_city }}@endif
                            @if($order->shipping_postal_code), {{ $order->shipping_postal_code }}@endif
                            @if($order->shipping_country), {{ $order->shipping_country }}@endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Courier</p>
                        <p class="mt-2 font-semibold text-adapt">{{ $order->courier ?: 'Not assigned yet' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-soft">Tracking number</p>
                        <p class="mt-2 font-semibold text-adapt">{{ $order->tracking_number ?: 'Pending' }}</p>
                    </div>
                    @if($order->tracking_url)
                        <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">
                            Open courier tracking
                            <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                        </a>
                    @endif
                </div>
            </div>

            <div class="surface card-shadow rounded-[1.75rem] p-6">
                <h2 class="text-xl font-bold text-adapt">Need help?</h2>
                <p class="mt-3 text-sm leading-7 text-soft">If anything looks incorrect, use the help center or the support chat on the storefront and mention this order number.</p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <a href="{{ route('orders.invoice', $order) }}" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">Download Invoice</a>
                    <a href="{{ route('orders.receipt', $order) }}" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">Download Receipt</a>
                    <a wire:navigate href="{{ route('help-center') }}" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">Open Help Center</a>
                    <a wire:navigate href="{{ route('track-order', ['order_number' => $order->order_number, 'email' => $order->customer_email]) }}" class="btn-gradient rounded-full px-5 py-3 text-sm font-semibold">Open Public Tracker</a>
                </div>
            </div>

            <div class="surface card-shadow rounded-[1.75rem] p-6">
                <h2 class="text-xl font-bold text-adapt">Returns & refunds</h2>
                @if($canRequestReturn)
                    <p class="mt-3 text-sm leading-7 text-soft">This order is eligible for a return request. Share the reason and any details that will help the team review it faster.</p>
                    <form method="POST" action="{{ route('orders.return-request', $order) }}" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-soft">Reason</label>
                            <input type="text" name="return_reason" value="{{ old('return_reason') }}" class="field" placeholder="Wrong item, activation issue, duplicate purchase">
                            @error('return_reason')
                                <p class="mt-2 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-soft">Extra details</label>
                            <textarea name="return_notes" rows="4" class="field resize-none" placeholder="Add any extra explanation, account issue, or delivery problem">{{ old('return_notes') }}</textarea>
                            @error('return_notes')
                                <p class="mt-2 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white dark:bg-white dark:text-slate-950">Request return</button>
                    </form>
                @elseif($order->isReturnPending())
                    <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm leading-7 text-amber-800 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-200">
                        A return request is already under review for this order.
                        @if($order->return_reason)
                            <div class="mt-2 font-semibold">Reason: {{ $order->return_reason }}</div>
                        @endif
                    </div>
                @elseif(in_array($order->status, ['returned', 'refunded'], true))
                    <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm leading-7 text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-200">
                        This order has already completed the return or refund flow.
                    </div>
                @else
                    <div class="mt-4 rounded-2xl border border-slate-200 bg-white/80 p-4 text-sm leading-7 text-soft dark:border-white/10 dark:bg-slate-900/70">
                        Return requests become available after the order reaches a delivered or completed state.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
