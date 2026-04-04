@extends('layouts.shop')
@section('title', 'Help Center')
@section('content')
@php($supportEmail = \App\Models\SiteSetting::get('support_email', \App\Models\SiteSetting::get('support_notification_email', '')))
@php($supportPhone = \App\Models\SiteSetting::get('support_phone', ''))
<div class="mx-auto max-w-6xl py-8">
    <nav class="mb-6 flex items-center gap-2 text-sm text-slate-400">
        <a wire:navigate href="/" class="hover:text-slate-700 dark:hover:text-slate-100">Home</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="font-medium text-slate-700 dark:text-slate-100">Help Center</span>
    </nav>

    <div class="glass card-shadow rounded-[2rem] px-6 py-8">
        <p class="text-xs font-semibold uppercase tracking-[0.28em]" style="color:var(--primary)">Support Hub</p>
        <h1 class="mt-3 text-4xl font-black text-adapt">Help customers move faster.</h1>
        <p class="mt-4 max-w-3xl text-sm leading-7 text-soft">Use this page for common purchase questions, payment guidance, order tracking help, and support contact routes when customers need assistance.</p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a wire:navigate href="{{ route('track-order') }}" class="btn-gradient rounded-full px-6 py-3 text-sm font-semibold">Track an Order</a>
            <a wire:navigate href="{{ route('products.index') }}" class="rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">Browse Products</a>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="space-y-6">
            <div class="surface card-shadow rounded-[1.75rem] p-6">
                <h2 class="text-xl font-bold text-adapt">Frequently Asked Questions</h2>
                <div class="mt-5 space-y-4">
                    @foreach([
                        ['How do I know if my order was placed?', 'After checkout, the site creates your order immediately and sends an email update to the address you entered.'],
                        ['What happens for bank or online payments?', 'You can submit your reference and proof during checkout. The admin team reviews it before progressing the order.'],
                        ['Can I track my order status?', 'Yes. Use the order tracking page with your order number and email to see the latest recorded status.'],
                        ['What if an item is out of stock?', 'You can save it in wishlist, and the site team can restock items from admin when inventory returns.'],
                    ] as [$question, $answer])
                        <div class="rounded-2xl border border-slate-200 bg-white/80 p-5 dark:border-white/10 dark:bg-slate-900/70">
                            <p class="text-sm font-semibold text-adapt">{{ $question }}</p>
                            <p class="mt-2 text-sm leading-7 text-soft">{{ $answer }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="surface card-shadow rounded-[1.75rem] p-6">
                <h2 class="text-xl font-bold text-adapt">Payment and order guidance</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-500/20 dark:bg-emerald-500/10">
                        <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300"><i class="fas fa-shield-check mr-2"></i>Checkout confidence</p>
                        <p class="mt-2 text-sm leading-7 text-emerald-700/90 dark:text-emerald-200/80">The storefront shows totals before order placement and records review notes for proof-based payments.</p>
                    </div>
                    <div class="rounded-2xl border border-violet-200 bg-violet-50 p-5 dark:border-violet-500/20 dark:bg-violet-500/10">
                        <p class="text-sm font-semibold text-violet-800 dark:text-violet-300"><i class="fas fa-envelope-open-text mr-2"></i>Status updates</p>
                        <p class="mt-2 text-sm leading-7 text-violet-700/90 dark:text-violet-200/80">Customers receive order progress emails at placement, verification, approval, shipping, and completion stages.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="surface card-shadow rounded-[1.75rem] p-6">
                <h2 class="text-xl font-bold text-adapt">Need direct help?</h2>
                <div class="mt-5 space-y-3">
                    @if(!empty($supportEmail))
                        <a href="mailto:{{ $supportEmail }}" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-4 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">
                            <span><i class="fas fa-envelope mr-3"></i>Email Support</span>
                            <span class="text-xs text-soft">{{ $supportEmail }}</span>
                        </a>
                    @endif
                    @if(!empty($supportPhone))
                        <a href="tel:{{ preg_replace('/\s+/', '', $supportPhone) }}" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-4 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">
                            <span><i class="fas fa-phone mr-3"></i>Call Support</span>
                            <span class="text-xs text-soft">{{ $supportPhone }}</span>
                        </a>
                    @endif
                    <a wire:navigate href="{{ route('track-order') }}" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-4 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">
                        <span><i class="fas fa-location-crosshairs mr-3"></i>Track Your Order</span>
                        <i class="fas fa-chevron-right text-xs text-soft"></i>
                    </a>
                    <a wire:navigate href="{{ route('products.index') }}" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-4 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">
                        <span><i class="fas fa-store mr-3"></i>Continue Shopping</span>
                        <i class="fas fa-chevron-right text-xs text-soft"></i>
                    </a>
                </div>
            </div>

            <div class="surface card-shadow rounded-[1.75rem] p-6">
                <h2 class="text-xl font-bold text-adapt">Best next actions</h2>
                <div class="mt-5 space-y-3 text-sm leading-7 text-soft">
                    <p><i class="fas fa-check-circle mr-2 text-emerald-500"></i>Use the tracking page if you already have an order number.</p>
                    <p><i class="fas fa-check-circle mr-2 text-emerald-500"></i>Use the cart and wishlist flows to save items before checkout.</p>
                    <p><i class="fas fa-check-circle mr-2 text-emerald-500"></i>Contact support if payment proof needs clarification.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
