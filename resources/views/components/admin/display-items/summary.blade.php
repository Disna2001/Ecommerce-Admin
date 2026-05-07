<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
    @foreach([
        ['published', 'Published', 'Storefront Active', 'fa-globe', 'emerald'],
        ['featured', 'Featured', 'Homepage Hero', 'fa-star', 'amber'],
        ['new', 'New Arrivals', 'Fresh Catalog', 'fa-sparkles', 'sky'],
        ['deals', 'Best Deals', 'Promo Logic', 'fa-tag', 'indigo'],
        ['low_stock', 'Low Stock', 'Alert Status', 'fa-exclamation-triangle', 'rose']
    ] as [$key, $label, $sub, $icon, $color])
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm group hover:border-{{ $color }}-500 transition-all">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-{{ $color }}-50 text-{{ $color }}-600 group-hover:bg-{{ $color }}-500 group-hover:text-white transition-colors">
                    <i class="fas {{ $icon }} text-xs"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 leading-none mb-1">{{ $label }}</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-xl font-black text-slate-900 leading-none">{{ $displayStats[$key] }}</span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">{{ $sub }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
