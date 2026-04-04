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

        <main :class="sidebarOpen ? 'main-content-with-sidebar' : 'main-content-full'" class="admin-main main-content">
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

    @livewireScripts
    @stack('scripts')

    <script>
        function adminLayout() {
            return {
                sidebarOpen: localStorage.getItem('sidebarOpen') === null ? true : localStorage.getItem('sidebarOpen') === 'true',
                profileDropdownOpen: false,
                notificationDropdownOpen: false,
                isMobile: window.innerWidth < 1024,
                theme: localStorage.getItem('adminTheme') || 'light',

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
                    localStorage.setItem('adminTheme', value);
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
                }
            }
        }
    </script>

    @if(\App\Models\SiteSetting::get('ai_enabled', true))
        @livewire('a-i-assistant')
    @endif
</body>
</html>
