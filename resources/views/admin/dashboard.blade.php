@extends('layouts.admin')

@section('header', 'Dashboard')
@section('breadcrumb', 'Overview')

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
        ['title' => 'Payment Review Queue', 'count' => $pendingPaymentReviews, 'route' => route('admin.orders'), 'tone' => 'emerald', 'icon' => 'fa-money-check-dollar'],
        ['title' => 'Failed Notifications', 'count' => $failedOutbox, 'route' => route('admin.notification-outbox'), 'tone' => 'rose', 'icon' => 'fa-triangle-exclamation'],
        ['title' => 'Low Stock Items', 'count' => $lowStockCount, 'route' => route('admin.stocks'), 'tone' => 'amber', 'icon' => 'fa-box-open'],
        ['title' => 'Queued Deliveries', 'count' => $queuedOutbox, 'route' => route('admin.notification-outbox'), 'tone' => 'indigo', 'icon' => 'fa-paper-plane'],
    ])->filter(fn ($item) => $item['count'] > 0)->take(4);

    $quickActions = collect([
        ['title' => 'Review Orders', 'desc' => 'Process pending and confirmed customer orders.', 'route' => route('admin.orders'), 'show' => $user->can('view orders'), 'icon' => 'fa-receipt'],
        ['title' => 'Storefront Studio', 'desc' => 'Manage page layouts, sections, and banners.', 'route' => route('admin.site-management.index'), 'show' => $user->can('view site management'), 'icon' => 'fa-wand-magic-sparkles'],
        ['title' => 'Stock Ledger', 'desc' => 'Track item arrivals, dispatches, and inventory movement.', 'route' => route('admin.stock-movements'), 'show' => $user->can('view stock movements'), 'icon' => 'fa-arrow-right-arrow-left'],
        ['title' => 'System Settings', 'desc' => 'Configure email, WhatsApp, and store parameters.', 'route' => route('admin.settings'), 'show' => $user->can('view settings'), 'icon' => 'fa-sliders'],
    ])->filter(fn ($item) => $item['show']);
@endphp

<div class="min-h-screen bg-slate-50 p-4 lg:p-8">
    <div class="mx-auto max-w-[1600px] space-y-6">
        <!-- Single-line Dashboard Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Overview of orders, revenue, and system status</p>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-lg border border-slate-200 shadow-sm self-start sm:self-auto">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-semibold text-slate-700">System Live</span>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <x-admin.dashboard.hero
            :today-orders="$todayOrders"
            :pending-payment-reviews="$pendingPaymentReviews"
            :month-revenue="$monthRevenue"
            :failed-outbox="$failedOutbox"
        />

        <!-- Main Dashboard Content Grid -->
        <div class="grid gap-6 lg:grid-cols-12">
            <div class="lg:col-span-7 space-y-6">
                <x-admin.dashboard.attention :attention-items="$attentionItems" />
                <x-admin.dashboard.quick-actions :quick-actions="$quickActions" />
                <x-admin.dashboard.pulse
                    :today-stock-out="$todayStockOut"
                    :today-stock-in="$todayStockIn"
                    :today-reversals="$todayReversals"
                    :sent-outbox-today="$sentOutboxToday"
                />
            </div>

            <div class="lg:col-span-5 space-y-6">
                <x-admin.dashboard.comms-status :items="[
                    ['Email Delivery', \App\Models\SiteSetting::get('mail_from_address') ? 'Operational' : 'Not Configured', 'fa-envelope', \App\Models\SiteSetting::get('mail_from_address') ? 'text-emerald-600' : 'text-amber-600'],
                    ['WhatsApp Automation', \App\Models\SiteSetting::get('whatsapp_enabled', false) ? 'Operational' : 'Disabled', 'fa-comment-dots', \App\Models\SiteSetting::get('whatsapp_enabled', false) ? 'text-emerald-600' : 'text-amber-600'],
                    ['AI Assistant', \App\Models\SiteSetting::get('ai_enabled', true) ? 'Operational' : 'Disabled', 'fa-robot', \App\Models\SiteSetting::get('ai_enabled', true) ? 'text-emerald-600' : 'text-amber-600'],
                    ['Queue Driver', config('queue.default', 'sync') === 'sync' ? 'Sync Mode' : 'Queue Driver Active', 'fa-clock', config('queue.default', 'sync') === 'sync' ? 'text-amber-600' : 'text-emerald-600'],
                    ['Outbox Queue', $failedOutbox > 0 ? $failedOutbox . ' Failed Items' : ($queuedOutbox > 0 ? $queuedOutbox . ' Queued' : 'Operational'), 'fa-inbox', $failedOutbox > 0 ? 'text-rose-600' : ($queuedOutbox > 0 ? 'text-amber-600' : 'text-emerald-600')],
                ]" />
                <x-admin.dashboard.recent-activity :recent-activity-logs="$recentActivityLogs" />
            </div>
        </div>
    </div>
</div>
@endsection
