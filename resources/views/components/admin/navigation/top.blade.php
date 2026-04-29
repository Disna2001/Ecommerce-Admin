@php
    $siteName = \App\Models\SiteSetting::get('site_name', config('app.name', 'Display Lanka'));
@endphp

<nav class="admin-topbar">
    <div class="admin-topbar__inner">
        <div style="display:flex;align-items:center;gap:0.9rem;min-width:0;">
            <button @click="toggleSidebar" class="sidebar-toggle mobile-only" aria-label="Toggle sidebar">
                <svg x-show="!sidebarOpen" style="width:1.2rem;height:1.2rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <svg x-show="sidebarOpen" style="width:1.2rem;height:1.2rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <button @click="toggleSidebar" class="sidebar-toggle desktop-sidebar-toggle" aria-label="Collapse sidebar">
                <svg x-show="sidebarOpen" style="width:1.2rem;height:1.2rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                </svg>
                <svg x-show="!sidebarOpen" style="width:1.2rem;height:1.2rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                </svg>
            </button>

            <x-admin.logo />
        </div>

        <div class="desktop-only" style="flex:1;display:flex;justify-content:center;min-width:0;">
            <div style="width:min(29rem,100%);">
                <x-admin.navigation.search />
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:0.65rem;flex-shrink:0;">
            <span class="admin-chip desktop-only">
                <span style="width:0.45rem;height:0.45rem;border-radius:999px;background:#34d399;"></span>
                {{ $siteName }} Admin
            </span>

            @if (Route::has('admin.pos'))
                @can('view pos')
                    <button
                        type="button"
                        @click="launchPos('{{ route('admin.pos') }}')"
                        class="admin-chip desktop-only"
                        style="border:none;cursor:pointer;"
                        aria-label="Open POS in a new window"
                    >
                        <i class="fas fa-cash-register"></i>
                        <span>POS</span>
                        <i class="fas fa-arrow-up-right-from-square" style="font-size:0.72rem;opacity:0.72;"></i>
                    </button>

                    <button
                        type="button"
                        @click="launchPos('{{ route('admin.pos') }}')"
                        class="admin-tool-button mobile-only"
                        aria-label="Open POS in a new window"
                        title="Open POS"
                    >
                        <i class="fas fa-cash-register"></i>
                    </button>
                @endcan
            @endif

            @can('view system health')
                <a href="{{ route('admin.system-health') }}" wire:navigate class="admin-chip desktop-only" style="text-decoration:none;">
                    <i class="fas fa-heart-pulse"></i>
                    <span>Health</span>
                </a>
            @endcan

            @can('view site management')
                <a href="{{ route('admin.site-management.index') }}" wire:navigate class="admin-chip desktop-only" style="text-decoration:none;">
                    <i class="fas fa-store"></i>
                    <span>Storefront</span>
                </a>
            @endcan

            <button @click="toggleTheme" class="admin-tool-button" aria-label="Toggle theme">
                <svg x-show="theme === 'light'" style="width:1.15rem;height:1.15rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 3v2.25M12 18.75V21m6.364-15.364-1.591 1.591M7.227 16.773l-1.591 1.591M21 12h-2.25M5.25 12H3m15.364 6.364-1.591-1.591M7.227 7.227 5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"></path>
                </svg>
                <svg x-show="theme === 'dark'" style="width:1.15rem;height:1.15rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 12c0 5.385 4.365 9.75 9.75 9.75a9.753 9.753 0 009.002-6.748z"></path>
                </svg>
            </button>

            <x-admin.navigation.user-menu />
        </div>
    </div>
</nav>
