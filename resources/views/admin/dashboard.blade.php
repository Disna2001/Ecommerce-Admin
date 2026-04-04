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

    $enabledModules = collect([
        ['title' => 'Orders', 'desc' => 'Review sales, fulfillment, and payment checks.', 'route' => route('admin.orders'), 'permission' => 'view orders', 'icon' => 'fa-shopping-bag'],
        ['title' => 'Inventory', 'desc' => 'Manage stock, categories, brands, and item setup.', 'route' => route('admin.stocks'), 'permission' => 'view inventory', 'icon' => 'fa-boxes-stacked'],
        ['title' => 'Stock Ledger', 'desc' => 'Inspect inventory movement history and reversals.', 'route' => route('admin.stock-movements'), 'permission' => 'view stock movements', 'icon' => 'fa-arrow-right-arrow-left'],
        ['title' => 'Site Management', 'desc' => 'Control storefront design, payments, and display content.', 'route' => route('admin.site-management.index'), 'permission' => 'view site management', 'icon' => 'fa-store'],
        ['title' => 'Settings', 'desc' => 'Configure email, WhatsApp automation, and AI controls.', 'route' => route('admin.settings'), 'permission' => 'view settings', 'icon' => 'fa-sliders'],
        ['title' => 'System Health', 'desc' => 'Track queue readiness, delivery health, and production checks.', 'route' => route('admin.system-health'), 'permission' => 'view system health', 'icon' => 'fa-heart-pulse'],
        ['title' => 'Activity Logs', 'desc' => 'Review the admin audit trail and recent control actions.', 'route' => route('admin.activity-logs'), 'permission' => 'view activity logs', 'icon' => 'fa-chart-line'],
        ['title' => 'Notification Outbox', 'desc' => 'Retry failed email and WhatsApp deliveries from one queue view.', 'route' => route('admin.notification-outbox'), 'permission' => 'view notification outbox', 'icon' => 'fa-inbox'],
        ['title' => 'Users', 'desc' => 'Manage staff access and operational permissions.', 'route' => route('admin.users'), 'permission' => 'view users', 'icon' => 'fa-users-cog'],
    ])->filter(fn ($module) => auth()->user()->can($module['permission']));

    $quickActions = collect([
        ['title' => 'Review Orders', 'desc' => 'Process pending and confirmed orders.', 'route' => route('admin.orders'), 'show' => $user->can('view orders'), 'icon' => 'fa-receipt'],
        ['title' => 'Retry Deliveries', 'desc' => 'Inspect failed emails and WhatsApp jobs.', 'route' => route('admin.notification-outbox'), 'show' => $user->can('view notification outbox'), 'icon' => 'fa-rotate-right'],
        ['title' => 'Check Stock Ledger', 'desc' => 'Trace every stock in, stock out, and reversal.', 'route' => route('admin.stock-movements'), 'show' => $user->can('view stock movements'), 'icon' => 'fa-arrow-right-arrow-left'],
        ['title' => 'Production Health', 'desc' => 'Check queue readiness, config safety, and storage health.', 'route' => route('admin.system-health'), 'show' => $user->can('view system health'), 'icon' => 'fa-heart-pulse'],
        ['title' => 'Update Storefront', 'desc' => 'Change homepage content, banners, and branding.', 'route' => route('admin.site-management.appearance'), 'show' => $user->can('view site management'), 'icon' => 'fa-wand-magic-sparkles'],
        ['title' => 'Adjust Settings', 'desc' => 'Manage email, WhatsApp, and AI configuration.', 'route' => route('admin.settings'), 'show' => $user->can('view settings'), 'icon' => 'fa-sliders'],
        ['title' => 'Audit Activity', 'desc' => 'Trace who changed orders, invoices, settings, and more.', 'route' => route('admin.activity-logs'), 'show' => $user->can('view activity logs'), 'icon' => 'fa-clock-rotate-left'],
        ['title' => 'Manage Team', 'desc' => 'Review users, roles, and page permissions.', 'route' => route('admin.roles'), 'show' => $user->can('view roles'), 'icon' => 'fa-user-shield'],
    ])->filter(fn ($item) => $item['show']);

@endphp

<div class="space-y-6">
    <x-admin.dashboard.hero
        :today-orders="$todayOrders"
        :pending-payment-reviews="$pendingPaymentReviews"
        :month-revenue="$monthRevenue"
        :failed-outbox="$failedOutbox"
    />

    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="space-y-6">
            <x-admin.dashboard.attention :attention-items="$attentionItems" />
            <x-admin.dashboard.quick-actions :quick-actions="$quickActions" />
            <x-admin.dashboard.modules :enabled-modules="$enabledModules" />
            <x-admin.dashboard.priorities
                :pending-orders="$pendingOrders"
                :pending-payment-reviews="$pendingPaymentReviews"
                :low-stock-count="$lowStockCount"
            />
            <x-admin.dashboard.pulse
                :today-stock-out="$todayStockOut"
                :today-stock-in="$todayStockIn"
                :today-reversals="$todayReversals"
                :sent-outbox-today="$sentOutboxToday"
            />
        </div>

        <div class="space-y-6">
            <x-admin.dashboard.focus :user="$user" />
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
@endsection
