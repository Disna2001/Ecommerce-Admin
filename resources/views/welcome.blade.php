@php
use Illuminate\Support\Facades\Storage;

$wishCount = count(session('wishlist', []));
$cartCount = collect(session('cart', []))->sum('quantity');
$isApp = str_contains(request()->userAgent(), 'DisplayLankaApp');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeStore()" x-init="init()" :class="{ dark: dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>window._token='{{ csrf_token() }}';</script>
    <title>{{ $siteName }}</title>
    @if($faviconPath)<link rel="icon" href="{{ Storage::url($faviconPath) }}">@endif
    @if($logoPath)<link rel="preload" as="image" href="{{ Storage::url($logoPath) }}">@endif
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&family=outfit:wght@400;500;600;700;800&family=plus-jakarta-sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @livewireStyles
    <style>
        :root { --primary: {{ $primaryColor }}; --secondary: {{ $secondaryColor }}; --accent: {{ $accentColor }}; --site-text: {{ $textColor ?? '#111827' }}; --site-bg: {{ $bgColor ?? '#f8fafc' }}; --nav-bg: {{ $navBgColor ?? '#ffffff' }}; --heading-font: '{{ $headingFont }}', sans-serif; --body-font: '{{ $bodyFont }}', sans-serif; }
        body { font-family: var(--body-font), sans-serif; scroll-behavior: smooth; }
        h1, h2, h3, h4, h5, h6 { font-family: var(--heading-font), sans-serif; }
        .shell { background: radial-gradient(circle at top left, rgba(109,40,217,.15), transparent 28%), radial-gradient(circle at top right, rgba(6,182,212,.14), transparent 24%), var(--site-bg); color: var(--site-text); }
        .dark .shell { background: radial-gradient(circle at top left, rgba(109,40,217,.28), transparent 28%), radial-gradient(circle at top right, rgba(6,182,212,.18), transparent 22%), #0f1020; color: #f5f3ff; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .dark .glass { background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .premium-card { background: white; border-radius: 3rem; border: 1px solid rgba(0,0,0,0.03); box-shadow: 0 25px 80px -20px rgba(0,0,0,0.08); transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        .dark .premium-card { background: #0f172a; border-color: rgba(255,255,255,0.05); box-shadow: 0 25px 80px -20px rgba(0,0,0,0.4); }
        .premium-card:hover { transform: translateY(-8px); box-shadow: 0 40px 100px -20px rgba(0,0,0,0.12); }
        .hero-gradient { background: linear-gradient(135deg, {{ $heroBgFrom }} 0%, {{ $heroBgTo }} 100%); }
    </style>
</head>
<body class="shell">
<div class="min-h-screen">
    <div id="site-progress" class="pointer-events-none fixed left-0 top-0 z-[70] h-1 w-0 opacity-0 transition-[width,opacity] duration-300" style="background:linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));"></div>
    <livewire:storefront.header-bar />

    <main class="px-4 {{ $isApp ? 'pb-12' : 'pb-20 lg:pb-12' }} space-y-6 sm:space-y-8">
        @include('storefront.partials.page-sections', ['sections' => app(\App\Services\Storefront\StorefrontLayoutService::class)->editorSectionsFor('home'), 'slot' => 'before'])

        <!-- Hero Protocol (Compact Marketplace Two-Column Layout) -->
        @if(($heroSlideshowEnabled ?? true) && ($heroBanners ?? collect())->isNotEmpty())
            <section x-data="{ activeSlide: 0, slidesCount: {{ $heroBanners->count() }} }" 
                     @if($heroSlideshowAutoplay ?? true)
                     x-init="setInterval(() => { activeSlide = (activeSlide + 1) % slidesCount }, 7000)"
                     @endif
                     class="mx-auto mt-2 max-w-7xl">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">
                    <!-- Left Hero Banner (8 Cols) -->
                    <div class="lg:col-span-8 rounded-[2.5rem] overflow-hidden relative shadow-2xl">
                        @foreach($heroBanners as $index => $banner)
                            <div x-show="activeSlide === {{ $index }}" 
                                 x-transition:enter="transition ease-out duration-1000"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-500"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-105"
                                 class="relative z-10 px-6 py-8 sm:px-8 sm:py-10 lg:px-10 lg:py-12 flex flex-col justify-between min-h-[300px] sm:min-h-[360px]"
                                 style="background: {{ $banner->bg_color ?: 'linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%)' }}; color: {{ $banner->text_color ?: '#ffffff' }}">
                                 
                                 <div class="absolute inset-0 opacity-15 pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;20&quot; height=&quot;20&quot; viewBox=&quot;0 0 20 20&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot; fill-rule=&quot;evenodd&quot;%3E%3Ccircle cx=&quot;3&quot; cy=&quot;3&quot; r=&quot;3&quot;/%3E%3Ccircle cx=&quot;13&quot; cy=&quot;13&quot; r=&quot;3&quot;/%3E%3C/g%3E%3C/svg%3E');"></div>
                                 
                                 <div class="relative z-10 space-y-4 max-w-xl text-left">
                                     @if($banner->subtitle)
                                         <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.25em] border border-white/20">
                                             <span class="relative flex h-2 w-2">
                                                 <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                 <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                             </span>
                                             {{ $banner->subtitle }}
                                         </div>
                                     @endif
                                     
                                     <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black leading-tight tracking-tight">
                                         {{ $banner->title }}
                                     </h1>
                                     
                                     @if($banner->caption)
                                         <p class="text-xs sm:text-sm leading-relaxed opacity-90">
                                             {{ $banner->caption }}
                                         </p>
                                     @endif
                                     
                                     @if($banner->button_text)
                                         <div class="pt-2">
                                             <a wire:navigate href="{{ url($banner->button_link ?: '/products') }}" 
                                                class="inline-flex items-center justify-center gap-3 rounded-full bg-white px-7 py-3 text-xs font-black uppercase tracking-widest shadow-xl transition-all duration-300 hover:scale-105 group"
                                                style="color: {{ $banner->bg_color ?: 'var(--primary)' }}">
                                                 <span>{{ $banner->button_text }}</span>
                                                 <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
                                             </a>
                                         </div>
                                     @endif
                                 </div>

                                 @if($banner->image_path)
                                     <div class="mt-4 lg:mt-0 lg:absolute lg:right-6 lg:bottom-6 w-full lg:w-60 overflow-hidden rounded-2xl border border-white/20 bg-white/10 shadow-xl p-2">
                                         <img src="{{ Storage::url($banner->image_path) }}" alt="{{ $banner->title }}" class="h-32 sm:h-44 w-full rounded-xl object-cover transition-transform duration-700 hover:scale-105">
                                     </div>
                                 @endif
                            </div>
                        @endforeach

                        @if($heroBanners->count() > 1)
                            <div class="absolute bottom-4 left-6 z-20 flex gap-2">
                                @foreach($heroBanners as $index => $banner)
                                    <button @click="activeSlide = {{ $index }}" 
                                            class="h-1.5 rounded-full transition-all duration-300"
                                            :class="activeSlide === {{ $index }} ? 'w-6 bg-white' : 'w-2 bg-white/40'"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Right Stacked Promo Tiles (4 Cols) -->
                    <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-4 sm:gap-6">
                        <a wire:navigate href="{{ url('/products?sort=newest') }}" class="flex-1 group relative overflow-hidden rounded-[2rem] bg-slate-900 p-6 text-white shadow-xl hover:scale-[1.02] transition-all border border-slate-800 flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-3">
                                <span class="rounded-full bg-indigo-500/20 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-indigo-300 border border-indigo-400/30">
                                    New In
                                </span>
                                <i class="fas fa-arrow-right text-xs text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black tracking-tight text-white group-hover:text-amber-300 transition-colors">New Arrivals</h3>
                                <p class="text-xs text-slate-400 mt-1">Fresh display assemblies in stock</p>
                            </div>
                        </a>

                        <a wire:navigate href="{{ url('/products') }}" class="flex-1 group relative overflow-hidden rounded-[2rem] bg-slate-800 p-6 text-white shadow-xl hover:scale-[1.02] transition-all border border-slate-700 flex flex-col justify-between" style="background:linear-gradient(135deg, #1e293b 0%, #334155 100%)">
                            <div class="flex items-center justify-between mb-3">
                                <span class="rounded-full bg-amber-500/20 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-amber-300 border border-amber-400/30">
                                    Popular
                                </span>
                                <i class="fas fa-arrow-right text-xs text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black tracking-tight text-white group-hover:text-cyan-300 transition-colors">Best Sellers</h3>
                                <p class="text-xs text-slate-400 mt-1">Top rated replacement modules</p>
                            </div>
                        </a>
                    </div>
                </div>
            </section>
        @else
            <!-- Static Marketplace Hero Fallback (Two-Column Layout) -->
            <section class="mx-auto mt-2 max-w-7xl">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">
                    <!-- Main Hero Banner (Left 8 Cols) -->
                    <div class="lg:col-span-8 rounded-[2.5rem] overflow-hidden relative shadow-2xl p-6 sm:p-10 text-white flex flex-col justify-between min-h-[300px] sm:min-h-[360px]" style="background:{{ $heroSurface === 'minimal' ? 'transparent' : 'linear-gradient(135deg, '.$heroBgFrom.' 0%, '.$heroBgTo.' 100%)' }}">
                        @if($heroSurface !== 'minimal')
                            <div class="absolute inset-0 opacity-20 pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;20&quot; height=&quot;20&quot; viewBox=&quot;0 0 20 20&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot; fill-rule=&quot;evenodd&quot;%3E%3Ccircle cx=&quot;3&quot; cy=&quot;3&quot; r=&quot;3&quot;/%3E%3Ccircle cx=&quot;13&quot; cy=&quot;13&quot; r=&quot;3&quot;/%3E%3C/g%3E%3C/svg%3E');"></div>
                        @endif

                        <div class="relative z-10 space-y-4 max-w-xl text-left">
                            <div class="inline-flex items-center gap-2 rounded-full px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.25em] {{ $heroSurface !== 'minimal' ? 'bg-white/10 text-white border border-white/20' : 'bg-slate-100 text-slate-500' }}">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                {{ $siteTagline }}
                            </div>
                            
                            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black leading-tight tracking-tight text-slate-900 {{ $heroSurface !== 'minimal' ? 'text-white' : '' }}">
                                {{ $heroTitle }}
                                <span class="block mt-1 bg-gradient-to-r {{ $heroSurface !== 'minimal' ? 'from-amber-300 via-cyan-200 to-amber-300' : 'from-[var(--primary)] via-[var(--accent)] to-[var(--primary)]' }} bg-[length:200%_auto] bg-clip-text text-transparent animate-gradient-x">{{ $heroHighlight }}</span>
                            </h1>
                            
                            <p class="text-xs sm:text-sm leading-relaxed {{ $heroSurface !== 'minimal' ? 'text-white/80' : 'text-slate-600' }}">
                                {{ $heroSubtitle }} <span class="font-bold {{ $heroSurface !== 'minimal' ? 'text-white' : 'text-slate-900' }}">{{ $heroMicrocopy }}</span>
                            </p>
                            
                            <div class="pt-2 flex flex-wrap items-center gap-4">
                                <a wire:navigate href="{{ $heroBtnLink === '#' ? url('/products') : url($heroBtnLink) }}" class="inline-flex items-center justify-center gap-3 rounded-full px-7 py-3 text-xs font-black text-white uppercase tracking-widest shadow-xl transition-all duration-300 hover:scale-105 group" style="background:linear-gradient(90deg, var(--primary), var(--secondary))">
                                    <span>{{ $heroBtnText }}</span>
                                    <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
                                </a>
                                @guest
                                    <a wire:navigate href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full border px-6 py-3 text-xs font-black uppercase tracking-widest transition-all duration-300 hover:bg-white/10 {{ $heroSurface !== 'minimal' ? 'border-white/30 text-white' : 'border-slate-200 text-slate-900 bg-white shadow-sm' }}">
                                        Onboard Now
                                    </a>
                                @endguest
                            </div>
                        </div>

                        @if($heroImagePath)
                            <div class="mt-4 lg:mt-0 lg:absolute lg:right-6 lg:bottom-6 w-full lg:w-56 overflow-hidden rounded-2xl border {{ $heroSurface !== 'minimal' ? 'border-white/20 bg-white/10 shadow-xl' : 'border-slate-200 bg-white shadow-md' }} p-2">
                                <img src="{{ Storage::url($heroImagePath) }}" alt="{{ $heroTitle }}" class="h-32 sm:h-44 w-full rounded-xl object-cover transition-transform duration-700 hover:scale-105">
                            </div>
                        @endif
                    </div>

                    <!-- Right Stacked Promo Tiles (4 Cols) -->
                    <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-4 sm:gap-6">
                        <a wire:navigate href="{{ url('/products?sort=newest') }}" class="flex-1 group relative overflow-hidden rounded-[2rem] bg-slate-900 p-6 text-white shadow-xl hover:scale-[1.02] transition-all border border-slate-800 flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-3">
                                <span class="rounded-full bg-indigo-500/20 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-indigo-300 border border-indigo-400/30">
                                    New In
                                </span>
                                <i class="fas fa-arrow-right text-xs text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black tracking-tight text-white group-hover:text-amber-300 transition-colors">New Arrivals</h3>
                                <p class="text-xs text-slate-400 mt-1">Fresh display assemblies in stock</p>
                            </div>
                        </a>

                        <a wire:navigate href="{{ url('/products') }}" class="flex-1 group relative overflow-hidden rounded-[2rem] bg-slate-800 p-6 text-white shadow-xl hover:scale-[1.02] transition-all border border-slate-700 flex flex-col justify-between" style="background:linear-gradient(135deg, #1e293b 0%, #334155 100%)">
                            <div class="flex items-center justify-between mb-3">
                                <span class="rounded-full bg-amber-500/20 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-amber-300 border border-amber-400/30">
                                    Popular
                                </span>
                                <i class="fas fa-arrow-right text-xs text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black tracking-tight text-white group-hover:text-cyan-300 transition-colors">Best Sellers</h3>
                                <p class="text-xs text-slate-400 mt-1">Top rated replacement modules</p>
                            </div>
                        </a>
                    </div>
                </div>
            </section>
        @endif

        <!-- Dense Category Icon Grid -->
        <section id="categories" class="mx-auto max-w-7xl px-4">
            <div class="mb-4 flex items-end justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-1">{{ $categoryStripTitle }}</p>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $categoryH2 }}</h2>
                </div>
                <a wire:navigate href="{{ url('/products') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    {{ $categorySeeAll }} <i class="fas fa-arrow-right ml-1 text-[8px]"></i>
                </a>
            </div>
            
            @if($categories->isNotEmpty())
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3 sm:gap-4">
                    <a wire:navigate href="{{ url('/products') }}" class="group flex flex-col items-center p-3 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-lg transition-all text-center">
                        <div class="flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-sm mb-2 group-hover:scale-105 transition-transform">
                            <i class="fas fa-layer-group text-lg"></i>
                        </div>
                        <span class="text-[11px] font-black uppercase tracking-tight text-slate-900 dark:text-white line-clamp-1">
                            {{ $categoryAllLabel }}
                        </span>
                    </a>
                    
                    @foreach($categories as $category)
                        <a wire:navigate href="{{ url('/products?category='.$category->id) }}" class="group flex flex-col items-center p-3 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-lg transition-all text-center">
                            <div class="flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-2xl bg-indigo-50 dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-all mb-2">
                                <i class="fas {{ ($categoryIcons[$category->id] ?? 'fa-tag') }} text-base"></i>
                            </div>
                            <span class="text-[11px] font-bold text-slate-800 dark:text-slate-200 line-clamp-1 group-hover:text-indigo-600">
                                {{ $category->name }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <!-- Dense Intentional Category Grid Fallback -->
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3 sm:gap-4">
                    <a wire:navigate href="{{ url('/products') }}" class="group flex flex-col items-center p-3 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-lg transition-all text-center">
                        <div class="flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-sm mb-2 group-hover:scale-105 transition-transform">
                            <i class="fas fa-layer-group text-lg"></i>
                        </div>
                        <span class="text-[11px] font-black uppercase tracking-tight text-slate-900 dark:text-white line-clamp-1">
                            {{ $categoryAllLabel }}
                        </span>
                    </a>

                    @foreach([
                        ['Displays', 'fa-mobile-screen-button'],
                        ['Touch Glass', 'fa-hand-pointer'],
                        ['Batteries', 'fa-battery-full'],
                        ['Charging ICs', 'fa-bolt'],
                        ['Flex Cables', 'fa-plug'],
                        ['Repair Tools', 'fa-screwdriver-wrench'],
                        ['Accessories', 'fa-shield-halved']
                    ] as [$placeholderName, $icon])
                        <a wire:navigate href="{{ url('/products') }}" class="group flex flex-col items-center p-3 rounded-2xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/60 hover:shadow-md transition-all text-center">
                            <div class="flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 group-hover:bg-indigo-600 group-hover:text-white transition-all mb-2">
                                <i class="fas {{ $icon }} text-base"></i>
                            </div>
                            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 line-clamp-1 group-hover:text-indigo-600">
                                {{ $placeholderName }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- Promo Strip Protocol -->
        @if(($promoBanners ?? collect())->isNotEmpty())
            @foreach($promoBanners as $banner)
                <section class="mx-auto max-w-7xl">
                    <div class="card rounded-[2rem] px-6 py-5 sm:px-8 shadow-md transition-all hover:scale-[1.01]" 
                         style="background: {{ $banner->bg_color ?: 'linear-gradient(120deg, var(--primary), var(--secondary))' }}; color: {{ $banner->text_color ?: '#ffffff' }}">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="max-w-3xl">
                                @if($banner->subtitle)
                                    <span class="inline-flex rounded-full bg-white/15 px-3 py-0.5 text-[10px] font-bold uppercase tracking-[0.2em]">{{ $banner->subtitle }}</span>
                                @endif
                                <h2 class="mt-2 text-xl font-black sm:text-2xl" style="color: {{ $banner->text_color ?: '#ffffff' }}">{{ $banner->title }}</h2>
                                @if($banner->caption)
                                    <p class="mt-1 max-w-2xl text-xs leading-5 opacity-80">{{ $banner->caption }}</p>
                                @endif
                            </div>
                            @if($banner->button_text)
                                <a wire:navigate href="{{ url($banner->button_link ?: '/products') }}" 
                                   class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-xs font-bold shadow-md hover:scale-105 transition-transform shrink-0"
                                   style="color: {{ $banner->bg_color ?: 'var(--primary)' }}">
                                    {{ $banner->button_text }}
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </section>
            @endforeach
        @elseif($promoStripEnabled)
            <!-- Static Promo Strip Fallback -->
            <section class="mx-auto max-w-7xl">
                <div class="card rounded-[2rem] px-6 py-5 text-white sm:px-8 shadow-md" style="background:linear-gradient(120deg, {{ $promoStripFrom }}, {{ $promoStripTo }})">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="max-w-3xl">
                            <span class="inline-flex rounded-full bg-white/15 px-3 py-0.5 text-[10px] font-bold uppercase tracking-[0.2em]">{{ $promoStripBadge }}</span>
                            <h2 class="mt-2 text-xl font-black sm:text-2xl">{{ $promoStripTitle }}</h2>
                            <p class="mt-1 max-w-2xl text-xs leading-5 text-white/80">{{ $promoStripText }}</p>
                        </div>
                        <a wire:navigate href="{{ url($promoStripButtonLink) }}" class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-xs font-bold text-slate-900 shadow-md shrink-0">
                            {{ $promoStripButtonText }}
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </section>
        @endif

        @if(($personalizedRecommendations ?? collect())->isNotEmpty())
            <section class="mx-auto max-w-7xl px-4">
                <div class="mb-4 flex items-end justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-[var(--primary)] mb-1">Picked For You</p>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Recommended For You</h2>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
                    @foreach($personalizedRecommendations as $product)
                        @include('storefront.partials.product-card', ['product' => $product, 'badge' => 'For You'])
                    @endforeach
                </div>
            </section>
        @endif

        @php
            $railGridClass = 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4';
            $sectionSubtitles = [
                'deals' => $dealsSubtitle,
                'featured' => $featuredSubtitle,
                'new-arrivals' => $newSubtitle,
            ];
        @endphp

        @foreach(array_values(array_filter([
            ['deals', $dealsTitle, $deals->take(max(1, $productsPerRail)), 'Hot Sale'],
            ['featured', $featuredTitle, $featured->take(max(1, $productsPerRail)), 'Featured'],
            $showNewArrivalsLink ? ['new-arrivals', $newTitle, $newArrivals->take(max(1, $productsPerRail)), 'New'] : null,
        ])) as [$sectionId,$sectionTitle,$items,$badge])
            @if($items->isNotEmpty())
                <section id="{{ $sectionId }}" class="mx-auto max-w-7xl px-4">
                    <div class="mb-4 flex items-end justify-between">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-[var(--primary)] mb-1">{{ $badge }}</p>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $sectionTitle }}</h2>
                        </div>
                        <a wire:navigate href="{{ url('/products') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                            View All <i class="fas fa-arrow-right ml-1 text-[8px]"></i>
                        </a>
                    </div>
                    <div class="{{ $railGridClass }}">
                        @foreach($items as $product)
                            @include('storefront.partials.product-card', ['product' => $product, 'badge' => $badge])
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach

        <!-- Compressed Critical Review Registry -->
        <section id="reviews" class="mx-auto max-w-7xl px-4 py-4">
            <div class="text-center mb-6">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-[var(--primary)] mb-1">{{ $reviewsEyebrow }}</p>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $reviewsSectionTitle }}</h2>
                <p class="mt-1 text-xs text-slate-400 max-w-xl mx-auto">{{ $reviewsSectionSubtitle }}</p>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                @forelse($reviews as $review)
                    <article class="premium-card p-6 flex flex-col justify-between !rounded-2xl">
                        <div>
                            <div class="flex gap-1 mb-3">
                                @for($i=0;$i<5;$i++)<i class="fas fa-star text-[10px] {{ $i < $review->rating ? 'text-amber-400' : 'text-slate-200' }}"></i>@endfor
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed italic">"{{ $review->body ?: $review->title }}"</p>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-white/5 flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-slate-900 dark:bg-white flex items-center justify-center text-white dark:text-slate-900 text-[10px] font-black">
                                {{ strtoupper(substr($review->user->name ?? 'V', 0, 1) ?: 'V') }}
                            </div>
                            <div>
                                <p class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-wider">{{ $review->user->name ?? 'Verified Buyer' }}</p>
                                <p class="text-[8px] text-slate-400 font-bold uppercase tracking-wider">Verified Purchase</p>
                            </div>
                        </div>
                    </article>
                @empty
                    @foreach([
                        ['Ordered a replacement screen for my Samsung and it arrived the next day. Fit perfectly — exactly as described.', 'Kumari P.'],
                        ['Best prices I found anywhere in Sri Lanka. The part was genuine and support actually picked up the phone when I called.', 'Dilan S.'],
                        ['Bought an iPhone display assembly. Easy to install and the quality is clearly original. Will order again.', 'Ruwani M.'],
                    ] as [$text, $name])
                        <article class="premium-card p-6 flex flex-col justify-between !rounded-2xl">
                            <div>
                                <div class="flex gap-1 mb-3 text-amber-400">@for($i=0;$i<5;$i++)<i class="fas fa-star text-[10px]"></i>@endfor</div>
                                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed italic">"{{ $text }}"</p>
                            </div>
                            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-white/5 flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-slate-900 dark:bg-white flex items-center justify-center text-white dark:text-slate-900 text-[10px] font-black">{{ strtoupper(substr($name, 0, 1)) }}</div>
                                <div>
                                    <p class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-wider">{{ $name }}</p>
                                    <p class="text-[8px] text-slate-400 font-bold uppercase tracking-wider">Verified Purchase</p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                @endforelse
            </div>
        </section>

        <!-- Compressed Final CTA -->
        <section class="mx-auto max-w-7xl px-4">
            <div class="relative rounded-[2.5rem] px-6 py-8 sm:px-10 sm:py-10 text-center text-white overflow-hidden shadow-xl" style="background:linear-gradient(135deg, var(--primary), var(--secondary), var(--accent))">
                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 20px 20px;"></div>
                <div class="relative z-10 space-y-3">
                    <p class="text-[9px] font-black uppercase tracking-[0.4em] text-white/70">{{ $finalCtaEyebrow }}</p>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight">{{ $finalCtaTitle }}</h2>
                    <p class="mx-auto max-w-xl text-xs sm:text-sm text-white/80">{{ $finalCtaSubtitle }}</p>
                    <div class="pt-2">
                        <a wire:navigate href="{{ url($finalCtaButtonLink) }}" class="inline-flex items-center gap-3 rounded-full bg-white px-8 py-3.5 text-xs font-black text-slate-900 uppercase tracking-widest shadow-lg hover:scale-105 transition-all group">
                            <span>{{ $finalCtaButtonText }}</span>
                            <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Global Footer -->
    <footer id="footer" class="mt-24 px-4 pb-12 hidden lg:block">
        <div class="mx-auto max-w-7xl rounded-[3.5rem] bg-slate-950 px-8 py-12 sm:px-12 sm:py-20 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-1/3 h-1/3 bg-indigo-500/10 blur-[120px] rounded-full"></div>
            <div class="relative z-10 grid gap-10 sm:gap-16 lg:grid-cols-[1.5fr_1fr_1fr_1fr]">
                <div>
                    <div class="flex items-center gap-3 mb-8">
                        @if($logoPath)
                            <img src="{{ Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-10 w-auto object-contain">
                        @else
                            <div class="text-2xl font-black lowercase tracking-tighter" style="color:var(--primary)">{{ strtolower($siteName) }}</div>
                        @endif
                    </div>
                    <p class="max-w-xs text-sm leading-8 text-slate-400 mb-8">{{ $footerTagline }}</p>
                    <div class="flex items-center gap-4">
                        @foreach([['fab fa-facebook-f',$fbUrl],['fab fa-twitter',$twUrl],['fab fa-instagram',$igUrl]] as [$icon,$url])
                            <a href="{{ $url }}" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white/60 hover:bg-white hover:text-slate-900 transition-all"><i class="{{ $icon }} text-xs"></i></a>
                        @endforeach
                    </div>
                </div>
                <div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-500 mb-8">{{ $footerColShop }}</h3>
                    <nav class="flex flex-col gap-4 text-xs font-bold uppercase tracking-widest text-slate-400">
                        <a wire:navigate href="{{ url('/products') }}" class="hover:text-white transition-colors">{{ $navProductsLabel }}</a>
                        <a wire:navigate href="{{ route('track-order') }}" class="hover:text-white transition-colors">Track Your Order</a>
                        <a wire:navigate href="{{ route('login') }}" class="hover:text-white transition-colors">{{ $footerLinkSignin }}</a>
                    </nav>
                </div>
                <div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-500 mb-8">{{ $footerColLegal }}</h3>
                    <nav class="flex flex-col gap-4 text-xs font-bold uppercase tracking-widest text-slate-400">
                        <a wire:navigate href="{{ route('privacy-policy') }}" class="hover:text-white transition-colors">{{ $footerLinkPrivacy }}</a>
                        <a wire:navigate href="{{ route('terms-and-conditions') }}" class="hover:text-white transition-colors">Terms of Service</a>
                        <a wire:navigate href="{{ route('refund-policy') }}" class="hover:text-white transition-colors">{{ $footerLinkRefund }}</a>
                    </nav>
                </div>
                <div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-500 mb-8">Contact</h3>
                    <div class="flex flex-col gap-6 text-sm text-slate-400">
                        <p class="leading-relaxed">{{ $utilityCenter }}</p>
                        @if($supportEmail)<a href="mailto:{{ $supportEmail }}" class="text-white font-bold">{{ $supportEmail }}</a>@endif
                        @if($supportPhone)<p class="text-white font-bold">{{ $supportPhone }}</p>@endif
                    </div>
                </div>
            </div>
            <div class="mt-12 sm:mt-20 pt-8 border-t border-white/5 flex flex-col sm:flex-row flex-wrap justify-between items-center gap-6 sm:gap-4 text-center sm:text-left">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-600">{{ $footerCopy }}</p>
                <div class="flex gap-6">
                    <i class="fab fa-cc-visa text-xl text-slate-700"></i>
                    <i class="fab fa-cc-mastercard text-xl text-slate-700"></i>
                    <i class="fab fa-cc-paypal text-xl text-slate-700"></i>
                </div>
            </div>
        </div>
    </footer>

    @include('frontend.partials.support-chatbox')
</div>

@livewireScripts
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
document.addEventListener('click', function(e){const cartBtn=e.target.closest('.shop-cart-btn');if(!cartBtn)return;e.preventDefault();const original=cartBtn.innerHTML;cartBtn.innerHTML='<i class="fas fa-spinner fa-spin"></i>';fetch('/cart/add/'+cartBtn.dataset.id,{method:'POST',headers:{'X-CSRF-TOKEN':window._token,'Content-Type':'application/json'},body:JSON.stringify({quantity:1})}).then(r=>r.json()).then((d)=>{cartBtn.innerHTML='Added';if(window.Livewire){window.Livewire.dispatch('cart-updated',{count:d.count});}setTimeout(()=>cartBtn.innerHTML=original,1400);}).catch(()=>cartBtn.innerHTML=original);});
</script>
</body>
</html>
