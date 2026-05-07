@php
    $pendingOrders = \App\Models\Order::whereIn('status', ['pending', 'confirmed'])->count();
    $lowStockItems = \App\Models\Stock::whereColumn('quantity', '<=', 'reorder_level')->count();
    $pendingReviews = class_exists(\App\Models\Review::class) ? \App\Models\Review::where('is_approved', false)->count() : 0;
    $failedOutbox = \App\Models\NotificationOutbox::where('status', 'failed')->count();
@endphp

<aside 
    :class="sidebarOpen ? 'sidebar-visible' : 'sidebar-hidden'" 
    class="admin-sidebar sidebar-transition flex flex-col"
    x-data="{coreOpen:true,storeOpen:false,systemOpen:false}"
>
    <!-- Brand / Identity Zone -->
    <div class="p-8 pb-4">
        <div class="flex items-center gap-4 p-5 rounded-3xl bg-slate-900 shadow-xl shadow-slate-200 dark:shadow-none relative overflow-hidden group">
            <div class="absolute right-0 top-0 -mr-8 -mt-8 h-24 w-24 rounded-full bg-white/5 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10 flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 text-white shadow-inner">
                <i class="fas fa-layer-group text-lg"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[9px] font-black uppercase tracking-[0.4em] text-white/40 leading-none mb-1.5">Control Center</p>
                <h3 class="text-sm font-black tracking-tight text-white uppercase tracking-widest">Admin Workspace</h3>
            </div>
        </div>
    </div>

    <!-- Scrollable Navigation Registry -->
    <div class="flex-1 overflow-y-auto px-6 py-4">
        <div class="space-y-8">
            <!-- Core Operations -->
            @canany(['view dashboard','view orders','view inventory','view users'])
                <div class="space-y-3">
                    <p class="px-4 text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Core Engine</p>
                    <div class="space-y-1">
                        @can('view dashboard')
                            <x-admin.sidebar.link href="{{ route('admin.dashboard') }}" route="admin.dashboard" icon="fa-chart-pie">Dashboard</x-admin.sidebar.link>
                        @endcan
                        @can('view orders')
                            <x-admin.sidebar.link href="{{ route('admin.orders') }}" route="admin.orders" icon="fa-receipt" :badge="$pendingOrders > 0 ? $pendingOrders : null">Orders</x-admin.sidebar.link>
                        @endcan
                        @can('view inventory')
                            <x-admin.sidebar.link href="{{ route('admin.stocks') }}" route="admin.stocks" icon="fa-cubes" :badge="$lowStockItems > 0 ? $lowStockItems : null">Stock Ledger</x-admin.sidebar.link>
                        @endcan
                        @can('view users')
                            <x-admin.sidebar.link href="{{ route('admin.users') }}" route="admin.users" icon="fa-user-astronaut">Users</x-admin.sidebar.link>
                        @endcan
                    </div>
                </div>
            @endcanany

            <!-- Storefront Architecture -->
            @can('view site management')
                <div class="space-y-3">
                    <p class="px-4 text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Storefront</p>
                    <div class="space-y-1">
                        <x-admin.sidebar.link href="{{ route('admin.site-management.index') }}" route="admin.site-management.index" icon="fa-store">Hub Overview</x-admin.sidebar.link>
                        <x-admin.sidebar.link href="{{ route('admin.site-management.appearance') }}" route="admin.site-management.appearance" icon="fa-palette">Appearance</x-admin.sidebar.link>
                        <x-admin.sidebar.link href="{{ route('admin.site-management.banners') }}" route="admin.site-management.banners" icon="fa-image">Banners</x-admin.sidebar.link>
                        <x-admin.sidebar.link href="{{ route('admin.site-management.automated-discounts') }}" route="admin.site-management.automated-discounts" icon="fa-bolt">Discount Hub</x-admin.sidebar.link>
                        <x-admin.sidebar.link href="{{ route('admin.site-management.reviews') }}" route="admin.site-management.reviews" icon="fa-star" :badge="$pendingReviews > 0 ? $pendingReviews : null">Reviews</x-admin.sidebar.link>
                    </div>
                </div>
            @endcan

            <!-- System Protocols -->
            @canany(['view system health','view notification outbox','view settings','view roles'])
                <div class="space-y-3">
                    <p class="px-4 text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Protocols</p>
                    <div class="space-y-1">
                        @can('view system health')
                            <x-admin.sidebar.link href="{{ route('admin.system-health') }}" route="admin.system-health" icon="fa-heart-pulse">System Pulse</x-admin.sidebar.link>
                        @endcan
                        @can('view notification outbox')
                            <x-admin.sidebar.link href="{{ route('admin.notification-outbox') }}" route="admin.notification-outbox" icon="fa-paper-plane" :badge="$failedOutbox > 0 ? $failedOutbox : null">Outbox</x-admin.sidebar.link>
                        @endcan
                        @can('view roles')
                            <x-admin.sidebar.link href="{{ route('admin.roles') }}" route="admin.roles" icon="fa-shield-halved">Access Roles</x-admin.sidebar.link>
                        @endcan
                        @can('view settings')
                            <x-admin.sidebar.link href="{{ route('admin.settings') }}" route="admin.settings" icon="fa-sliders">Global Settings</x-admin.sidebar.link>
                        @endcan
                    </div>
                </div>
            @endcanany
        </div>
    </div>

    <!-- Quick Insights Footer -->
    <div class="p-6">
        <div class="rounded-3xl bg-slate-50 dark:bg-slate-800/50 p-4 border border-slate-200/50 dark:border-slate-700/50">
            <a href="{{ url('/') }}" target="_blank" class="flex items-center justify-between group">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white dark:bg-slate-700 shadow-sm text-slate-900 dark:text-white group-hover:scale-110 transition-transform">
                        <i class="fas fa-external-link-alt text-[10px]"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-900 dark:text-white">Live Store</p>
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter mt-0.5">View Frontend</p>
                    </div>
                </div>
                <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
            </a>
        </div>
    </div>
</aside>
