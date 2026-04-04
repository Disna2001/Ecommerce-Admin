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
        :root { --primary: {{ $primaryColor }}; --secondary: {{ $secondaryColor }}; --accent: {{ $accentColor }}; }
        body { font-family: 'Figtree', sans-serif; }
        .shell { background: radial-gradient(circle at top left, rgba(109,40,217,.15), transparent 28%), radial-gradient(circle at top right, rgba(6,182,212,.14), transparent 24%), #f5f3ff; color: #24183f; }
        .dark .shell { background: radial-gradient(circle at top left, rgba(109,40,217,.28), transparent 28%), radial-gradient(circle at top right, rgba(6,182,212,.18), transparent 22%), #0f1020; color: #f5f3ff; }
        .glass { background: rgba(255,255,255,.78); border: 1px solid rgba(139,92,246,.12); backdrop-filter: blur(16px); }
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
        <div class="glass card mx-auto flex max-w-7xl items-center justify-between rounded-full px-4 py-3 lg:px-6">
            <div class="flex items-center gap-4">
                <a href="/" class="flex items-center gap-3">
                    @if($logoPath)
                        <img src="{{ Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-10 w-auto object-contain">
                    @else
                        <span class="text-2xl font-black lowercase" style="color:var(--primary)">{{ strtolower($siteName) }}</span>
                    @endif
                </a>
                <nav class="hidden items-center gap-5 text-sm font-medium lg:flex">
                    <a wire:navigate href="{{ url('/products') }}">Products</a>
                    <a href="#categories">Categories</a>
                    <a href="#deals">Deals</a>
                    <a href="#reviews">Reviews</a>
                    <a wire:navigate href="{{ route('track-order') }}">Track</a>
                    <a wire:navigate href="{{ route('help-center') }}">Help</a>
                    <a href="#footer">Contact</a>
                </nav>
            </div>

            <form action="{{ url('/products') }}" method="GET" class="relative hidden w-full max-w-md md:block">
                <input type="text" name="search" placeholder="{{ $searchPlaceholder }}" class="w-full rounded-full border border-white/40 bg-white/70 px-11 py-3 text-sm text-slate-700 outline-none dark:border-white/10 dark:bg-slate-900/60 dark:text-white">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
            </form>

            <div class="flex items-center gap-2">
                <button @click="toggle()" type="button" class="flex h-11 w-11 items-center justify-center rounded-full border border-white/30 bg-white/70 text-slate-700 dark:border-white/10 dark:bg-slate-900/60 dark:text-white">
                    <i class="fas" :class="dark ? 'fa-sun' : 'fa-moon'"></i>
                </button>
                <a wire:navigate href="{{ url('/wishlist') }}" class="relative flex h-11 w-11 items-center justify-center rounded-full border border-white/30 bg-white/70 dark:border-white/10 dark:bg-slate-900/60"><i class="far fa-heart"></i>@if($wishCount>0)<span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white">{{ $wishCount }}</span>@endif</a>
                <a wire:navigate href="{{ url('/cart') }}" class="relative flex h-11 w-11 items-center justify-center rounded-full border border-white/30 bg-white/70 dark:border-white/10 dark:bg-slate-900/60"><i class="fas fa-bag-shopping"></i>@if($cartCount>0)<span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-[16px] items-center justify-center rounded-full px-1 text-[10px] font-bold text-white" style="background:var(--primary)">{{ $cartCount }}</span>@endif</a>
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

    <main class="px-4 pb-16">
        <section class="mx-auto mt-4 max-w-7xl rounded-[2rem] px-6 py-12 card" style="background:linear-gradient(180deg, #efe9ff 0%, #f7f4ff 100%)">
            <div class="grid gap-8 lg:grid-cols-[1.08fr_0.92fr] lg:items-center">
                <div class="mx-auto max-w-4xl text-center lg:mx-0 lg:max-w-none lg:text-left">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em]" style="color:var(--primary)">{{ $siteTagline }}</p>
                    <h1 class="mt-6 text-4xl font-black leading-tight text-slate-900 sm:text-5xl lg:text-6xl">
                        {{ $heroTitle }}
                        <span class="block" style="color:var(--primary)">{{ $heroHighlight }}</span>
                    </h1>
                    <p class="mx-auto mt-5 max-w-2xl text-base leading-8 text-slate-600 lg:mx-0">{{ $heroSubtitle }} <span class="font-medium text-slate-800">{{ $heroMicrocopy }}</span></p>
                    <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row lg:justify-start">
                        <a wire:navigate href="{{ $heroBtnLink === '#' ? url('/products') : url($heroBtnLink) }}" class="inline-flex items-center gap-2 rounded-full px-7 py-3 text-sm font-semibold text-white" style="background:linear-gradient(90deg, var(--primary), var(--secondary))">{{ $heroBtnText }} <i class="fas fa-arrow-right text-xs"></i></a>
                        @guest <a wire:navigate href="{{ route('register') }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white/80 px-7 py-3 text-sm font-semibold text-slate-700">Create account</a> @endguest
                    </div>
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-5 text-sm font-medium text-slate-700 lg:justify-start">
                        @foreach([$featureOne, $featureTwo, $featureThree, $featureFour] as $feature)
                            <span class="inline-flex items-center gap-2"><i class="fas fa-star text-[11px]" style="color:var(--primary)"></i>{{ $feature }}</span>
                        @endforeach
                    </div>
                </div>

                @if($heroImagePath)
                    <div class="mx-auto w-full max-w-xl">
                        <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/70 p-3 shadow-[0_25px_60px_rgba(88,28,135,0.14)]">
                            <img src="{{ Storage::url($heroImagePath) }}" alt="{{ $heroTitle }}" class="h-[320px] w-full rounded-[1.5rem] object-cover sm:h-[380px]">
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <section id="categories" class="mx-auto mt-5 flex max-w-7xl gap-3 overflow-x-auto pb-2">
            <a wire:navigate href="{{ url('/products') }}" class="shrink-0 rounded-2xl px-5 py-3 text-sm font-semibold text-white" style="background:linear-gradient(90deg, var(--primary), var(--secondary))">All Products</a>
            @foreach($categories as $category)
                <a wire:navigate href="{{ url('/products?category='.$category->id) }}" class="glass shrink-0 rounded-2xl px-5 py-3 text-sm font-medium">{{ $category->name }}</a>
            @endforeach
        </section>

        @foreach([['deals',$dealsTitle,$deals,'Hot Sale'],['featured',$featuredTitle,$featured,'Featured'],['new-arrivals',$newTitle,$newArrivals,'New']] as [$sectionId,$sectionTitle,$items,$badge])
            @if($items->isNotEmpty())
                <section id="{{ $sectionId }}" class="mx-auto mt-12 max-w-7xl">
                    <div class="mb-6 flex items-end justify-between gap-4">
                        <div><h2 class="text-3xl font-bold">{{ $sectionTitle }}</h2><p class="muted mt-1 text-sm">Handpicked for fast-moving shoppers.</p></div>
                        <a wire:navigate href="{{ url('/products') }}" class="text-sm font-semibold" style="color:var(--primary)">View All <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach($items as $product)
                            <article class="product-card card overflow-hidden rounded-[1.75rem] p-3">
                                <a wire:navigate href="{{ url('/products/'.$product->id) }}" class="relative block overflow-hidden rounded-[1.25rem]">
                                    <div class="absolute left-3 top-3 z-10 rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-white" style="background:linear-gradient(90deg, #f97316, #ef4444)">{{ $badge }}</div>
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
                        <a wire:navigate class="block" href="{{ url('/products') }}">All Products</a>
                        <a class="block" href="#deals">Deals</a>
                        <a class="block" href="#reviews">Reviews</a>
                        <a wire:navigate class="block" href="{{ route('track-order') }}">Track Order</a>
                        <a wire:navigate class="block" href="{{ route('login') }}">Login</a>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.22em] text-white/60">Support</h3>
                    <div class="mt-4 space-y-3 text-sm text-white/75">
                        <div>Track Order</div>
                        <div>Help Center</div>
                        <div>Terms & Conditions</div>
                        <div>Privacy Policy</div>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.22em] text-white/60">Contact</h3>
                    <div class="mt-4 space-y-3 text-sm text-white/75">
                        <div>{{ $utilityLeft }}</div>
                        <div>{{ $utilityCenter }}</div>
                        <div>{{ $featureOne }}</div>
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
document.addEventListener('click', function(e){const cartBtn=e.target.closest('.shop-cart-btn');if(!cartBtn)return;e.preventDefault();const original=cartBtn.innerHTML;cartBtn.innerHTML='<i class="fas fa-spinner fa-spin"></i>';fetch('/cart/add/'+cartBtn.dataset.id,{method:'POST',headers:{'X-CSRF-TOKEN':window._token,'Content-Type':'application/json'},body:JSON.stringify({quantity:1})}).then(r=>r.json()).then(()=>{cartBtn.innerHTML='Added';setTimeout(()=>cartBtn.innerHTML=original,1400);}).catch(()=>cartBtn.innerHTML=original);});
</script>
</body>
</html>
