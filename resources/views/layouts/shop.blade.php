@php
    use Illuminate\Support\Facades\Storage;

    $wishCount = count(session('wishlist', []));
    $cartCount = collect(session('cart', []))->sum('quantity');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeStore()" x-init="init()" :class="{ dark: dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>window._token='{{ csrf_token() }}';</script>
    <title>@yield('title', $siteName) - {{ $siteName }}</title>
    @if($faviconPath)<link rel="icon" href="{{ Storage::url($faviconPath) }}">@endif
    @if($logoPath)<link rel="preload" as="image" href="{{ Storage::url($logoPath) }}">@endif
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @livewireStyles
    <style>
        :root {
            --primary: {{ $primaryColor }};
            --secondary: {{ $secondaryColor }};
            --accent: {{ $accentColor }};
            --site-text: {{ $textColor ?? '#111827' }};
            --site-bg: {{ $bgColor ?? '#f8fafc' }};
            --nav-bg: {{ $navBgColor ?? '#ffffff' }};
        }
        body { font-family: 'Figtree', sans-serif; }
        .shell {
            background:
                radial-gradient(circle at top left, rgba(109,40,217,.15), transparent 28%),
                radial-gradient(circle at top right, rgba(6,182,212,.14), transparent 24%),
                var(--site-bg);
            color: var(--site-text);
        }
        .dark .shell {
            background:
                radial-gradient(circle at top left, rgba(109,40,217,.28), transparent 28%),
                radial-gradient(circle at top right, rgba(6,182,212,.18), transparent 22%),
                #0f1020;
            color: #f5f3ff;
        }
        .glass {
            background: color-mix(in srgb, var(--nav-bg) 78%, white 22%);
            border: 1px solid rgba(139,92,246,.12);
            backdrop-filter: blur(16px);
        }
        .dark .glass {
            background: rgba(15,23,42,.72);
            border-color: rgba(255,255,255,.08);
        }
        .card-shadow { box-shadow: 0 18px 48px rgba(88,28,135,.10); }
        .dark .card-shadow { box-shadow: 0 20px 60px rgba(0,0,0,.35); }
        .surface {
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(139,92,246,.10);
        }
        .dark .surface {
            background: rgba(15,23,42,.92);
            border-color: rgba(255,255,255,.06);
        }
        .muted { color: #6b6480; }
        .dark .muted { color: #c8bdf0; }
        .text-adapt { color: #0f172a; }
        .dark .text-adapt { color: #f8fafc; }
        .text-soft { color: #64748b; }
        .dark .text-soft { color: #cbd5e1; }
        .field {
            width: 100%;
            border-radius: 1rem;
            border: 1px solid rgba(203,213,225,1);
            background: rgba(255,255,255,.88);
            padding: .875rem 1rem;
            font-size: .875rem;
            color: #0f172a;
        }
        .dark .field {
            border-color: rgba(255,255,255,.08);
            background: rgba(15,23,42,.72);
            color: #f8fafc;
        }
        .field:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 16%, transparent);
        }
        .btn-gradient {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            color: white;
        }
        .btn-gradient:hover { filter: brightness(.96); }
        .storefront-hero {
            background:
                radial-gradient(circle at top right, color-mix(in srgb, var(--secondary) 18%, transparent), transparent 28%),
                linear-gradient(135deg, rgba(255,255,255,0.94), rgba(248,250,252,0.86));
            border: 1px solid rgba(139,92,246,.10);
        }
        .dark .storefront-hero {
            background:
                radial-gradient(circle at top right, rgba(139,92,246,.22), transparent 28%),
                linear-gradient(135deg, rgba(15,23,42,0.92), rgba(17,24,39,0.88));
            border-color: rgba(255,255,255,.08);
        }
        .storefront-stat {
            background: rgba(255,255,255,.78);
            border: 1px solid rgba(148,163,184,.16);
            backdrop-filter: blur(12px);
        }
        .dark .storefront-stat {
            background: rgba(15,23,42,.72);
            border-color: rgba(255,255,255,.08);
        }
        .storefront-page-shell {
            background:
                radial-gradient(circle at top left, rgba(109, 40, 217, 0.10), transparent 24%),
                radial-gradient(circle at top right, rgba(6, 182, 212, 0.08), transparent 18%),
                linear-gradient(180deg, color-mix(in srgb, var(--site-bg) 92%, white 8%) 0%, color-mix(in srgb, var(--site-bg) 96%, white 4%) 52%, var(--site-bg) 100%);
        }
        .dark .storefront-page-shell {
            background:
                radial-gradient(circle at top left, rgba(109, 40, 217, 0.22), transparent 24%),
                radial-gradient(circle at top right, rgba(6, 182, 212, 0.14), transparent 18%),
                linear-gradient(180deg, #111428 0%, #10192b 52%, #0c1324 100%);
        }
        .storefront-panel {
            background: rgba(255,255,255,0.82);
            border: 1px solid rgba(139,92,246,0.10);
            backdrop-filter: blur(16px);
        }
        .dark .storefront-panel {
            background: rgba(15,23,42,0.76);
            border-color: rgba(255,255,255,0.08);
        }
        .storefront-card {
            background: rgba(255,255,255,0.92);
            border: 1px solid rgba(139,92,246,0.10);
            box-shadow: 0 18px 48px rgba(88,28,135,0.08);
        }
        .dark .storefront-card {
            background: rgba(15,23,42,0.92);
            border-color: rgba(255,255,255,0.06);
            box-shadow: 0 20px 60px rgba(0,0,0,0.32);
        }
        .storefront-chip {
            background: rgba(255,255,255,0.88);
            border: 1px solid rgba(139,92,246,0.10);
            box-shadow: 0 12px 30px rgba(88,28,135,0.06);
        }
        .dark .storefront-chip {
            background: rgba(15,23,42,0.82);
            border-color: rgba(255,255,255,0.08);
            box-shadow: 0 18px 44px rgba(0,0,0,0.22);
        }
        .storefront-reveal {
            opacity: 0;
            transform: translateY(14px);
            animation: storefront-fade-up .55s ease forwards;
        }
        .storefront-reveal-delay-1 { animation-delay: .08s; }
        .storefront-reveal-delay-2 { animation-delay: .16s; }
        .storefront-reveal-delay-3 { animation-delay: .24s; }
        @keyframes storefront-fade-up {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .storefront-reveal,
            .storefront-reveal-delay-1,
            .storefront-reveal-delay-2,
            .storefront-reveal-delay-3 {
                animation: none;
                opacity: 1;
                transform: none;
            }
        }
        [wire\:loading] { pointer-events: none; }
    </style>
    @stack('styles')
</head>
<body class="shell">
<div class="min-h-screen">
    <div id="site-progress" class="pointer-events-none fixed left-0 top-0 z-[70] h-1 w-0 opacity-0 transition-[width,opacity] duration-300" style="background:linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));"></div>
    @if($topbarEnabled)
        <div class="px-4 py-2 text-xs font-semibold text-white" style="background:linear-gradient(90deg, {{ $topbarFrom }}, {{ $topbarTo }})">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-center gap-4 sm:justify-between">
                <span><i class="fas fa-bolt mr-2 text-[10px]"></i>{{ $utilityBadge }}</span>
                <div class="hidden items-center gap-5 sm:flex">
                    <span>{{ $utilityLeft }}</span>
                    <span>{{ $utilityCenter }}</span>
                    <span>{{ $topbarText }}</span>
                </div>
            </div>
        </div>
    @endif

    <header class="sticky top-0 z-50 px-4 py-4">
        <div class="glass card-shadow mx-auto flex max-w-7xl items-center justify-between rounded-full px-4 py-3 lg:px-6">
            <div class="flex items-center gap-4">
                <a href="/" class="flex items-center gap-3">
                    @if($logoPath)
                        <img src="{{ Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-10 w-auto object-contain">
                    @else
                        <span class="text-2xl font-black lowercase" style="color:var(--primary)">{{ strtolower($siteName) }}</span>
                    @endif
                </a>
                <nav class="hidden items-center gap-5 text-sm font-medium lg:flex">
                    <a wire:navigate href="{{ url('/products') }}">{{ $navProductsLabel ?? 'Products' }}</a>
                    <a wire:navigate href="{{ url('/products?sort=newest') }}">New Arrivals</a>
                    <a wire:navigate href="{{ url('/products?sort=price_asc') }}">{{ $navDealsLabel ?? 'Deals' }}</a>
                    <a wire:navigate href="{{ route('track-order') }}">{{ $navTrackLabel ?? 'Track' }}</a>
                    <a wire:navigate href="{{ route('help-center') }}">{{ $navHelpLabel ?? 'Help' }}</a>
                </nav>
            </div>

            <form action="{{ url('/products') }}" method="GET" class="relative hidden w-full max-w-md md:block">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $searchPlaceholder }}" class="w-full rounded-full border border-white/40 bg-white/70 px-11 py-3 text-sm text-slate-700 outline-none dark:border-white/10 dark:bg-slate-900/60 dark:text-white">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
            </form>

            <div class="flex items-center gap-2">
                <button @click="toggle()" type="button" class="flex h-11 w-11 items-center justify-center rounded-full border border-white/30 bg-white/70 text-slate-700 dark:border-white/10 dark:bg-slate-900/60 dark:text-white">
                    <i class="fas" :class="dark ? 'fa-sun' : 'fa-moon'"></i>
                </button>
                <a wire:navigate href="{{ url('/wishlist') }}" class="relative flex h-11 w-11 items-center justify-center rounded-full border border-white/30 bg-white/70 dark:border-white/10 dark:bg-slate-900/60">
                    <i class="far fa-heart"></i>
                    @if($wishCount>0)<span class="wishlist-count absolute -right-0.5 -top-0.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white">{{ $wishCount }}</span>@endif
                </a>
                <a wire:navigate href="{{ url('/cart') }}" class="relative flex h-11 w-11 items-center justify-center rounded-full border border-white/30 bg-white/70 dark:border-white/10 dark:bg-slate-900/60">
                    <i class="fas fa-bag-shopping"></i>
                    @if($cartCount>0)<span class="cart-count absolute -right-0.5 -top-0.5 flex h-4 min-w-[16px] items-center justify-center rounded-full px-1 text-[10px] font-bold text-white" style="background:var(--primary)">{{ $cartCount }}</span>@endif
                </a>
                @guest
                    <a wire:navigate href="{{ route('login') }}" class="hidden rounded-full px-4 py-2 text-sm font-semibold md:inline-flex">Login</a>
                    <a wire:navigate href="{{ route('register') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-white" style="background:linear-gradient(90deg, var(--primary), var(--secondary))">Sign Up</a>
                @else
                    <a wire:navigate href="{{ route('profile.index') }}" class="hidden rounded-full px-4 py-2 text-sm font-semibold md:inline-flex">My Account</a>
                    @can('view-admin-menu')
                        <a wire:navigate href="{{ route('admin.dashboard') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-white" style="background:linear-gradient(90deg, var(--primary), var(--secondary))">Admin Panel</a>
                    @endcan
                @endguest
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="mx-auto max-w-7xl px-4">
            <div class="glass card-shadow rounded-2xl border border-emerald-200/60 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="mx-auto max-w-7xl px-4">
            <div class="glass card-shadow rounded-2xl border border-rose-200/60 px-4 py-3 text-sm text-rose-700">{{ session('error') }}</div>
        </div>
    @endif

    <main class="px-4 pb-16">
        @yield('content')
    </main>

    <footer class="mt-16 px-4 pb-10">
        <div class="mx-auto max-w-7xl rounded-[2rem] bg-slate-950 px-8 py-10 text-white card-shadow">
            <div class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr_0.8fr_1fr]">
                <div>
                    @if($logoPath)
                        <img src="{{ Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-12 w-auto object-contain brightness-0 invert">
                    @else
                        <div class="text-3xl font-black lowercase">{{ strtolower($siteName) }}</div>
                    @endif
                    <p class="mt-4 max-w-sm text-sm leading-7 text-white/70">{{ $footerTagline }}</p>
                    <div class="mt-5 flex items-center gap-3">
                        @foreach([['fab fa-facebook-f',$fbUrl],['fab fa-twitter',$twUrl],['fab fa-instagram',$igUrl],['fab fa-pinterest',$piUrl]] as [$icon,$url])
                            <a href="{{ $url }}" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white/80"><i class="{{ $icon }}"></i></a>
                        @endforeach
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.22em] text-white/60">Browse</h3>
                    <div class="mt-4 space-y-3 text-sm text-white/75">
                        <a wire:navigate class="block" href="{{ url('/products') }}">{{ $navProductsLabel ?? 'Products' }}</a>
                        <a wire:navigate class="block" href="{{ url('/products?sort=newest') }}">New Arrivals</a>
                        <a wire:navigate class="block" href="{{ url('/products?sort=price_asc') }}">{{ $navDealsLabel ?? 'Deals' }}</a>
                        <a wire:navigate class="block" href="{{ url('/wishlist') }}">Wishlist</a>
                        <a wire:navigate class="block" href="{{ route('track-order') }}">{{ $navTrackLabel ?? 'Track' }}</a>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.22em] text-white/60">Categories</h3>
                    <div class="mt-4 space-y-3 text-sm text-white/75">
                        @foreach($categories->take(4) as $category)
                            <a wire:navigate class="block" href="{{ url('/products?category='.$category->id) }}">{{ $category->name }}</a>
                        @endforeach
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.22em] text-white/60">Support</h3>
                    <div class="mt-4 space-y-3 text-sm text-white/75">
                        <a wire:navigate class="block" href="{{ route('help-center') }}">{{ $navHelpLabel ?? 'Help' }}</a>
                        @if(!empty($supportEmail))<div>{{ $supportEmail }}</div>@endif
                        @if(!empty($supportPhone))<div>{{ $supportPhone }}</div>@endif
                        @if(!empty($supportWhatsapp))<div>WhatsApp: {{ $supportWhatsapp }}</div>@endif
                        @if(!empty($supportHours))<div>{{ $supportHours }}</div>@endif
                    </div>
                </div>
            </div>
            <div class="mt-10 border-t border-white/10 pt-5 text-xs text-white/50">{{ $footerCopy }}</div>
        </div>
    </footer>

    @include('frontend.partials.support-chatbox')
