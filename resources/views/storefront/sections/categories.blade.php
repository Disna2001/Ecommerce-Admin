@props(['config' => []])
@php
    $title = $config['title'] ?? 'Shop by Category';
    $subtitle = $config['subtitle'] ?? 'Jump straight into the product family you need.';
    $seeAllLabel = $config['see_all_label'] ?? 'See All';
    $allLabel = $config['all_label'] ?? 'Full Catalog';
    $limit = max(1, (int)($config['limit'] ?? 8));
    $categories = App\Models\Category::query()->select('id', 'name')->orderBy('name')->take($limit)->get();
@endphp

<section id="categories" class="mx-auto max-w-7xl px-4 my-4">
    <div class="mb-4 flex items-end justify-between">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-1">{{ $subtitle }}</p>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $title }}</h2>
        </div>
        <a wire:navigate href="{{ url('/products') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
            {{ $seeAllLabel }} <i class="fas fa-arrow-right ml-1 text-[8px]"></i>
        </a>
    </div>

    @if($categories->isNotEmpty())
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3 sm:gap-4">
            <!-- Full Catalog Icon Tile -->
            <a wire:navigate href="{{ url('/products') }}" class="group flex flex-col items-center p-3 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-lg hover:border-indigo-500/40 transition-all text-center">
                <div class="flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-sm mb-2 group-hover:scale-105 transition-transform">
                    <i class="fas fa-layer-group text-lg"></i>
                </div>
                <span class="text-[11px] font-black uppercase tracking-tight text-slate-900 dark:text-white line-clamp-1 group-hover:text-indigo-600 transition-colors">
                    {{ $allLabel }}
                </span>
            </a>

            @foreach($categories as $category)
                <a wire:navigate href="{{ url('/products?category='.$category->id) }}" class="group flex flex-col items-center p-3 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-lg hover:border-indigo-500/40 transition-all text-center">
                    <div class="flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-2xl bg-indigo-50 dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-xs mb-2">
                        <i class="fas fa-tag text-base"></i>
                    </div>
                    <span class="text-[11px] font-bold text-slate-800 dark:text-slate-200 line-clamp-1 group-hover:text-indigo-600 transition-colors">
                        {{ $category->name }}
                    </span>
                </a>
            @endforeach
        </div>
    @else
        <!-- Dense Intentional 8-Item Category Grid Fallback -->
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3 sm:gap-4">
            <a wire:navigate href="{{ url('/products') }}" class="group flex flex-col items-center p-3 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-lg transition-all text-center">
                <div class="flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-sm mb-2 group-hover:scale-105 transition-transform">
                    <i class="fas fa-layer-group text-lg"></i>
                </div>
                <span class="text-[11px] font-black uppercase tracking-tight text-slate-900 dark:text-white line-clamp-1">
                    {{ $allLabel }}
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
            ] as [$catName, $icon])
                <a wire:navigate href="{{ url('/products') }}" class="group flex flex-col items-center p-3 rounded-2xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/60 hover:shadow-md transition-all text-center">
                    <div class="flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 group-hover:bg-indigo-600 group-hover:text-white transition-all mb-2">
                        <i class="fas {{ $icon }} text-base"></i>
                    </div>
                    <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 line-clamp-1 group-hover:text-indigo-600">
                        {{ $catName }}
                    </span>
                </a>
            @endforeach
        </div>
    @endif
</section>
