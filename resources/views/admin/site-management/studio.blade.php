@php
    $siteName = \App\Models\SiteSetting::get('site_name', config('app.name', 'DISPLAY LANKA.LK'));
    $faviconPath = \App\Models\SiteSetting::get('favicon_path', '');
    $currentRoute = request()->route()?->getName() ?? '';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $siteName }} — Storefront Studio</title>

    @if (!empty($faviconPath))
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($faviconPath) }}">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/storefront-studio/main.js'])
    @include('components.admin.styles')
    @livewireStyles
</head>
<body x-data="adminLayout()" x-init="init()" :class="theme === 'dark' ? 'dark admin-theme-dark' : 'admin-theme-light'">
    <div class="admin-shell min-h-screen">
        <x-admin.navigation.top />
        <x-admin.sidebar.overlay />
        <x-admin.sidebar.main />

        <main :class="sidebarOpen ? 'main-content-with-sidebar' : 'main-content-full'" class="admin-main main-content pb-0 lg:pb-0">
            <div id="storefront-studio" class="storefront-studio-root"></div>
        </main>
    </div>

    @livewireScripts

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
                    this.$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value));
                    this.$watch('theme', value => this.applyTheme(value));
                    window.addEventListener('resize', () => {
                        this.isMobile = window.innerWidth < 1024;
                        if (this.isMobile) this.sidebarOpen = false;
                    });
                    if (this.isMobile) this.sidebarOpen = false;
                },
                applyTheme(value) {
                    this.theme = value;
                    localStorage.setItem('site-theme', value);
                    document.documentElement.classList.toggle('dark', value === 'dark');
                },
                toggleTheme() { this.applyTheme(this.theme === 'dark' ? 'light' : 'dark'); },
                toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; },
                closeSidebar() { this.sidebarOpen = false; },
            }
        }
    </script>

    @include('components.admin.ui.notifications')
</body>
</html>
