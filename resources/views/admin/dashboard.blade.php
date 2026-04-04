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
    <div class="rounded-[2rem] bg-gradient-to-r from-slate-900 via-indigo-900 to-violet-700 p-6 text-white shadow-xl">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-white/60">Admin Operations Center</p>
                <h2 class="mt-3 text-3xl font-black">Run sales, delivery, stock, and control actions from one structured dashboard.</h2>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-white/75">
                    Focus first on what needs attention, then move into inventory, delivery health, storefront management, and AI-guided control tasks without hopping between disconnected pages.
                </p>
            </div>
            <div class="grid gap-3 sm:grid-cols-4">
                <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-white/60">Today</p>
                    <p class="mt-2 text-2xl font-black">{{ $todayOrders }}</p>
                    <p class="mt-1 text-xs text-white/60">Orders created</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-white/60">Payments</p>
                    <p class="mt-2 text-2xl font-black">{{ $pendingPaymentReviews }}</p>
                    <p class="mt-1 text-xs text-white/60">Awaiting review</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-white/60">Revenue</p>
                    <p class="mt-2 text-2xl font-black">Rs {{ number_format($monthRevenue, 0) }}</p>
                    <p class="mt-1 text-xs text-white/60">This month</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-white/60">Outbox</p>
                    <p class="mt-2 text-2xl font-black">{{ $failedOutbox }}</p>
                    <p class="mt-1 text-xs text-white/60">Failed deliveries</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="space-y-6">
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600">
                        <x-admin.icon name="fa-siren-on" />
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-slate-900">Needs Attention</h3>
                        <p class="mt-1 text-sm text-slate-500">Priority queues that should be checked before routine admin work.</p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    @forelse($attentionItems as $item)
                        <a href="{{ $item['route'] }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-white">
                            <div class="flex items-start gap-4">
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl
                                    {{ $item['tone'] === 'rose' ? 'bg-rose-100 text-rose-600' : ($item['tone'] === 'emerald' ? 'bg-emerald-100 text-emerald-600' : ($item['tone'] === 'amber' ? 'bg-amber-100 text-amber-600' : 'bg-indigo-100 text-indigo-600')) }}">
                                    <x-admin.icon :name="$item['icon']" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $item['title'] }}</p>
                                    <p class="mt-2 text-3xl font-black text-slate-900">{{ $item['count'] }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-5 text-sm text-slate-500">
                            No urgent admin queues need attention right now.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                            <x-admin.icon name="fa-bolt" />
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-slate-900">Quick Actions</h3>
                            <p class="mt-1 text-sm text-slate-500">Jump into the most common admin tasks without digging through the menu.</p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($quickActions as $action)
                        <a href="{{ $action['route'] }}" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50">
                            <div class="flex items-start gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                                    <x-admin.icon :name="$action['icon']" />
                                </div>
                                <div>
                                    <p class="text-base font-semibold text-slate-900">{{ $action['title'] }}</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-500">{{ $action['desc'] }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-white">
                            <x-admin.icon name="fa-layer-group" />
                        </div>
                        <div>
                        <h3 class="text-xl font-semibold text-slate-900">Functional Modules</h3>
                        <p class="mt-1 text-sm text-slate-500">Only modules you have permission to access are shown here.</p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    @forelse($enabledModules as $module)
                        <a href="{{ $module['route'] }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-5 transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-white">
                            <div class="flex items-start gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-white">
                                    <x-admin.icon :name="$module['icon']" />
                                </div>
                                <div>
                                    <p class="text-base font-semibold text-slate-900">{{ $module['title'] }}</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-500">{{ $module['desc'] }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500">
                            No admin modules are assigned to your current permission set.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                        <x-admin.icon name="fa-triangle-exclamation" />
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-slate-900">Operational Priorities</h3>
                        <p class="mt-1 text-sm text-slate-500">The highest-signal tasks to check first.</p>
                    </div>
                </div>
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                        <p class="text-sm font-semibold text-amber-900">Pending Orders</p>
                        <p class="mt-2 text-3xl font-black text-amber-700">{{ $pendingOrders }}</p>
                        <p class="mt-2 text-sm text-amber-800">Orders still waiting for confirmation or processing.</p>
                    </div>
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                        <p class="text-sm font-semibold text-emerald-900">Payment Reviews</p>
                        <p class="mt-2 text-3xl font-black text-emerald-700">{{ $pendingPaymentReviews }}</p>
                        <p class="mt-2 text-sm text-emerald-800">Submitted proofs waiting for verification.</p>
                    </div>
                    <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
                        <p class="text-sm font-semibold text-rose-900">Low Stock</p>
                        <p class="mt-2 text-3xl font-black text-rose-700">{{ $lowStockCount }}</p>
                        <p class="mt-2 text-sm text-rose-800">Products at or below reorder threshold.</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
                        <x-admin.icon name="fa-arrow-right-arrow-left" />
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-slate-900">Stock & Delivery Pulse</h3>
                        <p class="mt-1 text-sm text-slate-500">A quick read on movement volume and notification throughput today.</p>
                    </div>
                </div>
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4">
                        <p class="text-sm font-semibold text-sky-900">Stock Out</p>
                        <p class="mt-2 text-3xl font-black text-sky-700">{{ $todayStockOut }}</p>
                        <p class="mt-2 text-sm text-sky-800">Units deducted today.</p>
                    </div>
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                        <p class="text-sm font-semibold text-emerald-900">Restocks</p>
                        <p class="mt-2 text-3xl font-black text-emerald-700">{{ $todayStockIn }}</p>
                        <p class="mt-2 text-sm text-emerald-800">Units added back today.</p>
                    </div>
                    <div class="rounded-2xl border border-violet-200 bg-violet-50 p-4">
                        <p class="text-sm font-semibold text-violet-900">Reversals</p>
                        <p class="mt-2 text-3xl font-black text-violet-700">{{ $todayReversals }}</p>
                        <p class="mt-2 text-sm text-violet-800">Cancellations and returns.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-semibold text-slate-900">Delivered Messages</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $sentOutboxToday }}</p>
                        <p class="mt-2 text-sm text-slate-600">Outbox entries sent today.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50 text-violet-600">
                        <x-admin.icon name="fa-user-shield" />
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-slate-900">Admin Focus</h3>
                        <p class="mt-1 text-sm text-slate-500">Profile-level access and shortcuts for the current administrator.</p>
                    </div>
                </div>
                <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach($user->getRoleNames() as $role)
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 shadow-sm">{{ $role }}</span>
                        @endforeach
                    </div>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <a href="{{ route('profile.index', ['tab' => 'settings']) }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300">
                            <span class="flex items-center gap-3">
                                <x-admin.icon name="fa-user-gear" class="h-4 w-4 text-indigo-500" />
                                <span>Profile Settings</span>
                            </span>
                        </a>
                        <a href="{{ route('admin.settings') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300">
                            <span class="flex items-center gap-3">
                                <x-admin.icon name="fa-sliders" class="h-4 w-4 text-violet-500" />
                                <span>Admin Controls</span>
                            </span>
                        </a>
                        @can('view activity logs')
                            <a href="{{ route('admin.activity-logs') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300">
                                <span class="flex items-center gap-3">
                                    <x-admin.icon name="fa-clock-rotate-left" class="h-4 w-4 text-rose-500" />
                                    <span>Activity Logs</span>
                                </span>
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                        <x-admin.icon name="fa-satellite-dish" />
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-slate-900">Comms & AI Status</h3>
                        <p class="mt-1 text-sm text-slate-500">Monitor automation systems and assistant readiness.</p>
                    </div>
                </div>
                <div class="mt-5 space-y-4">
                    @foreach([
                        ['Email Delivery', \App\Models\SiteSetting::get('mail_from_address') ?: 'Not configured', 'fa-envelope', \App\Models\SiteSetting::get('mail_from_address') ? 'text-emerald-600' : 'text-amber-600'],
                        ['WhatsApp Automation', \App\Models\SiteSetting::get('whatsapp_enabled', false) ? 'Enabled' : 'Disabled', 'fa-comment-dots', \App\Models\SiteSetting::get('whatsapp_enabled', false) ? 'text-emerald-600' : 'text-slate-400'],
                        ['AI Assistant', \App\Models\SiteSetting::get('ai_enabled', true) ? (\App\Models\SiteSetting::get('ai_model', 'gpt-3.5-turbo')) : 'Disabled', 'fa-robot', \App\Models\SiteSetting::get('ai_enabled', true) ? 'text-emerald-600' : 'text-slate-400'],
                        ['Queue Driver', config('queue.default', 'sync'), 'fa-clock', config('queue.default', 'sync') === 'sync' ? 'text-amber-600' : 'text-emerald-600'],
                        ['Outbox Queue', $queuedOutbox . ' queued / ' . $failedOutbox . ' failed', 'fa-inbox', $failedOutbox > 0 ? 'text-rose-600' : 'text-emerald-600'],
                    ] as [$label, $value, $icon, $color])
                        <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white">
                                <x-admin.icon :name="$icon" class="h-5 w-5 {{ $color }}" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $label }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $value }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600">
                        <x-admin.icon name="fa-clock-rotate-left" />
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-slate-900">Recent Admin Activity</h3>
                        <p class="mt-1 text-sm text-slate-500">A quick read on the latest workflow changes across the system.</p>
                    </div>
                </div>
                <div class="mt-5 space-y-4">
                    @forelse($recentActivityLogs as $activity)
                        <div class="flex gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="mt-1 h-3 w-3 rounded-full
                                {{ str_contains($activity->action, 'deleted') || str_contains($activity->action, 'cancelled') ? 'bg-rose-500' : (str_contains($activity->action, 'payment') ? 'bg-emerald-500' : 'bg-indigo-500') }}">
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ \Illuminate\Support\Str::headline(str_replace('.', ' ', $activity->action)) }}</p>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $activity->description ?: 'An admin activity was recorded.' }}
                                    @if($activity->user)
                                        <span class="font-medium text-slate-600">by {{ $activity->user->name }}</span>
                                    @endif
                                </p>
                                <p class="mt-2 text-xs font-medium text-slate-400">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-5 text-sm text-slate-500">
                            No recent admin actions are available yet.
                        </div>
                    @endforelse
                </div>
                <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm font-medium text-slate-900">Access Model</p>
                    <p class="mt-2 text-sm leading-7 text-slate-500">
                        Page access is controlled by permissions, not just the Admin role. Use Roles & Permissions to decide exactly which modules each staff member can open.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
