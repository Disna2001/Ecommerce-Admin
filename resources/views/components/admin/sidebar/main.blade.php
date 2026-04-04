@php
    $pendingOrders = \App\Models\Order::whereIn('status', ['pending', 'confirmed'])->count();
    $lowStockItems = \App\Models\Stock::whereColumn('quantity', '<=', 'reorder_level')->count();
    $pendingReviews = class_exists(\App\Models\Review::class) ? \App\Models\Review::where('is_approved', false)->count() : 0;
    $failedOutbox = \App\Models\NotificationOutbox::where('status', 'failed')->count();
@endphp

<aside :class="sidebarOpen ? 'sidebar-visible' : 'sidebar-hidden'" class="admin-sidebar sidebar-transition scrollbar-custom"
    x-data="{coreOpen:true,storeOpen:false,systemOpen:false}">

    <div class="admin-sidebar-shell">
        <div class="admin-sidebar-brand">
            <div class="admin-sidebar-brand__icon">
                <svg style="width:1.05rem;height:1.05rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7.5h16M4 12h10m-10 4.5h16"></path>
                </svg>
            </div>
            <div>
                <p class="admin-sidebar-eyebrow">Navigation</p>
                <h3 class="admin-sidebar-title">Admin</h3>
            </div>
        </div>

        <div class="admin-sidebar-shortcuts">
            @can('view dashboard')
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="admin-sidebar-shortcut"><x-admin.icon name="fa-house" class="h-4 w-4" /><span>Home</span></a>
            @endcan
            @can('view orders')
                <a href="{{ route('admin.orders') }}" wire:navigate class="admin-sidebar-shortcut"><x-admin.icon name="fa-receipt" class="h-4 w-4" /><span>Orders</span></a>
            @endcan
            @can('view inventory')
                <a href="{{ route('admin.stocks') }}" wire:navigate class="admin-sidebar-shortcut"><x-admin.icon name="fa-box-open" class="h-4 w-4" /><span>Stock</span></a>
            @endcan
            @can('view site management')
                <a href="{{ route('admin.site-management.index') }}" wire:navigate class="admin-sidebar-shortcut"><x-admin.icon name="fa-store" class="h-4 w-4" /><span>Store</span></a>
            @endcan
        </div>

        <div class="admin-sidebar-insights" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
            <div class="admin-sidebar-insight"><span class="admin-sidebar-insight__label">Orders</span><span class="admin-sidebar-insight__value">{{ $pendingOrders }}</span></div>
            <div class="admin-sidebar-insight"><span class="admin-sidebar-insight__label">Low Stock</span><span class="admin-sidebar-insight__value">{{ $lowStockItems }}</span></div>
        </div>

        <div class="admin-sidebar-groups">
            @canany(['view dashboard','view orders','view inventory','view users'])
                <section class="admin-nav-group">
                    <button @click="coreOpen = !coreOpen" class="admin-nav-group__toggle">
                        <span class="admin-nav-group__left">
                            <span class="admin-nav-group__icon admin-nav-group__icon--violet"><x-admin.icon name="fa-play" class="h-4 w-4" /></span>
                            <span><span class="admin-nav-group__title">Core</span><span class="admin-nav-group__hint">Daily essentials</span></span>
                        </span>
                        <span class="text-slate-400">
                            <svg x-show="coreOpen" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m18 15-6-6-6 6"></path></svg>
                            <svg x-show="!coreOpen" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 9 6 6 6-6"></path></svg>
                        </span>
                    </button>
                    <div x-show="coreOpen" x-transition class="admin-nav-links">
                        @can('view dashboard')
                            <x-admin.sidebar.link href="{{ route('admin.dashboard') }}" route="admin.dashboard" icon="dashboard">Dashboard</x-admin.sidebar.link>
                        @endcan
                        @can('view orders')
                            <x-admin.sidebar.link href="{{ route('admin.orders') }}" route="admin.orders" icon="orders" :badge="$pendingOrders > 0 ? $pendingOrders : null">Orders</x-admin.sidebar.link>
                        @endcan
                        @can('view inventory')
                            <x-admin.sidebar.link href="{{ route('admin.stocks') }}" route="admin.stocks" icon="stock" :badge="$lowStockItems > 0 ? $lowStockItems : null">Stock</x-admin.sidebar.link>
                        @endcan
                        @can('view users')
                            <x-admin.sidebar.link href="{{ route('admin.users') }}" route="admin.users" icon="users">Users</x-admin.sidebar.link>
                        @endcan
                    </div>
                </section>
            @endcanany

            @can('view site management')
                <section class="admin-nav-group">
                    <button @click="storeOpen = !storeOpen" class="admin-nav-group__toggle">
                        <span class="admin-nav-group__left">
                            <span class="admin-nav-group__icon admin-nav-group__icon--pink"><x-admin.icon name="fa-store" class="h-4 w-4" /></span>
                            <span><span class="admin-nav-group__title">Store</span><span class="admin-nav-group__hint">Public-facing setup</span></span>
                        </span>
                        <span class="text-slate-400">
                            <svg x-show="storeOpen" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m18 15-6-6-6 6"></path></svg>
                            <svg x-show="!storeOpen" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 9 6 6 6-6"></path></svg>
                        </span>
                    </button>
                    <div x-show="storeOpen" x-transition class="admin-nav-links">
                        <x-admin.sidebar.link href="{{ route('admin.site-management.index') }}" route="admin.site-management.index" icon="storefront">Overview</x-admin.sidebar.link>
                        <x-admin.sidebar.link href="{{ route('admin.site-management.appearance') }}" route="admin.site-management.appearance" icon="appearance">Appearance</x-admin.sidebar.link>
                        <x-admin.sidebar.link href="{{ route('admin.site-management.banners') }}" route="admin.site-management.banners" icon="spark">Banners</x-admin.sidebar.link>
                        <x-admin.sidebar.link href="{{ route('admin.site-management.discounts') }}" route="admin.site-management.discounts" icon="bolt">Discounts</x-admin.sidebar.link>
                        <x-admin.sidebar.link href="{{ route('admin.site-management.reviews') }}" route="admin.site-management.reviews" icon="review" :badge="$pendingReviews > 0 ? $pendingReviews : null">Reviews</x-admin.sidebar.link>
                    </div>
                </section>
            @endcan

            @canany(['view system health','view notification outbox','view settings','view roles'])
                <section class="admin-nav-group">
                    <button @click="systemOpen = !systemOpen" class="admin-nav-group__toggle">
                        <span class="admin-nav-group__left">
                            <span class="admin-nav-group__icon admin-nav-group__icon--indigo"><x-admin.icon name="fa-user-shield" class="h-4 w-4" /></span>
                            <span><span class="admin-nav-group__title">System</span><span class="admin-nav-group__hint">Health and access</span></span>
                        </span>
                        <span class="text-slate-400">
                            <svg x-show="systemOpen" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m18 15-6-6-6 6"></path></svg>
                            <svg x-show="!systemOpen" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 9 6 6 6-6"></path></svg>
                        </span>
                    </button>
                    <div x-show="systemOpen" x-transition class="admin-nav-links">
                        @can('view system health')
                            <x-admin.sidebar.link href="{{ route('admin.system-health') }}" route="admin.system-health" icon="system-health">System Health</x-admin.sidebar.link>
                        @endcan
                        @can('view notification outbox')
                            <x-admin.sidebar.link href="{{ route('admin.notification-outbox') }}" route="admin.notification-outbox" icon="notification-outbox" :badge="$failedOutbox > 0 ? $failedOutbox : null">Outbox</x-admin.sidebar.link>
                        @endcan
                        @can('view roles')
                            <x-admin.sidebar.link href="{{ route('admin.roles') }}" route="admin.roles" icon="roles">Roles</x-admin.sidebar.link>
                        @endcan
                        @can('view settings')
                            <x-admin.sidebar.link href="{{ route('admin.settings') }}" route="admin.settings" icon="system">Settings</x-admin.sidebar.link>
                        @endcan
                    </div>
                </section>
            @endcanany
        </div>

        <div class="admin-sidebar-footer">
            <a href="{{ url('/') }}" wire:navigate class="admin-sidebar-footer__link"><x-admin.icon name="fa-arrow-up-right-from-square" class="h-4 w-4" /><span>Open Storefront</span></a>
        </div>
    </div>
</aside>
