<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        $siteName = \App\Models\SiteSetting::get('site_name', config('app.name', 'DISPLAY LANKA.LK'));
        $faviconPath = \App\Models\SiteSetting::get('favicon_path', '');
        $pageTitle = trim($__env->yieldContent('title', 'POS Workspace'));
    @endphp

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $siteName }} - {{ $pageTitle }}</title>

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
<body x-data="adminPosLayout()" x-init="init()" :class="theme === 'dark' ? 'dark admin-theme-dark' : 'admin-theme-light'">
    <div class="admin-shell min-h-screen">
        <nav class="admin-topbar">
            <div class="admin-topbar__inner">
                <div style="display:flex;align-items:center;gap:0.9rem;min-width:0;">
                    <a href="{{ route('admin.dashboard') }}" class="admin-tool-button" style="text-decoration:none;" aria-label="Back to admin">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <x-admin.logo />
                    <div class="desktop-only" style="display:flex;flex-direction:column;gap:0.15rem;">
                        <span style="font-size:0.72rem;font-weight:800;letter-spacing:0.18em;text-transform:uppercase;color:rgba(226,232,240,0.68);">Counter Workspace</span>
                        <span style="font-size:0.92rem;font-weight:700;color:var(--admin-navbar-text);">Separate POS Window</span>
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:0.65rem;flex-shrink:0;">
                    <a href="{{ route('admin.invoices') }}" class="admin-chip desktop-only" style="text-decoration:none;">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Invoices</span>
                    </a>

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

        <main class="admin-main" style="padding-top:5.8rem;">
            <div class="admin-content">
                @yield('content')
            </div>
        </main>
    </div>

    @livewireScripts
    @stack('scripts')

    <script>
        function adminPosLayout() {
            return {
                theme: localStorage.getItem('adminTheme') || 'light',

                init() {
                    this.applyTheme(this.theme);
                },

                applyTheme(value) {
                    this.theme = value;
                    localStorage.setItem('adminTheme', value);
                    document.documentElement.classList.toggle('dark', value === 'dark');
                },

                toggleTheme() {
                    this.applyTheme(this.theme === 'dark' ? 'light' : 'dark');
                }
            }
        }
    </script>
</body>
</html>