</div>

@livewireScripts
@stack('scripts')
<script>
function themeStore(){return{dark:false,init(){const saved=localStorage.getItem('site-theme');this.dark=saved?saved==='dark':window.matchMedia('(prefers-color-scheme: dark)').matches;this.apply();},toggle(){this.dark=!this.dark;this.apply();},apply(){document.documentElement.classList.toggle('dark',this.dark);localStorage.setItem('site-theme',this.dark?'dark':'light');}}}
document.addEventListener('livewire:init', () => {
    const progress = document.getElementById('site-progress');
    if (!progress) return;
    let timer;

    document.addEventListener('livewire:navigate', () => {
        progress.style.opacity = '1';
        progress.style.width = '32%';
        clearTimeout(timer);
        timer = setTimeout(() => {
            progress.style.width = '68%';
        }, 140);
    });

    document.addEventListener('livewire:navigated', () => {
        clearTimeout(timer);
        progress.style.width = '100%';
        setTimeout(() => {
            progress.style.opacity = '0';
            progress.style.width = '0';
        }, 180);
    });
});
document.addEventListener('click', function(e) {
    const cartBtn = e.target.closest('.shop-cart-btn');
    if (cartBtn) {
        e.preventDefault();
        const icon = cartBtn.querySelector('i');
        const prev = cartBtn.innerHTML;
        if (icon) icon.className = 'fas fa-spinner fa-spin';
        fetch('/cart/add/' + cartBtn.dataset.id, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window._token, 'Content-Type': 'application/json' },
            body: JSON.stringify({ quantity: 1 })
        }).then(r => r.json()).then(d => {
            cartBtn.innerHTML = 'Added';
            document.querySelectorAll('.cart-count').forEach(el => { el.textContent = d.count; el.style.display = d.count > 0 ? 'flex' : 'none'; });
            setTimeout(() => cartBtn.innerHTML = prev, 1400);
        }).catch(() => { cartBtn.innerHTML = prev; });
    }
    const wishBtn = e.target.closest('.shop-wishlist-btn');
    if (wishBtn) {
        e.preventDefault();
        const icon = wishBtn.querySelector('i');
        fetch('/wishlist/toggle/' + wishBtn.dataset.id, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window._token }
        }).then(r => r.json()).then(d => {
            if (icon) icon.className = d.added ? 'fas fa-heart text-rose-500' : 'far fa-heart text-slate-400';
            document.querySelectorAll('.wishlist-count').forEach(el => { el.textContent = d.count; el.style.display = d.count > 0 ? 'flex' : 'none'; });
        }).catch(() => {});
    }
});
</script>
</body>
</html>
