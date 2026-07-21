@props(['config' => []])
@php
    $heading = $config['heading'] ?? 'Genuine Smartphone Displays & Parts';
    $subheading = $config['subheading'] ?? 'Original display assemblies, replacement screens, and electronic components.';
    $ctaText = $config['cta_text'] ?? 'Shop Collection';
    $ctaUrl = $config['cta_url'] ?? '/products';
    $bgColor = $config['bg_color'] ?? '#7c3aed';
    $heroImage = $config['hero_image'] ?? '';

    $tile1Title = $config['tile1_title'] ?? 'New Arrivals';
    $tile1Subtitle = $config['tile1_subtitle'] ?? 'Fresh display assemblies in stock';
    $tile1Link = $config['tile1_link'] ?? '/products?sort=newest';
    $tile1Badge = $config['tile1_badge'] ?? 'New In';

    $tile2Title = $config['tile2_title'] ?? 'Best Sellers';
    $tile2Subtitle = $config['tile2_subtitle'] ?? 'Top rated replacement modules';
    $tile2Link = $config['tile2_link'] ?? '/products';
    $tile2Badge = $config['tile2_badge'] ?? 'Hot';
@endphp

<section class="my-4 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">
        <!-- Main Hero Banner (Left 8 Cols) -->
        <div class="lg:col-span-8 relative overflow-hidden rounded-[2.5rem] p-6 sm:p-10 text-white shadow-2xl flex flex-col justify-between" style="background-color: {{ $bgColor }}">
            <div class="absolute inset-0 opacity-15 pointer-events-none" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 20px 20px;"></div>

            <div class="relative z-10 space-y-4 max-w-2xl">
                <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.25em] border border-white/20">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Official Catalog
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black leading-tight tracking-tight">
                    {{ $heading }}
                </h1>

                <p class="text-sm sm:text-base text-white/80 font-medium max-w-xl">
                    {{ $subheading }}
                </p>

                @if(!empty($ctaText))
                    <div class="pt-2">
                        <a wire:navigate href="{{ url($ctaUrl) }}" class="inline-flex items-center gap-3 px-7 py-3.5 rounded-full bg-white text-slate-900 text-xs font-black uppercase tracking-wider hover:bg-slate-100 transition-all shadow-lg hover:-translate-y-0.5 group">
                            <span>{{ $ctaText }}</span>
                            <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                @endif
            </div>

            @if(!empty($heroImage))
                <div class="mt-6 lg:mt-0 lg:absolute lg:right-6 lg:bottom-6 w-full lg:w-56 overflow-hidden rounded-2xl border border-white/20 shadow-lg">
                    <img src="{{ $heroImage }}" alt="{{ $heading }}" class="w-full h-36 lg:h-44 object-cover">
                </div>
            @endif
        </div>

        <!-- Stacked Side Promo Tiles (Right 4 Cols) -->
        <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-4 sm:gap-6">
            <!-- Tile 1 -->
            <a wire:navigate href="{{ url($tile1Link) }}" class="flex-1 group relative overflow-hidden rounded-[2rem] bg-slate-900 p-6 text-white shadow-xl hover:scale-[1.02] transition-all border border-slate-800 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="rounded-full bg-indigo-500/20 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-indigo-300 border border-indigo-400/30">
                        {{ $tile1Badge }}
                    </span>
                    <i class="fas fa-arrow-right text-xs text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black tracking-tight text-white group-hover:text-amber-300 transition-colors">{{ $tile1Title }}</h3>
                    <p class="text-xs text-slate-400 mt-1">{{ $tile1Subtitle }}</p>
                </div>
            </a>

            <!-- Tile 2 -->
            <a wire:navigate href="{{ url($tile2Link) }}" class="flex-1 group relative overflow-hidden rounded-[2rem] bg-slate-800 p-6 text-white shadow-xl hover:scale-[1.02] transition-all border border-slate-700 flex flex-col justify-between" style="background:linear-gradient(135deg, #1e293b 0%, #334155 100%)">
                <div class="flex items-center justify-between mb-3">
                    <span class="rounded-full bg-amber-500/20 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-amber-300 border border-amber-400/30">
                        {{ $tile2Badge }}
                    </span>
                    <i class="fas fa-arrow-right text-xs text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black tracking-tight text-white group-hover:text-cyan-300 transition-colors">{{ $tile2Title }}</h3>
                    <p class="text-xs text-slate-400 mt-1">{{ $tile2Subtitle }}</p>
                </div>
            </a>
        </div>
    </div>
</section>
