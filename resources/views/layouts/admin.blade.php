<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        $siteName = \App\Models\SiteSetting::get('site_name', config('app.name', 'DISPLAY LANKA.LK'));
        $faviconPath = \App\Models\SiteSetting::get('favicon_path', '');
    @endphp

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $siteName }} - Admin</title>

    @if (!empty($faviconPath))
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($faviconPath) }}">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @include('components.admin.styles')
    @livewireStyles
</head>
<body x-data="adminLayout()" x-init="init()" :class="theme === 'dark' ? 'dark admin-theme-dark' : 'admin-theme-light'">
    <div class="admin-shell min-h-screen">
        <x-admin.navigation.top />
        <x-admin.sidebar.overlay />
        <x-admin.sidebar.main />

        <main :class="sidebarOpen ? 'main-content-with-sidebar' : 'main-content-full'" class="admin-main main-content pb-20 lg:pb-0">
            <div class="admin-content">
                @hasSection('header')
                    @php
                        $headerTitle = Illuminate\Support\Str::of(View::getSection('header'))->toString();
                        $breadcrumb = Illuminate\Support\Str::of(View::getSection('breadcrumb', 'Overview'))->toString();
                    @endphp
                    <x-admin.header :title="$headerTitle" :breadcrumb="$breadcrumb">
                        @hasSection('actions')
                            <x-slot name="actions">
                                @yield('actions')
                            </x-slot>
                        @endif
                    </x-admin.header>
                @endif

                <div class="admin-page-wrap">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    {{-- Mobile Bottom Navigation (lg:hidden) --}}
    @php
        $mobileNavPendingOrders = \App\Models\Order::whereIn('status', ['pending','confirmed'])->count();
        $mobileNavLowStock = \App\Models\Stock::whereColumn('quantity','<=','reorder_level')->count();
        $currentRoute = request()->route()?->getName() ?? '';
    @endphp
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-[60] bg-white/95 dark:bg-slate-950/95 border-t border-slate-100 dark:border-white/5 backdrop-blur-md flex items-stretch shadow-[0_-8px_30px_rgba(0,0,0,0.06)]" style="padding-bottom: env(safe-area-inset-bottom, 0)">
        @can('view dashboard')
        <a href="{{ route('admin.dashboard') }}" class="relative flex flex-col items-center justify-center gap-1 flex-1 py-3 group transition-all {{ str_starts_with($currentRoute, 'admin.dashboard') ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            <div class="flex h-6 w-6 items-center justify-center">
                <i class="fas fa-chart-pie text-base transition-transform group-active:scale-90"></i>
            </div>
            <span class="text-[9px] font-black uppercase tracking-wider">Dash</span>
            @if(str_starts_with($currentRoute, 'admin.dashboard'))
                <span class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-8 rounded-full bg-slate-900 dark:bg-white"></span>
            @endif
        </a>
        @endcan

        @can('view inventory')
        <a href="{{ route('admin.stocks') }}" class="relative flex flex-col items-center justify-center gap-1 flex-1 py-3 group transition-all {{ str_starts_with($currentRoute, 'admin.stocks') ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            <div class="relative flex h-6 w-6 items-center justify-center">
                <i class="fas fa-cubes text-base transition-transform group-active:scale-90"></i>
                @if($mobileNavLowStock > 0)
                    <span class="absolute -top-1 -right-1 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-rose-500 text-[7px] font-black text-white ring-2 ring-white dark:ring-slate-950">{{ $mobileNavLowStock > 9 ? '9+' : $mobileNavLowStock }}</span>
                @endif
            </div>
            <span class="text-[9px] font-black uppercase tracking-wider">Stock</span>
            @if(str_starts_with($currentRoute, 'admin.stocks'))
                <span class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-8 rounded-full bg-slate-900 dark:bg-white"></span>
            @endif
        </a>
        @endcan

        @can('view orders')
        <a href="{{ route('admin.orders') }}" class="relative flex flex-col items-center justify-center gap-1 flex-1 py-3 group transition-all {{ str_starts_with($currentRoute, 'admin.orders') ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            <div class="relative flex h-6 w-6 items-center justify-center">
                <i class="fas fa-receipt text-base transition-transform group-active:scale-90"></i>
                @if($mobileNavPendingOrders > 0)
                    <span class="absolute -top-1 -right-1 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-amber-500 text-[7px] font-black text-white ring-2 ring-white dark:ring-slate-950">{{ $mobileNavPendingOrders > 9 ? '9+' : $mobileNavPendingOrders }}</span>
                @endif
            </div>
            <span class="text-[9px] font-black uppercase tracking-wider">Orders</span>
            @if(str_starts_with($currentRoute, 'admin.orders'))
                <span class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-8 rounded-full bg-slate-900 dark:bg-white"></span>
            @endif
        </a>
        @endcan

        @can('view users')
        <a href="{{ route('admin.users') }}" class="relative flex flex-col items-center justify-center gap-1 flex-1 py-3 group transition-all {{ str_starts_with($currentRoute, 'admin.users') ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            <div class="flex h-6 w-6 items-center justify-center">
                <i class="fas fa-user-astronaut text-base transition-transform group-active:scale-90"></i>
            </div>
            <span class="text-[9px] font-black uppercase tracking-wider">Users</span>
            @if(str_starts_with($currentRoute, 'admin.users'))
                <span class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-8 rounded-full bg-slate-900 dark:bg-white"></span>
            @endif
        </a>
        @endcan

        @can('view pos')
        <button type="button" @click="launchPos('{{ route('admin.pos') }}')" class="relative flex flex-col items-center justify-center gap-1 flex-1 py-3 group transition-all text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 active:scale-95">
            <div class="flex h-6 w-6 items-center justify-center">
                <i class="fas fa-cash-register text-base transition-transform group-active:scale-90"></i>
            </div>
            <span class="text-[9px] font-black uppercase tracking-wider">POS</span>
        </button>
        @endcan
    </nav>

    @livewireScripts
    @stack('scripts')

    <script>
        function adminLayout() {
            return {
                sidebarOpen: localStorage.getItem('sidebarOpen') === null ? true : localStorage.getItem('sidebarOpen') === 'true',
                profileDropdownOpen: false,
                notificationDropdownOpen: false,
                isMobile: window.innerWidth < 1024,
                theme: localStorage.getItem('site-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),

                init() {
                    this.applyTheme(this.theme);

                    this.$watch('sidebarOpen', value => {
                        localStorage.setItem('sidebarOpen', value);
                    });

                    this.$watch('theme', value => {
                        this.applyTheme(value);
                    });

                    window.addEventListener('resize', () => {
                        this.isMobile = window.innerWidth < 1024;

                        if (this.isMobile) {
                            this.sidebarOpen = false;
                        }
                    });

                    if (this.isMobile) {
                        this.sidebarOpen = false;
                    }
                },

                applyTheme(value) {
                    this.theme = value;
                    localStorage.setItem('site-theme', value);
                    document.documentElement.classList.toggle('dark', value === 'dark');
                },

                toggleTheme() {
                    this.applyTheme(this.theme === 'dark' ? 'light' : 'dark');
                },

                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                },

                closeSidebar() {
                    this.sidebarOpen = false;
                },

                launchPos(url) {
                    const width = Math.min(window.screen.availWidth - 80, 1440);
                    const height = Math.min(window.screen.availHeight - 80, 960);
                    const left = Math.max(0, Math.round((window.screen.availWidth - width) / 2));
                    const top = Math.max(0, Math.round((window.screen.availHeight - height) / 2));
                    const features = [
                        `width=${width}`,
                        `height=${height}`,
                        `left=${left}`,
                        `top=${top}`,
                        'resizable=yes',
                        'scrollbars=yes',
                    ].join(',');

                    const posWindow = window.open(url, 'displaylanka-pos', features);

                    if (posWindow) {
                        posWindow.focus();
                    } else {
                        window.location.href = url;
                    }
                }
            }
        }
    </script>

    @include('components.admin.ui.notifications')
</body>
</html>
