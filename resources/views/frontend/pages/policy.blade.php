@extends('layouts.shop')
@section('title', $title)
@section('content')
@php($supportEmail = \App\Models\SiteSetting::get('support_email', \App\Models\SiteSetting::get('support_notification_email', '')))
@php($supportPhone = \App\Models\SiteSetting::get('support_phone', ''))
<div class="mx-auto max-w-6xl py-8">
    <nav class="mb-6 flex items-center gap-2 text-sm text-slate-400">
        <a wire:navigate href="/" class="hover:text-slate-700 dark:hover:text-slate-100">Home</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="font-medium text-slate-700 dark:text-slate-100">{{ $title }}</span>
    </nav>

    <div class="glass card-shadow rounded-[2rem] px-6 py-8">
        <p class="text-xs font-semibold uppercase tracking-[0.28em]" style="color:var(--primary)">{{ $eyebrow }}</p>
        <h1 class="mt-3 text-4xl font-black text-adapt">{{ $title }}</h1>
        <p class="mt-4 max-w-3xl text-sm leading-7 text-soft">{{ $intro }}</p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a wire:navigate href="{{ route('help-center') }}" class="btn-gradient rounded-full px-6 py-3 text-sm font-semibold">Open Help Center</a>
            <a wire:navigate href="{{ route('track-order') }}" class="rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">Track an Order</a>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="space-y-6">
            @foreach($sections as $section)
                <div class="surface card-shadow rounded-[1.75rem] p-6">
                    <h2 class="text-xl font-bold text-adapt">{{ $section['title'] }}</h2>
                    <div class="mt-4 space-y-3 text-sm leading-7 text-soft">
                        @foreach($section['body'] as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="space-y-6">
            <div class="surface card-shadow rounded-[1.75rem] p-6">
                <h2 class="text-xl font-bold text-adapt">Need help with an order?</h2>
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
                    <a wire:navigate href="{{ route('refund-policy') }}" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-4 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">
                        <span><i class="fas fa-rotate-left mr-3"></i>Refund Policy</span>
                        <i class="fas fa-chevron-right text-xs text-soft"></i>
                    </a>
                    <a wire:navigate href="{{ route('privacy-policy') }}" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-4 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">
                        <span><i class="fas fa-user-shield mr-3"></i>Privacy Policy</span>
                        <i class="fas fa-chevron-right text-xs text-soft"></i>
                    </a>
                    <a wire:navigate href="{{ route('terms-and-conditions') }}" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-4 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100">
                        <span><i class="fas fa-file-contract mr-3"></i>Terms & Conditions</span>
                        <i class="fas fa-chevron-right text-xs text-soft"></i>
                    </a>
                </div>
            </div>

            <div class="surface card-shadow rounded-[1.75rem] p-6">
                <h2 class="text-xl font-bold text-adapt">Policy notes</h2>
                <div class="mt-5 space-y-3 text-sm leading-7 text-soft">
                    <p><i class="fas fa-circle-check mr-2 text-emerald-500"></i>These pages are published publicly so customers can review payment and purchase terms before checkout.</p>
                    <p><i class="fas fa-circle-check mr-2 text-emerald-500"></i>Keep policy wording aligned with how the business actually handles delivery, activation, support, and refunds.</p>
                    <p><i class="fas fa-circle-check mr-2 text-emerald-500"></i>Update these pages whenever your payment flow, product type, or customer support process changes.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
