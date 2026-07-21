@props(['config' => []])
@php
    $title = $config['title'] ?? 'New Arrivals';
    $subtitle = $config['subtitle'] ?? 'Fresh products customers should notice first.';
    $limit = (int)($config['limit'] ?? 6);
    $products = \App\Models\Stock::visibleOnStorefront()->latest()->take($limit)->get();
@endphp

<section class="my-6 max-w-7xl mx-auto px-4">
    <div class="mb-4 flex items-end justify-between">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-600 dark:text-emerald-400 mb-1">Fresh Stock</p>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $title }}</h2>
        </div>
        <a wire:navigate href="{{ url('/products?sort=newest') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
            View All <i class="fas fa-arrow-right ml-1 text-[8px]"></i>
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
        @forelse($products as $product)
            @include('storefront.partials.product-card', ['product' => $product, 'badge' => 'New In'])
        @empty
            <div class="col-span-full py-12 text-center rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-800">
                <i class="fas fa-box-open text-3xl text-slate-300 mb-2"></i>
                <p class="text-xs font-bold text-slate-400">No new arrival items available.</p>
            </div>
        @endforelse
    </div>
</section>
