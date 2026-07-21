@php
    $pendingOrders = \App\Models\Order::whereIn('status', ['pending', 'confirmed'])->count();
    $lowStockItems = \App\Models\Stock::whereColumn('quantity', '<=', 'reorder_level')->count();
    $pendingReviews = class_exists(\App\Models\Review::class) ? \App\Models\Review::where('is_approved', false)->count() : 0;
    $failedOutbox = \App\Models\NotificationOutbox::where('status', 'failed')->count();
    $awaitingWhatsAppHandoffs = class_exists(\App\Models\WhatsAppConversation::class)
        ? \App\Models\WhatsAppConversation::where('status', 'awaiting_human')->count()
        : 0;
@endphp

<aside 
    :class="sidebarOpen ? 'sidebar-visible' : 'sidebar-hidden'" 
    class="admin-sidebar sidebar-transition flex flex-col"
    x-data="{coreOpen:true,storeOpen:false,systemOpen:false}"
>
    <!-- Quiet Workspace Header Row -->
    <div class="px-3 py-2.5 border-b border-slate-200/60 dark:border-slate-800/80">
        <div class="flex items-center gap-2 text-xs font-bold text-slate-800 dark:text-slate-200">
            <i class="fas fa-layer-group text-slate-500 text-xs flex-shrink-0"></i>
            <span class="truncate">Admin Workspace</span>
        </div>
    </div>

    <!-- Scrollable Navigation List -->
    <div class="flex-1 overflow-y-auto px-2 py-2 scrollbar-custom space-y-4">
        <!-- Core Engine -->
        @canany(['view dashboard','view orders','view inventory','view users'])
            <div class="space-y-1">
                <p class="px-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-2 mb-1">Core Engine</p>
                <div class="space-y-0.5">
                    @can('view dashboard')
                        <x-admin.sidebar.link href="{{ route('admin.dashboard') }}" route="admin.dashboard" icon="fa-chart-pie">Dashboard</x-admin.sidebar.link>
                    @endcan
                    @can('view orders')
                        <x-admin.sidebar.link href="{{ route('admin.orders') }}" route="admin.orders" icon="fa-receipt" :badge="$pendingOrders > 0 ? $pendingOrders : null">Orders</x-admin.sidebar.link>
                    @endcan
                    @can('view inventory')
                        <x-admin.sidebar.link href="{{ route('admin.stocks') }}" route="admin.stocks" icon="fa-boxes-stacked" :badge="$lowStockItems > 0 ? $lowStockItems : null">Stock Ledger</x-admin.sidebar.link>
                    @endcan
                    @can('view users')
                        <x-admin.sidebar.link href="{{ route('admin.users') }}" route="admin.users" icon="fa-users">Users</x-admin.sidebar.link>
                    @endcan
                </div>
            </div>
        @endcanany

        <!-- Catalog Data -->
        @canany(['view inventory', 'view supply chain', 'view stock movements'])
            <div class="space-y-1">
                <p class="px-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-2 mb-1">Catalog Data</p>
                <div class="space-y-0.5">
                    @can('view inventory')
                        <x-admin.sidebar.link href="{{ route('admin.categories') }}" route="admin.categories" icon="fa-tags">Categories</x-admin.sidebar.link>
                        <x-admin.sidebar.link href="{{ route('admin.brands') }}" route="admin.brands" icon="fa-copyright">Brands</x-admin.sidebar.link>
                        <x-admin.sidebar.link href="{{ route('admin.makes') }}" route="admin.makes" icon="fa-car">Makes</x-admin.sidebar.link>
                        <x-admin.sidebar.link href="{{ route('admin.item-types') }}" route="admin.item-types" icon="fa-boxes-packing">Item Types</x-admin.sidebar.link>
                        <x-admin.sidebar.link href="{{ route('admin.item-quality-levels') }}" route="admin.item-quality-levels" icon="fa-award">Quality Tiers</x-admin.sidebar.link>
                    @endcan
                    @can('view supply chain')
                        <x-admin.sidebar.link href="{{ route('admin.suppliers') }}" route="admin.suppliers" icon="fa-truck-moving">Suppliers</x-admin.sidebar.link>
                        <x-admin.sidebar.link href="{{ route('admin.warranties') }}" route="admin.warranties" icon="fa-shield-halved">Warranties</x-admin.sidebar.link>
                    @endcan
                    @can('view stock movements')
                        <x-admin.sidebar.link href="{{ route('admin.stock-movements') }}" route="admin.stock-movements" icon="fa-clock-rotate-left">Stock Movements</x-admin.sidebar.link>
                    @endcan
                </div>
            </div>
        @endcanany

        <!-- Storefront -->
        @can('view site management')
            <div class="space-y-1">
                <p class="px-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-2 mb-1">Storefront</p>
                <div class="space-y-0.5">
                    <x-admin.sidebar.link href="{{ route('admin.site-management.index') }}" route="admin.site-management.index" icon="fa-wand-magic-sparkles">Storefront Studio</x-admin.sidebar.link>
                </div>
            </div>
        @endcan

        <!-- Support -->
        @can('view whatsapp conversations')
            <div class="space-y-1">
                <p class="px-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-2 mb-1">Support</p>
                <div class="space-y-0.5">
                    <x-admin.sidebar.link href="{{ route('admin.whatsapp-conversations') }}" route="admin.whatsapp-conversations" icon="fa-comments" :badge="$awaitingWhatsAppHandoffs > 0 ? $awaitingWhatsAppHandoffs : null">WhatsApp Conversations</x-admin.sidebar.link>
                </div>
            </div>
        @endcan

        <!-- Protocols -->
        @canany(['view system health','view notification outbox','view settings','view roles'])
            <div class="space-y-1">
                <p class="px-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-2 mb-1">Protocols</p>
                <div class="space-y-0.5">
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

    <!-- Quiet Live Store Footer Row -->
    <div class="p-2 border-t border-slate-200/60 dark:border-slate-800/80">
        <a href="{{ url('/') }}" target="_blank" class="flex items-center justify-between px-2.5 py-1.5 rounded-md text-xs font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-external-link-alt text-[11px] text-slate-400"></i>
                <span>Live Store</span>
            </div>
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
        </a>
    </div>
</aside>
