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
    <title>{{ $siteName }}</title>
    @if($faviconPath)<link rel="icon" href="{{ Storage::url($faviconPath) }}">@endif
    @if($logoPath)<link rel="preload" as="image" href="{{ Storage::url($logoPath) }}">@endif
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @livewireStyles
    <style>
        :root { --primary: {{ $primaryColor }}; --secondary: {{ $secondaryColor }}; --accent: {{ $accentColor }}; --site-text: {{ $textColor ?? '#111827' }}; --site-bg: {{ $bgColor ?? '#f8fafc' }}; --nav-bg: {{ $navBgColor ?? '#ffffff' }}; }
        body { font-family: 'Figtree', sans-serif; scroll-behavior: smooth; }
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

    <main class="px-4 pb-16 space-y-12">
        <!-- Hero Protocol -->
        <section class="mx-auto mt-4 max-w-7xl rounded-[3rem] overflow-hidden relative" style="background:{{ $heroSurface === 'minimal' ? 'transparent' : 'linear-gradient(135deg, '.$heroBgFrom.' 0%, '.$heroBgTo.' 100%)' }}">
            @if($heroSurface !== 'minimal')
                <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\"20\" height=\"20\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"1\" fill-rule=\"evenodd\"%3E%3Ccircle cx=\"3\" cy=\"3\" r=\"3\"/%3E%3Ccircle cx=\"13\" cy=\"13\" r=\"3\"/%3E%3C/g%3E%3C/svg%3E');"></div>
            @endif
            
            <div class="relative z-10 px-6 py-16 sm:px-8 sm:py-20 lg:px-16 lg:py-24">
                <div class="{{ $heroLayout === 'centered' ? 'mx-auto max-w-4xl text-center' : 'grid gap-10 sm:gap-16 lg:grid-cols-2 lg:items-center' }}">
                    <div class="{{ $heroLayout === 'centered' ? '' : 'text-left' }}">
                        <div class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.3em] mb-8 {{ $heroSurface !== 'minimal' ? 'bg-white/10 text-white border border-white/20' : 'bg-slate-100 text-slate-500' }}">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            {{ $siteTagline }}
                        </div>
                        
                        <h1 class="text-4xl sm:text-5xl lg:text-7xl font-black leading-[1.1] text-slate-900 {{ $heroSurface !== 'minimal' ? 'text-white' : '' }} tracking-tight">
                            {{ $heroTitle }}
                            <span class="block mt-2 bg-gradient-to-r from-[var(--primary)] via-[var(--accent)] to-[var(--primary)] bg-[length:200%_auto] bg-clip-text text-transparent animate-gradient-x">{{ $heroHighlight }}</span>
                        </h1>
                        
                        <p class="mt-8 text-lg leading-relaxed {{ $heroSurface !== 'minimal' ? 'text-white/80' : 'text-slate-600' }} max-w-xl {{ $heroLayout === 'centered' ? 'mx-auto' : '' }}">
                            {{ $heroSubtitle }} <span class="font-bold {{ $heroSurface !== 'minimal' ? 'text-white' : 'text-slate-900' }}">{{ $heroMicrocopy }}</span>
                        </p>
                        
                        <div class="mt-10 flex flex-col sm:flex-row flex-wrap items-center gap-4 sm:gap-6 {{ $heroLayout === 'centered' ? 'justify-center' : '' }}">
                            <a wire:navigate href="{{ $heroBtnLink === '#' ? url('/products') : url($heroBtnLink) }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-4 rounded-full px-10 py-4 sm:py-5 text-xs font-black text-white uppercase tracking-[0.25em] shadow-2xl transition-all duration-300 hover:scale-[1.05] hover:shadow-indigo-500/40 group" style="background:linear-gradient(90deg, var(--primary), var(--secondary))">
                                <span>{{ $heroBtnText }}</span>
                                <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
                            </a>
                            @guest
                                <a wire:navigate href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center rounded-full border px-10 py-4 sm:py-5 text-xs font-black uppercase tracking-[0.25em] transition-all duration-300 hover:bg-white/10 {{ $heroSurface !== 'minimal' ? 'border-white/30 text-white' : 'border-slate-200 text-slate-900 bg-white shadow-sm hover:shadow-lg' }}">
                                    Onboard Now
                                </a>
                            @endguest
                        </div>
                    </div>

                    @if($heroImagePath && $heroLayout !== 'centered')
                        <div class="relative group">
                            <div class="absolute -inset-4 bg-white/10 rounded-[3rem] blur-2xl transition-all group-hover:blur-3xl"></div>
                            <div class="relative overflow-hidden rounded-[2.5rem] border {{ $heroSurface !== 'minimal' ? 'border-white/20 bg-white/10 shadow-2xl' : 'border-slate-200 bg-white shadow-xl' }} p-2 sm:p-4">
                                <img src="{{ Storage::url($heroImagePath) }}" alt="{{ $heroTitle }}" class="h-[250px] sm:h-[450px] w-full rounded-[2rem] object-cover transition-transform duration-700 group-hover:scale-105">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Category Pulse -->
        <section id="categories" class="mx-auto max-w-7xl px-4">
            <div class="mb-8 flex items-end justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-2">{{ $categoryStripTitle }}</p>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Browse Registry</h2>
                </div>
                <a wire:navigate href="{{ url('/products') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">See Ledger <i class="fas fa-arrow-right ml-2"></i></a>
            </div>
            
            <div class="{{ $categoryStripStyle === 'cards' ? 'grid gap-6 sm:grid-cols-2 xl:grid-cols-4' : 'flex gap-4 overflow-x-auto pb-4 custom-scrollbar' }}">
                <a wire:navigate href="{{ url('/products') }}" class="group relative flex flex-col justify-between overflow-hidden rounded-[2rem] p-8 text-white min-h-[160px] min-w-[240px] shadow-xl transition-all hover:scale-[1.02]" style="background:linear-gradient(135deg, var(--primary), var(--secondary))">
                    <i class="fas fa-grid-2 text-3xl opacity-20 absolute -right-4 -top-4"></i>
                    <p class="text-[10px] font-black uppercase tracking-widest text-white/60">Global</p>
                    <span class="text-lg font-black uppercase tracking-tight">Full Catalog</span>
                </a>
                
                @foreach($categories as $category)
                    <a wire:navigate href="{{ url('/products?category='.$category->id) }}" class="premium-card group relative flex flex-col justify-between p-8 min-h-[160px] min-w-[240px]">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white transition-colors group-hover:bg-slate-900 group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-slate-900">
                            <i class="fas {{ ($categoryIcons[$category->id] ?? 'fa-tag') }} text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Classification</p>
                            <span class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $category->name }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        @if($promoStripEnabled)
            <section class="mx-auto mt-8 max-w-7xl">
                <div class="card rounded-[2rem] px-6 py-6 text-white sm:px-8" style="background:linear-gradient(120deg, {{ $promoStripFrom }}, {{ $promoStripTo }})">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                        <div class="max-w-3xl">
                            <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em]">{{ $promoStripBadge }}</span>
                            <h2 class="mt-4 text-2xl font-black sm:text-3xl">{{ $promoStripTitle }}</h2>
                            <p class="mt-3 max-w-2xl text-sm leading-7 text-white/80">{{ $promoStripText }}</p>
                        </div>
                        <a wire:navigate href="{{ url($promoStripButtonLink) }}" class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-bold text-slate-900">
                            {{ $promoStripButtonText }}
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            </section>
        @endif

        @if(($personalizedRecommendations ?? collect())->isNotEmpty())
            <section class="mx-auto max-w-7xl px-4">
                <div class="mb-10 flex items-end justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-[var(--primary)] mb-2">Curated Intelligence</p>
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Personalized Ledger</h2>
                    </div>
                </div>
                <div class="grid gap-8 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach($personalizedRecommendations as $product)
                        <article class="premium-card group overflow-hidden flex flex-col h-full">
                            <a wire:navigate href="{{ url('/products/'.$product->id) }}" class="relative block overflow-hidden p-3 pb-0">
                                <div class="absolute left-6 top-6 z-10 rounded-full px-3 py-1 text-[9px] font-black uppercase tracking-[0.2em] text-white shadow-lg" style="background:linear-gradient(90deg, var(--primary), var(--secondary))">For You</div>
                                <div class="flex h-64 items-center justify-center rounded-[2.2rem] bg-slate-50 dark:bg-slate-900/50 p-8 overflow-hidden">
                                    @if($product->primary_image_url)
                                        <img src="{{ $product->primary_image_sources['fallback'] ?? $product->primary_image_url }}" alt="{{ $product->name }}" class="h-full w-full object-contain transition-transform duration-500 group-hover:scale-110">
                                    @endif
                                </div>
                            </a>
                            <div class="p-6 pt-5 flex flex-col flex-1">
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">{{ $product->brand->name ?? 'Premium Item' }}</p>
                                <h3 class="text-base font-black text-slate-900 dark:text-white leading-tight mb-4 group-hover:text-[var(--primary)] transition-colors">{{ $product->name }}</h3>
                                <div class="mt-auto flex items-center justify-between gap-4">
                                    <p class="text-xl font-black text-slate-900 dark:text-white">Rs {{ number_format($product->final_price ?? $product->selling_price, 2) }}</p>
                                    <button type="button" class="shop-cart-btn h-10 w-10 flex items-center justify-center rounded-full text-white shadow-lg hover:shadow-indigo-500/30 transition-all hover:scale-110" style="background:linear-gradient(90deg, var(--primary), var(--secondary))" data-id="{{ $product->id }}">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        @php
            $railGridClass = 'grid gap-8 sm:grid-cols-2 xl:grid-cols-4';
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
                    <div class="mb-10 flex items-end justify-between">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-[var(--primary)] mb-2">{{ $badge }}</p>
                            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $sectionTitle }}</h2>
                        </div>
                        <a wire:navigate href="{{ url('/products') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">View All <i class="fas fa-arrow-right ml-2"></i></a>
                    </div>
                    <div class="{{ $railGridClass }}">
                        @foreach($items as $product)
                            <article class="premium-card group overflow-hidden flex flex-col h-full">
                                <a wire:navigate href="{{ url('/products/'.$product->id) }}" class="relative block overflow-hidden p-3 pb-0">
                                    <div class="absolute left-6 top-6 z-10 flex flex-col gap-2">
                                        <div class="rounded-full px-3 py-1 text-[9px] font-black uppercase tracking-[0.2em] text-white shadow-lg" style="background:linear-gradient(90deg, #f97316, #ef4444)">{{ $badge }}</div>
                                        @if($showRailStockStatus)
                                            <div class="rounded-full px-3 py-1 text-[8px] font-black uppercase tracking-[0.2em] shadow-sm {{ $product->quantity <= 0 ? 'bg-slate-900 text-white' : 'bg-white text-slate-900' }}">
                                                {{ $product->quantity <= 0 ? 'Exhausted' : 'In Registry' }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex h-64 items-center justify-center rounded-[2.2rem] bg-slate-50 dark:bg-slate-900/50 p-8 overflow-hidden">
                                        @if($product->primary_image_url)
                                            <img src="{{ $product->primary_image_sources['fallback'] ?? $product->primary_image_url }}" alt="{{ $product->name }}" class="h-full w-full object-contain transition-transform duration-500 group-hover:scale-110">
                                        @else
                                            <i class="fas fa-box-open text-4xl text-slate-200"></i>
                                        @endif
                                    </div>
                                </a>
                                <div class="p-6 pt-5 flex flex-col flex-1">
                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">{{ $product->brand->name ?? 'Registry Item' }}</p>
                                    <h3 class="text-base font-black text-slate-900 leading-tight mb-4 group-hover:text-[var(--primary)] transition-colors line-clamp-2">{{ $product->name }}</h3>
                                    <div class="mt-auto flex items-center justify-between gap-4">
                                        <div>
                                            <p class="text-xl font-black text-slate-900">Rs {{ number_format($product->final_price, 2) }}</p>
                                            @if($product->discount_badge)<p class="text-[10px] text-slate-400 line-through">Rs {{ number_format($product->selling_price, 2) }}</p>@endif
                                        </div>
                                        <button type="button" class="shop-cart-btn h-10 w-10 flex items-center justify-center rounded-full text-white shadow-lg hover:shadow-indigo-500/30 transition-all hover:scale-110" style="background:linear-gradient(90deg, var(--primary), var(--secondary))" data-id="{{ $product->id }}">
                                            <i class="fas fa-shopping-cart text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach

        <!-- Critical Review Registry -->
        <section id="reviews" class="mx-auto max-w-7xl px-4 py-12">
            <div class="text-center mb-12">
                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-[var(--primary)] mb-3">Public Audit</p>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">{{ $reviewsSectionTitle }}</h2>
                <p class="mt-4 text-sm sm:text-base text-slate-500 max-w-2xl mx-auto">{{ $reviewsSectionSubtitle }}</p>
            </div>
            <div class="grid gap-8 md:grid-cols-3">
                @forelse($reviews as $review)
                    <article class="premium-card p-10 flex flex-col justify-between">
                        <div>
                            <div class="flex gap-1 mb-6">
                                @for($i=0;$i<5;$i++)<i class="fas fa-star text-[10px] {{ $i < $review->rating ? 'text-amber-400' : 'text-slate-200' }}"></i>@endfor
                            </div>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed italic">"{{ $review->body ?: $review->title }}"</p>
                        </div>
                        <div class="mt-8 pt-8 border-t border-slate-50 dark:border-white/5 flex items-center gap-4">
                            <div class="h-10 w-10 rounded-full bg-slate-900 dark:bg-white flex items-center justify-center text-white dark:text-slate-900 text-[10px] font-black">
                                {{ strtoupper(substr($review->user->name ?? 'V', 0, 1) ?: 'V') }}
                            </div>
                            <div>
                                <p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">{{ $review->user->name ?? 'Verified Auditor' }}</p>
                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Verified Purchase</p>
                            </div>
                        </div>
                    </article>
                @empty
                    @foreach([['Exceptional logistics and item integrity.','Kumari P.'],['Strategic pricing and flawless registration process.','Dilan S.'],['Premium interface and rapid administrative support.','Ruwani M.']] as [$text,$name])
                        <article class="premium-card p-10">
                            <div class="flex gap-1 mb-6 text-amber-400">@for($i=0;$i<5;$i++)<i class="fas fa-star text-[10px]"></i>@endfor</div>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed italic">"{{ $text }}"</p>
                            <div class="mt-8 pt-8 border-t border-slate-50 dark:border-white/5 flex items-center gap-4">
                                <div class="h-10 w-10 rounded-full bg-slate-900 dark:bg-white flex items-center justify-center text-white dark:text-slate-900 text-[10px] font-black">{{ strtoupper(substr($name, 0, 1)) }}</div>
                                <div><p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">{{ $name }}</p></div>
                            </div>
                        </article>
                    @endforeach
                @endforelse
            </div>
        </section>

        <!-- Final Protocol CTA -->
        <section class="mx-auto max-w-7xl px-4">
            <div class="relative rounded-[3.5rem] px-8 py-20 text-center text-white overflow-hidden shadow-2xl" style="background:linear-gradient(135deg, var(--primary), var(--secondary), var(--accent))">
                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.5em] text-white/60 mb-6">Execution Phase</p>
                    <h2 class="text-3xl sm:text-4xl lg:text-6xl font-black tracking-tight mb-6">{{ $finalCtaTitle }}</h2>
                    <p class="mx-auto max-w-2xl text-lg text-white/80 mb-12">{{ $finalCtaSubtitle }}</p>
                    <a wire:navigate href="{{ url($finalCtaButtonLink) }}" class="inline-flex w-full sm:w-auto justify-center items-center gap-4 rounded-full bg-white px-12 py-4 sm:py-5 text-xs font-black text-slate-900 uppercase tracking-[0.3em] shadow-xl hover:scale-105 transition-all group">
                        {{ $finalCtaButtonText }}
                        <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Global Footer -->
    <footer id="footer" class="mt-24 px-4 pb-12">
        <div class="mx-auto max-w-7xl rounded-[3.5rem] bg-slate-950 px-8 py-12 sm:px-12 sm:py-20 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-1/3 h-1/3 bg-indigo-500/10 blur-[120px] rounded-full"></div>
            <div class="relative z-10 grid gap-10 sm:gap-16 lg:grid-cols-[1.5fr_1fr_1fr_1fr]">
                <div>
                    <div class="flex items-center gap-3 mb-8">
                        @if($logoPath)
                            <img src="{{ Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-10 w-auto brightness-0 invert">
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
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-500 mb-8">System</h3>
                    <nav class="flex flex-col gap-4 text-xs font-bold uppercase tracking-widest text-slate-400">
                        <a wire:navigate href="{{ url('/products') }}" class="hover:text-white transition-colors">{{ $navProductsLabel }}</a>
                        <a wire:navigate href="{{ route('track-order') }}" class="hover:text-white transition-colors">Track Shipment</a>
                        <a wire:navigate href="{{ route('login') }}" class="hover:text-white transition-colors">Authentication</a>
                    </nav>
                </div>
                <div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-500 mb-8">Governance</h3>
                    <nav class="flex flex-col gap-4 text-xs font-bold uppercase tracking-widest text-slate-400">
                        <a wire:navigate href="{{ route('privacy-policy') }}" class="hover:text-white transition-colors">Privacy Protocol</a>
                        <a wire:navigate href="{{ route('terms-and-conditions') }}" class="hover:text-white transition-colors">Terms of Service</a>
                        <a wire:navigate href="{{ route('refund-policy') }}" class="hover:text-white transition-colors">Refund Charter</a>
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
