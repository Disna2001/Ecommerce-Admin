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
        body { font-family: 'Figtree', sans-serif; }
        .shell { background: radial-gradient(circle at top left, rgba(109,40,217,.15), transparent 28%), radial-gradient(circle at top right, rgba(6,182,212,.14), transparent 24%), var(--site-bg); color: var(--site-text); }
        .dark .shell { background: radial-gradient(circle at top left, rgba(109,40,217,.28), transparent 28%), radial-gradient(circle at top right, rgba(6,182,212,.18), transparent 22%), #0f1020; color: #f5f3ff; }
        .glass { background: color-mix(in srgb, var(--nav-bg) 78%, white 22%); border: 1px solid rgba(139,92,246,.12); backdrop-filter: blur(16px); }
        .dark .glass { background: rgba(15,23,42,.72); border-color: rgba(255,255,255,.08); }
        .muted { color: #6b6480; } .dark .muted { color: #c8bdf0; }
        .card { box-shadow: 0 18px 48px rgba(88,28,135,.10); }
        .dark .card { box-shadow: 0 20px 60px rgba(0,0,0,.35); }
        .product-card { background: rgba(255,255,255,.92); border: 1px solid rgba(139,92,246,.10); }
        .dark .product-card { background: rgba(15,23,42,.92); border-color: rgba(255,255,255,.06); }
    </style>
</head>
<body class="shell">
<div class="min-h-screen">
    <div id="site-progress" class="pointer-events-none fixed left-0 top-0 z-[70] h-1 w-0 opacity-0 transition-[width,opacity] duration-300" style="background:linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));"></div>
    <livewire:storefront.header-bar />

    <main class="px-4 pb-16">
        <section class="mx-auto mt-4 max-w-7xl rounded-[2rem] px-6 py-12 card
            {{ $heroSurface === 'minimal' ? 'bg-transparent shadow-none' : '' }}
            {{ $heroSurface === 'solid' ? '' : '' }}"
            style="background:{{ $heroSurface === 'minimal' ? 'transparent' : 'linear-gradient(180deg, '.$heroBgFrom.' 0%, '.$heroBgTo.' 100%)' }}">
            <div class="{{ $heroLayout === 'centered' ? 'mx-auto max-w-4xl text-center' : ($heroLayout === 'stacked' ? 'space-y-8' : 'grid gap-8 lg:grid-cols-[1.08fr_0.92fr] lg:items-center') }}">
                <div class="mx-auto max-w-4xl {{ $heroLayout === 'centered' || $heroAlignment === 'center' ? 'text-center' : 'lg:mx-0 lg:max-w-none lg:text-left text-center' }}">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em]" style="color:var(--primary)">{{ $siteTagline }}</p>
                    <h1 class="mt-6 text-4xl font-black leading-tight text-slate-900 sm:text-5xl lg:text-6xl {{ $heroSurface !== 'minimal' ? 'text-white' : '' }}">
                        {{ $heroTitle }}
                        <span class="block" style="color:var(--primary)">{{ $heroHighlight }}</span>
                    </h1>
                    <p class="mx-auto mt-5 max-w-2xl text-base leading-8 {{ $heroSurface !== 'minimal' ? 'text-white/85' : 'text-slate-600' }} {{ $heroLayout === 'centered' || $heroAlignment === 'center' ? '' : 'lg:mx-0' }}">{{ $heroSubtitle }} <span class="font-medium {{ $heroSurface !== 'minimal' ? 'text-white' : 'text-slate-800' }}">{{ $heroMicrocopy }}</span></p>
                    <div class="mt-8 flex flex-col items-center gap-4 sm:flex-row {{ $heroLayout === 'centered' || $heroAlignment === 'center' ? 'justify-center' : 'lg:justify-start justify-center' }}">
                        <a wire:navigate href="{{ $heroBtnLink === '#' ? url('/products') : url($heroBtnLink) }}" class="inline-flex items-center gap-2 rounded-full px-7 py-3 text-sm font-semibold text-white" style="background:linear-gradient(90deg, var(--primary), var(--secondary))">{{ $heroBtnText }} <i class="fas fa-arrow-right text-xs"></i></a>
                        @guest <a wire:navigate href="{{ route('register') }}" class="inline-flex items-center rounded-full border {{ $heroSurface !== 'minimal' ? 'border-white/25 bg-white/10 text-white' : 'border-slate-300 bg-white/80 text-slate-700' }} px-7 py-3 text-sm font-semibold">Create account</a> @endguest
                    </div>
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-5 text-sm font-medium {{ $heroSurface !== 'minimal' ? 'text-white/85' : 'text-slate-700' }} {{ $heroLayout === 'centered' || $heroAlignment === 'center' ? '' : 'lg:justify-start' }}">
                        @foreach([$featureOne, $featureTwo, $featureThree, $featureFour] as $feature)
                            <span class="inline-flex items-center gap-2"><i class="fas fa-star text-[11px]" style="color:var(--primary)"></i>{{ $feature }}</span>
                        @endforeach
                    </div>
                </div>

                @if($heroImagePath && $heroLayout !== 'centered')
                    <div class="mx-auto w-full max-w-xl">
                        <div class="overflow-hidden rounded-[2rem] border {{ $heroSurface !== 'minimal' ? 'border-white/20 bg-white/10' : 'border-white/70 bg-white/70' }} p-3 shadow-[0_25px_60px_rgba(88,28,135,0.14)]">
                            <img src="{{ Storage::url($heroImagePath) }}" alt="{{ $heroTitle }}" class="h-[320px] w-full rounded-[1.5rem] object-cover sm:h-[380px]">
                        </div>
                    </div>
                @endif
            </div>
            @if($heroBanners->isNotEmpty())
                <div class="mt-8 grid gap-4 md:grid-cols-3" style="grid-template-columns: repeat({{ min(3, max(1, $heroBanners->count())) }}, minmax(0, 1fr));">
                    @foreach($heroBanners as $banner)
                        <div class="rounded-[1.5rem] border border-white/15 p-5 text-white shadow-[0_16px_48px_rgba(15,23,42,0.18)]" style="background:linear-gradient(135deg, {{ $banner->bg_color }}, {{ $banner->bg_color }}dd); color: {{ $banner->text_color }};">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em]" style="color: {{ $banner->text_color }}cc;">{{ $banner->subtitle ?: 'Storefront Banner' }}</p>
                            <h3 class="mt-3 text-xl font-bold">{{ $banner->title }}</h3>
                            @if($banner->caption)
                                <p class="mt-2 text-sm" style="color: {{ $banner->text_color }}dd;">{{ $banner->caption }}</p>
                            @endif
                            @if($banner->button_text)
                                <a href="{{ $banner->button_link ?: '#' }}" class="mt-4 inline-flex rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-900">{{ $banner->button_text }}</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section id="categories" class="mx-auto mt-8 max-w-7xl">
            <div class="mb-4 flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em]" style="color:var(--primary)">{{ $categoryStripTitle }}</p>
                    <p class="mt-2 text-sm text-slate-500">{{ $categoryStripSubtitle }}</p>
                </div>
            </div>
            <div class="{{ $categoryStripStyle === 'cards' ? 'grid gap-4 sm:grid-cols-2 xl:grid-cols-4' : 'flex gap-3 overflow-x-auto pb-2' }}">
                <a wire:navigate href="{{ url('/products') }}" class="{{ $categoryStripStyle === 'cards' ? 'min-h-[110px]' : 'shrink-0' }} rounded-2xl px-5 py-4 text-sm font-semibold text-white" style="background:linear-gradient(90deg, var(--primary), var(--secondary))">
                    <span class="inline-flex items-center gap-2">
                        @if($categoryShowIcons)<i class="fas fa-table-cells-large"></i>@endif
                        All Products
                    </span>
                </a>
                @foreach($categories as $category)
                    <a wire:navigate href="{{ url('/products?category='.$category->id) }}" class="glass {{ $categoryStripStyle === 'cards' ? 'min-h-[110px] rounded-[1.5rem] p-5' : 'shrink-0 rounded-2xl px-5 py-3' }} text-sm font-medium">
                        @if($categoryShowIcons)
                            <span class="mb-2 inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white/70 text-violet-600">
                                <i class="fas {{ ($categoryIcons[$category->id] ?? 'fa-tag') }}"></i>
                            </span>
                        @endif
                        <span class="block">{{ $category->name }}</span>
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
            <section class="mx-auto mt-12 max-w-7xl">
                <div class="mb-6 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em]" style="color:var(--primary)">Recommended For You</p>
                        <h2 class="mt-2 text-3xl font-bold">A smarter shortlist based on your activity</h2>
                        <p class="muted mt-2 text-sm">The storefront now adapts suggestions from your views, wishlist, reviews, and orders.</p>
                    </div>
                    <a wire:navigate href="{{ url('/products') }}" class="text-sm font-semibold" style="color:var(--primary)">Browse All <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
                </div>
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach($personalizedRecommendations as $product)
                        <article class="product-card card overflow-hidden rounded-[1.75rem] p-3">
                            <a wire:navigate href="{{ url('/products/'.$product->id) }}" class="relative block overflow-hidden rounded-[1.25rem]">
                                <div class="absolute left-3 top-3 z-10 rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-white" style="background:linear-gradient(90deg, var(--primary), var(--secondary))">For You</div>
                                <div class="flex h-64 items-center justify-center rounded-[1.25rem] bg-gradient-to-br from-white to-violet-50 p-6 dark:from-slate-900 dark:to-slate-800">
                                    @if($product->primary_image_url)
                                        <img src="{{ $product->primary_image_sources['fallback'] ?? $product->primary_image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                    @endif
                                </div>
                            </a>
                            <div class="px-2 pb-2 pt-4">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-400">{{ $product->brand->name ?? ($product->category->name ?? 'Digital Product') }}</p>
                                <h3 class="mt-2 line-clamp-2 text-base font-semibold">{{ $product->name }}</h3>
                                <p class="mt-4 text-lg font-black" style="color:var(--primary)">Rs {{ number_format($product->final_price ?? $product->selling_price, 2) }}</p>
                                <div class="mt-4 flex justify-between gap-3">
                                    <a wire:navigate href="{{ url('/products/'.$product->id) }}" class="inline-flex h-10 items-center justify-center rounded-full border border-slate-200 px-4 text-xs font-bold text-slate-700">View</a>
                                    <button type="button" class="shop-cart-btn rounded-full px-4 py-2 text-xs font-bold text-white" style="background:linear-gradient(90deg, var(--primary), var(--secondary))" data-id="{{ $product->id }}">Buy Now</button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        @if($promoBanners->isNotEmpty())
            <section class="mx-auto mt-8 max-w-7xl">
                <div class="grid gap-4 lg:grid-cols-3">
                    @foreach($promoBanners as $banner)
                        <article class="overflow-hidden rounded-[1.75rem] border border-white/40 shadow-[0_18px_54px_rgba(15,23,42,0.12)]" style="background:linear-gradient(135deg, {{ $banner->bg_color }}, {{ $banner->bg_color }}cc); color: {{ $banner->text_color }};">
                            <div class="p-6">
                                @if($banner->subtitle)
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em]" style="color: {{ $banner->text_color }}cc;">{{ $banner->subtitle }}</p>
                                @endif
                                <h3 class="mt-3 text-2xl font-black">{{ $banner->title }}</h3>
                                @if($banner->caption)
                                    <p class="mt-3 text-sm leading-7" style="color: {{ $banner->text_color }}dd;">{{ $banner->caption }}</p>
                                @endif
                                @if($banner->button_text)
                                    <a href="{{ $banner->button_link ?: '#' }}" class="mt-5 inline-flex items-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-900">
                                        {{ $banner->button_text }}
                                        <i class="fas fa-arrow-right text-xs"></i>
                                    </a>
                                @endif
                            </div>
                            @if($banner->image_path)
                                <img src="{{ Storage::url($banner->image_path) }}" alt="{{ $banner->title }}" class="h-44 w-full object-cover">
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        @php
            $railGridClass = match($railLayout) {
                'compact' => 'grid gap-4 sm:grid-cols-2 xl:grid-cols-5',
                'editorial' => 'grid gap-6 md:grid-cols-2 xl:grid-cols-3',
                default => 'grid gap-5 sm:grid-cols-2 xl:grid-cols-4',
            };
            $railCardClass = match($railLayout) {
                'compact' => 'product-card card overflow-hidden rounded-[1.5rem] p-3',
                'editorial' => 'product-card card overflow-hidden rounded-[2rem] p-4',
                default => 'product-card card overflow-hidden rounded-[1.75rem] p-3',
            };
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
                <section id="{{ $sectionId }}" class="mx-auto mt-12 max-w-7xl">
                    <div class="mb-6 flex items-end justify-between gap-4">
                        <div><h2 class="text-3xl font-bold">{{ $sectionTitle }}</h2><p class="muted mt-1 text-sm">{{ $sectionSubtitles[$sectionId] ?? 'Handpicked for fast-moving shoppers.' }}</p></div>
                        <a wire:navigate href="{{ url('/products') }}" class="text-sm font-semibold" style="color:var(--primary)">View All <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
                    </div>
                    <div class="{{ $railGridClass }}">
                        @foreach($items as $product)
                            <article class="{{ $railCardClass }}">
                                <a wire:navigate href="{{ url('/products/'.$product->id) }}" class="relative block overflow-hidden rounded-[1.25rem]">
                                    <div class="absolute left-3 top-3 z-10 rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-white" style="background:linear-gradient(90deg, #f97316, #ef4444)">{{ $badge }}</div>
                                    @if($showRailStockStatus)
                                        <div class="absolute right-3 top-3 z-10 rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-[0.18em] {{ $product->quantity <= 0 ? 'bg-rose-500 text-white' : ($product->isLowStock() ? 'bg-amber-400 text-slate-900' : 'bg-emerald-400 text-slate-900') }}">
                                            {{ $product->quantity <= 0 ? 'Out of stock' : ($product->isLowStock() ? 'Low stock' : 'In stock') }}
                                        </div>
                                    @endif
                                    <div class="flex h-64 items-center justify-center rounded-[1.25rem] bg-gradient-to-br from-white to-violet-50 p-6 dark:from-slate-900 dark:to-slate-800">
                                        @if($product->primary_image_url)
                                            <picture class="block h-full w-full">
                                                @if(!empty($product->primary_image_sources['webp']))
                                                    <source srcset="{{ $product->primary_image_sources['webp'] }}" type="image/webp">
                                                @endif
                                                @if(!empty($product->primary_image_sources['jpeg']))
                                                    <source srcset="{{ $product->primary_image_sources['jpeg'] }}" type="image/jpeg">
                                                @endif
                                                <img src="{{ $product->primary_image_sources['fallback'] ?? $product->primary_image_url }}" alt="{{ $product->name }}" loading="lazy" decoding="async" class="h-full w-full object-cover">
                                            </picture>
                                        @else
                                            <div class="text-center"><i class="fas fa-box-open text-5xl" style="color:var(--primary)"></i><p class="mt-3 text-sm font-semibold">{{ $product->brand->name ?? $siteName }}</p></div>
                                        @endif
                                    </div>
                                </a>
                                <div class="px-2 pb-2 pt-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">{{ $product->brand->name ?? ($product->category->name ?? 'Digital Product') }}</p>
                                    <h3 class="mt-2 line-clamp-2 text-base font-semibold">{{ $product->name }}</h3>
                                    <p class="muted mt-2 line-clamp-2 text-sm">{{ $product->model_name ?: 'Fast digital delivery with account-backed access.' }}</p>
                                    @if($showRailQuantity)
                                        <p class="mt-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Available now: {{ $product->quantity }}</p>
                                    @endif
                                    <div class="mt-4 flex items-end justify-between gap-3">
                                        <div>
                                            <p class="text-lg font-black" style="color:var(--primary)">Rs {{ number_format($product->final_price, 2) }}</p>
                                            @if($product->discount_badge)<p class="text-xs text-slate-400 line-through">Rs {{ number_format($product->selling_price, 2) }}</p>@endif
                                        </div>
                                        <button type="button" class="shop-cart-btn rounded-full px-4 py-2 text-xs font-bold text-white" style="background:linear-gradient(90deg, var(--primary), var(--secondary))" data-id="{{ $product->id }}">Buy Now</button>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach

        <section id="reviews" class="mx-auto mt-16 max-w-7xl">
            <div class="text-center">
                <h2 class="text-3xl font-bold">{{ $reviewsSectionTitle }}</h2>
                <p class="muted mt-2 text-sm">{{ $reviewsSectionSubtitle }}</p>
            </div>
            <div class="mt-8 grid gap-5 md:grid-cols-3">
                @forelse($reviews as $review)
                    <article class="glass card rounded-[1.5rem] p-6">
                        <div class="text-amber-400">@for($i=0;$i<5;$i++)<i class="fas fa-star text-sm {{ $i < $review->rating ? '' : 'opacity-30' }}"></i>@endfor</div>
                        <p class="mt-4 text-sm leading-7">{{ $review->body ?: $review->title }}</p>
                        <div class="mt-5 text-sm font-semibold">{{ $review->user->name ?? 'Verified Customer' }}</div>
                    </article>
                @empty
                    @foreach([['Fast delivery and smooth activation.','Kumari P.'],['Best prices I found and everything worked exactly as promised.','Dilan S.'],['Clean buying experience and very quick support.','Ruwani M.']] as [$text,$name])
                        <article class="glass card rounded-[1.5rem] p-6">
                            <div class="text-amber-400">@for($i=0;$i<5;$i++)<i class="fas fa-star text-sm"></i>@endfor</div>
                            <p class="mt-4 text-sm leading-7">{{ $text }}</p>
                            <div class="mt-5 text-sm font-semibold">{{ $name }}</div>
                        </article>
                    @endforeach
                @endforelse
            </div>
        </section>

        <section class="mx-auto mt-16 max-w-7xl rounded-[2rem] px-8 py-14 text-center text-white card" style="background:linear-gradient(120deg, var(--primary), var(--secondary), var(--accent))">
            <h2 class="text-3xl font-black sm:text-4xl">{{ $finalCtaTitle }}</h2>
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-white/85 sm:text-base">{{ $finalCtaSubtitle }}</p>
            <a wire:navigate href="{{ url($finalCtaButtonLink) }}" class="mt-8 inline-flex items-center gap-2 rounded-full bg-white px-7 py-3 text-sm font-bold text-slate-900">{{ $finalCtaButtonText }} <i class="fas fa-arrow-right text-xs"></i></a>
        </section>
    </main>

    <footer id="footer" class="mt-16 px-4 pb-10">
        <div class="mx-auto max-w-7xl rounded-[2rem] bg-slate-950 px-8 py-10 text-white card">
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
                    <h3 class="text-sm font-semibold uppercase tracking-[0.22em] text-white/60">Quick Links</h3>
                    <div class="mt-4 space-y-3 text-sm text-white/75">
                        <a wire:navigate class="block" href="{{ url('/products') }}">{{ $navProductsLabel }}</a>
                        @if($showDealsLink)
                            <a class="block" href="#deals">{{ $navDealsLabel }}</a>
                        @endif
                        <a class="block" href="#reviews">Reviews</a>
                        <a wire:navigate class="block" href="{{ route('track-order') }}">Track Order</a>
                        <a wire:navigate class="block" href="{{ route('refund-policy') }}">Refund Policy</a>
                        <a wire:navigate class="block" href="{{ route('privacy-policy') }}">Privacy Policy</a>
                        <a wire:navigate class="block" href="{{ route('terms-and-conditions') }}">Terms & Conditions</a>
                        <a wire:navigate class="block" href="{{ route('login') }}">Login</a>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.22em] text-white/60">Support</h3>
                    <div class="mt-4 space-y-3 text-sm text-white/75">
                        <a wire:navigate class="block hover:text-white" href="{{ route('track-order') }}">{{ $navTrackLabel }}</a>
                        <a wire:navigate class="block hover:text-white" href="{{ route('help-center') }}">{{ $navHelpLabel }}</a>
                        <a wire:navigate class="block hover:text-white" href="{{ route('help-center') }}">Payment Help</a>
                        <div>{{ $supportHours }}</div>
                        @if($supportEmail)<div>{{ $supportEmail }}</div>@endif
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.22em] text-white/60">Contact</h3>
                    <div class="mt-4 space-y-3 text-sm text-white/75">
                        @if($supportPhone)<div>{{ $supportPhone }}</div>@endif
                        @if($supportWhatsapp)<div>WhatsApp: {{ $supportWhatsapp }}</div>@endif
                        @if($supportEmail)<div>{{ $supportEmail }}</div>@endif
                        <div>{{ $utilityCenter }}</div>
                    </div>
                </div>
            </div>
            <div class="mt-10 border-t border-white/10 pt-5 text-xs text-white/50">{{ $footerCopy }}</div>
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
