@php
    use Illuminate\Support\Facades\Storage;
    $wishCount = count(session('wishlist', []));
    $cartCount = collect(session('cart', []))->sum('quantity');
    
    // Ensure all shared variables are safe strings
    $safeSiteName = (string) ($siteName ?? 'Display Lanka');
    $safeFavicon = (string) ($faviconPath ?? '');
    $safeLogo = (string) ($logoPath ?? '');
    $safePrimary = (string) ($primaryColor ?? '#8b5cf6');
    $safeSecondary = (string) ($secondaryColor ?? '#d946ef');
    $safeAccent = (string) ($accentColor ?? '#06b6d4');
    $safeText = (string) ($textColor ?? '#111827');
    $safeBg = (string) ($bgColor ?? '#f8fafc');
    $safeNavBg = (string) ($navBgColor ?? '#ffffff');

    // Load Footer and Contact Site Settings
    $footerTagline = \App\Models\SiteSetting::get('footer_tagline', 'Your one-stop shop for everything trendy.');
    $footerCopyright = \App\Models\SiteSetting::get('footer_copyright', '© {year} '.($safeSiteName ?? 'Shop').'. All rights reserved.');
    $footerCopyright = str_replace('{year}', date('Y'), $footerCopyright);
    $supportEmail = \App\Models\SiteSetting::get('support_email', \App\Models\SiteSetting::get('support_notification_email', ''));
    $supportPhone = \App\Models\SiteSetting::get('support_phone', '');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeStore()" x-init="init()" :class="{ dark: dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>window._token='{{ csrf_token() }}';</script>
    <title>@yield('title', $safeSiteName) - {{ $safeSiteName }}</title>
    
    @if(!empty($safeFavicon))
        <link rel="icon" href="{{ Storage::url($safeFavicon) }}">
    @endif
    
    @if(!empty($safeLogo))
        <link rel="preload" as="image" href="{{ Storage::url($safeLogo) }}">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet"/>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @livewireStyles
    <style>
        :root {
            --primary: {{ $safePrimary }};
            --secondary: {{ $safeSecondary }};
            --accent: {{ $safeAccent }};
            --site-text: {{ $safeText }};
            --site-bg: {{ $safeBg }};
            --nav-bg: {{ $safeNavBg }};
        }
        body { font-family: 'Figtree', sans-serif; }
        .shell {
            background: radial-gradient(circle at top left, rgba(109,40,217,.15), transparent 28%), radial-gradient(circle at top right, rgba(6,182,212,.14), transparent 24%), var(--site-bg);
            color: var(--site-text);
        }
        .dark .shell {
            background: radial-gradient(circle at top left, rgba(109,40,217,.28), transparent 28%), radial-gradient(circle at top right, rgba(6,182,212,.18), transparent 22%), #0f1020;
            color: #f5f3ff;
        }
        .glass { background: color-mix(in srgb, var(--nav-bg) 78%, white 22%); border: 1px solid rgba(139,92,246,.12); backdrop-filter: blur(16px); }
        .dark .glass { background: rgba(15,23,42,.72); border-color: rgba(255,255,255,.08); }
    </style>
    @stack('styles')
</head>
<body class="shell">
<x-preloader />
<div class="min-h-screen">
    <div id="site-progress" class="pointer-events-none fixed left-0 top-0 z-[70] h-1 w-0 opacity-0 transition-[width,opacity] duration-300" style="background:linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));"></div>
    <livewire:storefront.header-bar />

    @if(session('success'))
        <div class="mx-auto max-w-7xl px-4 mt-4">
            <div class="glass rounded-2xl border border-emerald-200/60 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        </div>
    @endif

    <main class="px-4 pb-16">
        @yield('content')
    </main>

    <footer class="mt-16 px-4 pb-10">
        <div class="mx-auto max-w-7xl rounded-[2rem] bg-slate-950 px-8 py-10 text-white shadow-2xl">
            <div class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr_0.8fr_1fr]">
                <div>
                    @if(!empty($safeLogo))
                        <div class="inline-flex h-12 items-center rounded-2xl bg-white/95 px-4">
                            <img src="{{ Storage::url($safeLogo) }}" alt="{{ $safeSiteName }}" class="h-8 w-auto object-contain">
                        </div>
                    @else
                        <div class="text-3xl font-black lowercase">{{ strtolower($safeSiteName) }}</div>
                    @endif
                    <p class="mt-4 max-w-sm text-sm leading-7 text-white/70">{{ $footerTagline }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.22em] text-white/60">Quick Links</h3>
                    <div class="mt-4 space-y-3 text-sm text-white/75">
                        <a wire:navigate class="block hover:text-white transition-colors" href="{{ url('/products') }}">Products</a>
                        <a wire:navigate class="block hover:text-white transition-colors" href="{{ route('track-order') }}">Track Order</a>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.22em] text-white/60">Legal</h3>
                    <div class="mt-4 space-y-3 text-sm text-white/75">
                        <a wire:navigate class="block hover:text-white transition-colors" href="{{ route('privacy-policy') }}">Privacy Policy</a>
                        <a wire:navigate class="block hover:text-white transition-colors" href="{{ route('terms-and-conditions') }}">Terms & Conditions</a>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.22em] text-white/60">Contact</h3>
                    <div class="mt-4 space-y-3 text-sm text-white/75">
                        @if(!empty($supportEmail))
                            <a href="mailto:{{ $supportEmail }}" class="block hover:text-white transition-colors">{{ $supportEmail }}</a>
                        @endif
                        @if(!empty($supportPhone))
                            <a href="tel:{{ preg_replace('/\s+/', '', $supportPhone) }}" class="block hover:text-white transition-colors">{{ $supportPhone }}</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-10 border-t border-white/10 pt-5 text-xs text-white/50">{{ $footerCopyright }}</div>
        </div>
    </footer>
</div>

@livewireScripts
@stack('scripts')
<script>
function themeStore(){return{dark:false,init(){const saved=localStorage.getItem('site-theme');this.dark=saved?saved==='dark':window.matchMedia('(prefers-color-scheme: dark)').matches;this.apply();},toggle(){this.dark=!this.dark;this.apply();},apply(){document.documentElement.classList.toggle('dark',this.dark);localStorage.setItem('site-theme',this.dark?'dark':'light');}}}
</script>
</body>
</html>
