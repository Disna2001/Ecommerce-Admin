@extends('layouts.admin')

@section('header', 'Dashboard')
@section('breadcrumb', 'Operations Overview')

@section('content')
@php
    $user = auth()->user();
    $todayOrders = \App\Models\Order::whereDate('created_at', today())->count();
    $pendingOrders = \App\Models\Order::whereIn('status', ['pending', 'confirmed'])->count();
    $pendingPaymentReviews = \App\Models\Order::where('payment_review_status', 'pending_review')->count();
    $monthRevenue = \App\Models\Order::whereIn('status', ['completed', 'delivered'])
        ->whereMonth('created_at', now()->month)
        ->sum('total');
    $lowStockCount = \App\Models\Stock::whereColumn('quantity', '<=', 'reorder_level')->count();
    $recentActivityLogs = \App\Models\AdminActivityLog::with('user')
        ->latest()
        ->take(6)
        ->get();
    $failedOutbox = \App\Models\NotificationOutbox::where('status', 'failed')->count();
    $queuedOutbox = \App\Models\NotificationOutbox::where('status', 'queued')->count();
    $sentOutboxToday = \App\Models\NotificationOutbox::where('status', 'sent')
        ->whereDate('sent_at', today())
        ->count();
    $todayStockOut = \App\Models\StockMovementLog::whereDate('created_at', today())
        ->where('direction', 'out')
        ->sum('quantity');
    $todayStockIn = \App\Models\StockMovementLog::whereDate('created_at', today())
        ->where('direction', 'in')
        ->sum('quantity');
    $todayReversals = \App\Models\StockMovementLog::whereDate('created_at', today())
        ->whereIn('context', ['order_cancelled', 'invoice_cancelled', 'refunded', 'returned'])
        ->count();
    $attentionItems = collect([
        ['title' => 'Payment review queue', 'count' => $pendingPaymentReviews, 'route' => route('admin.orders'), 'tone' => 'emerald', 'icon' => 'fa-money-check-dollar'],
        ['title' => 'Failed notifications', 'count' => $failedOutbox, 'route' => route('admin.notification-outbox'), 'tone' => 'rose', 'icon' => 'fa-triangle-exclamation'],
        ['title' => 'Low stock items', 'count' => $lowStockCount, 'route' => route('admin.stocks'), 'tone' => 'amber', 'icon' => 'fa-box-open'],
        ['title' => 'Queued deliveries', 'count' => $queuedOutbox, 'route' => route('admin.notification-outbox'), 'tone' => 'indigo', 'icon' => 'fa-paper-plane'],
    ])->filter(fn ($item) => $item['count'] > 0)->take(4);


    $quickActions = collect([
        ['title' => 'Review Orders', 'desc' => 'Process pending and confirmed orders.', 'route' => route('admin.orders'), 'show' => $user->can('view orders'), 'icon' => 'fa-receipt'],
        ['title' => 'Update Storefront', 'desc' => 'Change homepage content, banners, and branding.', 'route' => route('admin.site-management.appearance'), 'show' => $user->can('view site management'), 'icon' => 'fa-wand-magic-sparkles'],
        ['title' => 'Check Stock Ledger', 'desc' => 'Trace every stock in, stock out, and reversal.', 'route' => route('admin.stock-movements'), 'show' => $user->can('view stock movements'), 'icon' => 'fa-arrow-right-arrow-left'],
        ['title' => 'Adjust Settings', 'desc' => 'Manage email, WhatsApp, and AI configuration.', 'route' => route('admin.settings'), 'show' => $user->can('view settings'), 'icon' => 'fa-sliders'],
    ])->filter(fn ($item) => $item['show']);

@endphp

<div class="min-h-screen bg-[#F8FAFC] p-4 lg:p-8">
    <div class="mx-auto max-w-[1600px] space-y-8">
        <!-- Dashboard Header Narrative -->
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-10">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-white shadow-lg">
                        <i class="fas fa-terminal text-[10px]"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Operations Console</p>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-slate-900">Administrative Overview</h1>
                <p class="mt-2 text-sm font-bold text-slate-500">Welcome back, {{ $user->name }}. Your marketplace protocols are currently synchronized.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl shadow-sm border border-slate-200">
                    <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-900">System Live</span>
                </div>
            </div>
        </div>

        <x-admin.dashboard.hero
            :today-orders="$todayOrders"
            :pending-payment-reviews="$pendingPaymentReviews"
            :month-revenue="$monthRevenue"
            :failed-outbox="$failedOutbox"
        />

        <div class="grid gap-8 lg:grid-cols-12">
            <div class="lg:col-span-7 space-y-8">
                <x-admin.dashboard.attention :attention-items="$attentionItems" />
                <x-admin.dashboard.quick-actions :quick-actions="$quickActions" />
                <x-admin.dashboard.pulse
                    :today-stock-out="$todayStockOut"
                    :today-stock-in="$todayStockIn"
                    :today-reversals="$todayReversals"
                    :sent-outbox-today="$sentOutboxToday"
                />
            </div>

            <div class="lg:col-span-5 space-y-8">
                <x-admin.dashboard.comms-status :items="[
                    ['Email Delivery', \App\Models\SiteSetting::get('mail_from_address') ?: 'Not configured', 'fa-envelope', \App\Models\SiteSetting::get('mail_from_address') ? 'text-emerald-600' : 'text-amber-600'],
                    ['WhatsApp Automation', \App\Models\SiteSetting::get('whatsapp_enabled', false) ? 'Enabled' : 'Disabled', 'fa-comment-dots', \App\Models\SiteSetting::get('whatsapp_enabled', false) ? 'text-emerald-600' : 'text-slate-400'],
                    ['AI Assistant', \App\Models\SiteSetting::get('ai_enabled', true) ? (\App\Models\SiteSetting::get('ai_model', 'gpt-3.5-turbo')) : 'Disabled', 'fa-robot', \App\Models\SiteSetting::get('ai_enabled', true) ? 'text-emerald-600' : 'text-slate-400'],
                    ['Queue Driver', config('queue.default', 'sync'), 'fa-clock', config('queue.default', 'sync') === 'sync' ? 'text-amber-600' : 'text-emerald-600'],
                    ['Outbox Queue', $queuedOutbox . ' queued / ' . $failedOutbox . ' failed', 'fa-inbox', $failedOutbox > 0 ? 'text-rose-600' : 'text-emerald-600'],
                ]" />
                <x-admin.dashboard.recent-activity :recent-activity-logs="$recentActivityLogs" />
            </div>
        </div>
    </div>
</div>
@endsection
