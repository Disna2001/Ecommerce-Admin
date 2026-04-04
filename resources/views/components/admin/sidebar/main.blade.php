@php
    $pendingOrders = \App\Models\Order::whereIn('status', ['pending', 'confirmed'])->count();
    $lowStockItems = \App\Models\Stock::whereColumn('quantity', '<=', 'reorder_level')->count();
    $pendingReviews = class_exists(\App\Models\Review::class) ? \App\Models\Review::where('is_approved', false)->count() : 0;
    $failedOutbox = \App\Models\NotificationOutbox::where('status', 'failed')->count();
    $queuedPayments = \App\Models\Order::where('payment_review_status', 'pending_review')->count();
@endphp

<aside :class="sidebarOpen ? 'sidebar-visible' : 'sidebar-hidden'" class="admin-sidebar sidebar-transition scrollbar-custom"
    x-data="{startOpen:true,commerceOpen:true,catalogOpen:true,experienceOpen:true,controlOpen:true}">

    <div class="admin-sidebar-shell">
        <div class="admin-sidebar-brand">
            <div class="admin-sidebar-brand__icon">
                <svg style="width:1.05rem;height:1.05rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7.5h16M4 12h10m-10 4.5h16"></path>
                </svg>
            </div>
            <div>
                <p class="admin-sidebar-eyebrow">Navigation Workspace</p>
                <h3 class="admin-sidebar-title">Admin Command Center</h3>
                <p class="admin-sidebar-copy">Start with urgent queues, then move into catalog, storefront, and system control.</p>
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

        <div class="admin-sidebar-insights">
            <div class="admin-sidebar-insight"><span class="admin-sidebar-insight__label">Orders</span><span class="admin-sidebar-insight__value">{{ $pendingOrders }}</span></div>
            <div class="admin-sidebar-insight"><span class="admin-sidebar-insight__label">Payments</span><span class="admin-sidebar-insight__value">{{ $queuedPayments }}</span></div>
            <div class="admin-sidebar-insight"><span class="admin-sidebar-insight__label">Low Stock</span><span class="admin-sidebar-insight__value">{{ $lowStockItems }}</span></div>
        </div>

        <div class="admin-sidebar-groups">
            @canany(['view dashboard','view orders','view pos','view system health'])
                <section class="admin-nav-group">
                    <button @click="startOpen = !startOpen" class="admin-nav-group__toggle">
                        <span class="admin-nav-group__left">
                            <span class="admin-nav-group__icon admin-nav-group__icon--violet"><x-admin.icon name="fa-play" class="h-4 w-4" /></span>
                            <span><span class="admin-nav-group__title">Start Here</span><span class="admin-nav-group__hint">What to check first today</span></span>
                        </span>
                        <span class="text-slate-400">
                            <svg x-show="startOpen" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m18 15-6-6-6 6"></path></svg>
                            <svg x-show="!startOpen" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 9 6 6 6-6"></path></svg>
                        </span>
                    </button>
                    <div x-show="startOpen" x-transition class="admin-nav-links">
                        @can('view dashboard')
                            <x-admin.sidebar.link href="{{ route('admin.dashboard') }}" route="admin.dashboard" icon="dashboard" description="Control-center overview and attention queues">Dashboard</x-admin.sidebar.link>
                        @endcan
                        @can('view orders')
                            <x-admin.sidebar.link href="{{ route('admin.orders') }}" route="admin.orders" icon="orders" :badge="$pendingOrders > 0 ? $pendingOrders : null" description="Customer orders, fulfillment, and payment review">Orders</x-admin.sidebar.link>
                        @endcan
                        @can('view pos')
                            <x-admin.sidebar.link href="{{ route('admin.pos') }}" route="admin.pos" icon="pos" description="Create a new sale at the counter fast">POS</x-admin.sidebar.link>
                        @endcan
                        @can('view system health')
                            <x-admin.sidebar.link href="{{ route('admin.system-health') }}" route="admin.system-health" icon="system-health" :badge="$failedOutbox > 0 ? $failedOutbox : null" description="Queue readiness and production checks">System Health</x-admin.sidebar.link>
                        @endcan
                    </div>
                </section>
            @endcanany

            @canany(['view orders','view invoices','view notification outbox','view activity logs','view stock movements'])
                <section class="admin-nav-group">
                    <button @click="commerceOpen = !commerceOpen" class="admin-nav-group__toggle">
                        <span class="admin-nav-group__left">
                            <span class="admin-nav-group__icon admin-nav-group__icon--emerald"><x-admin.icon name="fa-money-bill-trend-up" class="h-4 w-4" /></span>
                            <span><span class="admin-nav-group__title">Commerce Flow</span><span class="admin-nav-group__hint">Sales, delivery, billing, and audit</span></span>
                        </span>
                        <span class="text-slate-400">
                            <svg x-show="commerceOpen" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m18 15-6-6-6 6"></path></svg>
                            <svg x-show="!commerceOpen" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 9 6 6 6-6"></path></svg>
                        </span>
                    </button>
                    <div x-show="commerceOpen" x-transition class="admin-nav-links">
                        @can('view invoices')
                            <x-admin.sidebar.link href="{{ route('admin.invoices') }}" route="admin.invoices" icon="invoice" description="Invoice workspace and billing follow-up">Invoices</x-admin.sidebar.link>
                        @endcan
                        @can('view notification outbox')
                            <x-admin.sidebar.link href="{{ route('admin.notification-outbox') }}" route="admin.notification-outbox" icon="notification-outbox" :badge="$failedOutbox > 0 ? $failedOutbox : null" description="Retry and inspect delivery jobs">Notification Outbox</x-admin.sidebar.link>
                        @endcan
                        @can('view stock movements')
                            <x-admin.sidebar.link href="{{ route('admin.stock-movements') }}" route="admin.stock-movements" icon="stock-movements" description="Inventory ledger for stock movement tracing">Stock Movements</x-admin.sidebar.link>
                        @endcan
                        @can('view activity logs')
                            <x-admin.sidebar.link href="{{ route('admin.activity-logs') }}" route="admin.activity-logs" icon="activity-logs" description="Admin audit trail and exported activity history">Activity Logs</x-admin.sidebar.link>
                        @endcan
                    </div>
                </section>
            @endcanany

            @canany(['view inventory','view supply chain'])
                <section class="admin-nav-group">
                    <button @click="catalogOpen = !catalogOpen" class="admin-nav-group__toggle">
                        <span class="admin-nav-group__left">
                            <span class="admin-nav-group__icon admin-nav-group__icon--amber"><x-admin.icon name="fa-boxes-stacked" class="h-4 w-4" /></span>
                            <span><span class="admin-nav-group__title">Catalog And Supply</span><span class="admin-nav-group__hint">Products, structure, vendors, warranties</span></span>
                        </span>
                        <span class="text-slate-400">
                            <svg x-show="catalogOpen" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m18 15-6-6-6 6"></path></svg>
                            <svg x-show="!catalogOpen" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 9 6 6 6-6"></path></svg>
                        </span>
                    </button>
                    <div x-show="catalogOpen" x-transition class="admin-nav-links">
                        @can('view inventory')
                            <x-admin.sidebar.link href="{{ route('admin.stocks') }}" route="admin.stocks" icon="stock" :badge="$lowStockItems > 0 ? $lowStockItems : null" description="Main inventory workspace with images and exports">Stock Management</x-admin.sidebar.link>
                            <x-admin.sidebar.link href="{{ route('admin.categories') }}" route="admin.categories" icon="categories" description="Store category structure and assignments">Categories</x-admin.sidebar.link>
                            <x-admin.sidebar.link href="{{ route('admin.brands') }}" route="admin.brands" icon="brands" description="Brand names and uploaded logos">Brands</x-admin.sidebar.link>
                            <x-admin.sidebar.link href="{{ route('admin.item-types') }}" route="admin.item-types" icon="item-types" description="Item type structure for products">Item Types</x-admin.sidebar.link>
                            <x-admin.sidebar.link href="{{ route('admin.makes') }}" route="admin.makes" icon="make" description="Manufacturers and origin references">Manufacturers</x-admin.sidebar.link>
                            <x-admin.sidebar.link href="{{ route('admin.item-quality-levels') }}" route="admin.item-quality-levels" icon="quality" description="Quality levels, badges, and labels">Quality Levels</x-admin.sidebar.link>
                        @endcan
                        @can('view supply chain')
                            <x-admin.sidebar.link href="{{ route('admin.suppliers') }}" route="admin.suppliers" icon="suppliers" description="Supplier contacts and activity state">Suppliers</x-admin.sidebar.link>
                            <x-admin.sidebar.link href="{{ route('admin.warranties') }}" route="admin.warranties" icon="warranties" description="Warranty plans and durations">Warranties</x-admin.sidebar.link>
                        @endcan
                    </div>
                </section>
            @endcanany

            @can('view site management')
                <section class="admin-nav-group">
                    <button @click="experienceOpen = !experienceOpen" class="admin-nav-group__toggle">
                        <span class="admin-nav-group__left">
                            <span class="admin-nav-group__icon admin-nav-group__icon--pink"><x-admin.icon name="fa-wand-magic-sparkles" class="h-4 w-4" /></span>
                            <span><span class="admin-nav-group__title">Storefront Experience</span><span class="admin-nav-group__hint">Branding, promotions, and public content</span></span>
                        </span>
                        <span class="text-slate-400">
                            <svg x-show="experienceOpen" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m18 15-6-6-6 6"></path></svg>
                            <svg x-show="!experienceOpen" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 9 6 6 6-6"></path></svg>
                        </span>
                    </button>
                    <div x-show="experienceOpen" x-transition class="admin-nav-links">
                        <x-admin.sidebar.link href="{{ route('admin.site-management.index') }}" route="admin.site-management.index" icon="storefront" description="Storefront control center and workflow guide">Overview</x-admin.sidebar.link>
                        <x-admin.sidebar.link href="{{ route('admin.site-management.appearance') }}" route="admin.site-management.appearance" icon="appearance" description="Branding, homepage content, and payments">Appearance</x-admin.sidebar.link>
                        <x-admin.sidebar.link href="{{ route('admin.site-management.banners') }}" route="admin.site-management.banners" icon="spark" description="Banner campaigns and hero visuals">Banners</x-admin.sidebar.link>
                        <x-admin.sidebar.link href="{{ route('admin.site-management.discounts') }}" route="admin.site-management.discounts" icon="bolt" description="Discount rules and promotional pricing">Discounts</x-admin.sidebar.link>
                        <x-admin.sidebar.link href="{{ route('admin.site-management.display-items') }}" route="admin.site-management.display-items" icon="display" description="Curated product rails and homepage sections">Display Items</x-admin.sidebar.link>
                        <x-admin.sidebar.link href="{{ route('admin.site-management.reviews') }}" route="admin.site-management.reviews" icon="review" :badge="$pendingReviews > 0 ? $pendingReviews : null" description="Moderate customer reviews and approvals">Reviews</x-admin.sidebar.link>
                    </div>
                </section>
            @endcan

            @canany(['view users','view roles','view settings'])
                <section class="admin-nav-group">
                    <button @click="controlOpen = !controlOpen" class="admin-nav-group__toggle">
                        <span class="admin-nav-group__left">
                            <span class="admin-nav-group__icon admin-nav-group__icon--indigo"><x-admin.icon name="fa-user-shield" class="h-4 w-4" /></span>
                            <span><span class="admin-nav-group__title">Team And System</span><span class="admin-nav-group__hint">Access, AI, communications, and recovery</span></span>
                        </span>
                        <span class="text-slate-400">
                            <svg x-show="controlOpen" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m18 15-6-6-6 6"></path></svg>
                            <svg x-show="!controlOpen" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 9 6 6 6-6"></path></svg>
                        </span>
                    </button>
                    <div x-show="controlOpen" x-transition class="admin-nav-links">
                        @can('view users')
                            <x-admin.sidebar.link href="{{ route('admin.users') }}" route="admin.users" icon="users" description="Staff accounts and user access review">Users</x-admin.sidebar.link>
                        @endcan
                        @can('view roles')
                            <x-admin.sidebar.link href="{{ route('admin.roles') }}" route="admin.roles" icon="roles" description="Roles, permissions, and access recovery">Roles And Permissions</x-admin.sidebar.link>
                        @endcan
                        @can('view settings')
                            <x-admin.sidebar.link href="{{ route('admin.settings') }}" route="admin.settings" icon="system" description="Email, WhatsApp, AI, and integration settings">System Settings</x-admin.sidebar.link>
                        @endcan
                    </div>
                </section>
            @endcanany
        </div>

        <div class="admin-sidebar-footer">
            <a href="{{ url('/') }}" wire:navigate class="admin-sidebar-footer__link"><x-admin.icon name="fa-arrow-up-right-from-square" class="h-4 w-4" /><span>Open Storefront</span></a>
            <p class="admin-sidebar-footer__copy">Use this sidebar for operations. Jump back to the live store when you need to verify the customer-facing result.</p>
        </div>
    </div>
</aside>
