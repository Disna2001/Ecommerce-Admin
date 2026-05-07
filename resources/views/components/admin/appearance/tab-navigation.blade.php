<div class="space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-600 shadow-inner">
            <i class="fas fa-compass text-lg"></i>
        </div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Navigational Architecture</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Global Menu & Support Identity</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1fr_400px]">
        <div class="space-y-8">
            <!-- Navigation Taxonomy -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Menu Taxonomy labels</p>
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach([
                        ['nav_products_label', 'Products'],
                        ['nav_categories_label', 'Categories'],
                        ['nav_deals_label', 'Deals'],
                        ['nav_reviews_label', 'Reviews'],
                        ['nav_track_label', 'Order Tracking'],
                        ['nav_help_label', 'Support Hub']
                    ] as [$key, $label])
                        <div class="group">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $label }}</label>
                            <input type="text" wire:model="{{ $key }}" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-2">
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <button 
                            wire:click="$set('show_deals_link', {{ !$show_deals_link ? 'true' : 'false' }})"
                            class="relative inline-flex h-7 w-14 items-center rounded-full transition-colors focus:outline-none {{ $show_deals_link ? 'bg-orange-500' : 'bg-slate-200' }}"
                        >
                            <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform {{ $show_deals_link ? 'translate-x-8' : 'translate-x-1' }}"></span>
                        </button>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Header Deals</span>
                    </div>
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <button 
                            wire:click="$set('show_new_arrivals_link', {{ !$show_new_arrivals_link ? 'true' : 'false' }})"
                            class="relative inline-flex h-7 w-14 items-center rounded-full transition-colors focus:outline-none {{ $show_new_arrivals_link ? 'bg-orange-500' : 'bg-slate-200' }}"
                        >
                            <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform {{ $show_new_arrivals_link ? 'translate-x-8' : 'translate-x-1' }}"></span>
                        </button>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Arrivals Spotlight</span>
                    </div>
                </div>
            </div>

            <!-- Support Intelligence -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Customer Support Intelligence</p>
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Support Email</label>
                        <div class="mt-2 relative">
                            <input type="email" wire:model="support_email" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner pl-10">
                            <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-[10px]"></i>
                        </div>
                    </div>
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Support Phone</label>
                        <div class="mt-2 relative">
                            <input type="text" wire:model="support_phone" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner pl-10">
                            <i class="fas fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-[10px]"></i>
                        </div>
                    </div>
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">WhatsApp Bridge</label>
                        <div class="mt-2 relative">
                            <input type="text" wire:model="support_whatsapp" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner pl-10">
                            <i class="fab fa-whatsapp absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-[10px]"></i>
                        </div>
                    </div>
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Service Hours</label>
                        <div class="mt-2 relative">
                            <input type="text" wire:model="support_hours" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner pl-10">
                            <i class="fas fa-clock absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-[10px]"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- High-Fidelity Preview Hub -->
        <div class="space-y-6">
            <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-8">Navigation Preview</p>
                
                <div class="space-y-8">
                    <!-- Header Matrix -->
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-6 shadow-inner">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Header Matrix</p>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                            @foreach([
                                $nav_products_label,
                                $nav_categories_label,
                                $show_deals_link ? $nav_deals_label : null,
                                $nav_reviews_label,
                                $nav_track_label,
                                $nav_help_label
                            ] as $label)
                                @if($label)
                                    <span class="text-[10px] font-black text-slate-900 uppercase tracking-widest">{{ $label }}</span>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Contact Identity -->
                    <div class="space-y-4">
                        <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest">Contact Identity</p>
                        <div class="space-y-3">
                            @foreach([
                                ['envelope', $support_email],
                                ['phone', $support_phone],
                                ['whatsapp', $support_whatsapp, 'fab'],
                                ['clock', $support_hours]
                            ] as $item)
                                @if($item[1])
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-orange-50 text-orange-600">
                                            <i class="{{ $item[2] ?? 'fas' }} fa-{{ $item[0] }} text-[10px]"></i>
                                        </div>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">{{ $item[1] }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
