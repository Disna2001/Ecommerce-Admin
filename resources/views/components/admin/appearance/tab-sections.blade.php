<div class="mt-12 pt-12 border-t border-slate-100 space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 shadow-inner">
            <i class="fas fa-puzzle-piece text-lg"></i>
        </div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Campaign Rails</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Promotion & Collection Labels</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1fr_400px]">
        <div class="space-y-8">
            <!-- Collection Labels -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Collection Taxonomy</p>
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Featured</label>
                        <input type="text" wire:model="featured_section_title" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">New Arrivals</label>
                        <input type="text" wire:model="new_arrivals_section_title" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Best Sellers</label>
                        <input type="text" wire:model="deals_section_title" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                </div>
            </div>

            <!-- Promo Strip Configuration -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Promo Rail Protocol</p>
                        <h4 class="text-sm font-black text-slate-900 mt-1">Seasonal Marketing Strip</h4>
                    </div>
                    <button 
                        wire:click="$set('promo_strip_enabled', {{ !$promo_strip_enabled ? 'true' : 'false' }})"
                        class="relative inline-flex h-7 w-14 items-center rounded-full transition-colors focus:outline-none {{ $promo_strip_enabled ? 'bg-indigo-500' : 'bg-slate-200' }}"
                    >
                        <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform {{ $promo_strip_enabled ? 'translate-x-8' : 'translate-x-1' }}"></span>
                    </button>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Strip Badge</label>
                        <input type="text" wire:model="promo_strip_badge" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Button Label</label>
                        <input type="text" wire:model="promo_strip_button_text" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Marketing Headline</label>
                        <input type="text" wire:model="promo_strip_title" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Narrative Body</label>
                        <textarea wire:model="promo_strip_text" rows="2" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner"></textarea>
                    </div>
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Navigation Target</label>
                        <input type="text" wire:model="promo_strip_button_link" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Rail From</label>
                            <div class="mt-2 flex items-center gap-2">
                                <input type="color" wire:model.live="promo_strip_from" class="h-10 w-10 overflow-hidden rounded-lg border-none p-0">
                                <input type="text" wire:model.live="promo_strip_from" class="flex-1 rounded-lg border-slate-100 bg-slate-50 px-3 py-2 font-mono text-[9px] font-black uppercase">
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Rail To</label>
                            <div class="mt-2 flex items-center gap-2">
                                <input type="color" wire:model.live="promo_strip_to" class="h-10 w-10 overflow-hidden rounded-lg border-none p-0">
                                <input type="text" wire:model.live="promo_strip_to" class="flex-1 rounded-lg border-slate-100 bg-slate-50 px-3 py-2 font-mono text-[9px] font-black uppercase">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- High-Fidelity Preview -->
        <div class="space-y-6">
            <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-8">Blueprint Preview</p>
                
                <div class="space-y-6">
                    <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Live Taxonomy</p>
                        <div class="space-y-2">
                            @foreach([$deals_section_title, $featured_section_title, $new_arrivals_section_title] as $label)
                                <div class="flex items-center gap-3">
                                    <div class="h-1.5 w-1.5 rounded-full bg-indigo-500"></div>
                                    <span class="text-xs font-black text-slate-900">{{ $label }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="relative rounded-[2rem] p-6 text-white overflow-hidden shadow-2xl transition-all {{ !$promo_strip_enabled ? 'opacity-20 grayscale' : '' }}" style="background: linear-gradient(135deg, {{ $promo_strip_from }}, {{ $promo_strip_to }})">
                        <div class="absolute right-0 top-0 -mr-8 -mt-8 h-32 w-32 rounded-full bg-white/10"></div>
                        <span class="relative z-10 inline-flex rounded-full bg-white/20 px-3 py-1 text-[9px] font-black uppercase tracking-widest">{{ $promo_strip_badge ?: 'CAMPAIGN' }}</span>
                        <h5 class="relative z-10 mt-4 text-lg font-black tracking-tight leading-none">{{ $promo_strip_title ?: 'Your Headline' }}</h5>
                        <p class="relative z-10 mt-2 text-[10px] font-bold text-white/80 leading-relaxed">{{ $promo_strip_text ?: 'Detailed campaign message goes here...' }}</p>
                        <div class="relative z-10 mt-6 inline-flex rounded-xl bg-white px-5 py-2.5 text-[10px] font-black text-slate-900 uppercase tracking-widest shadow-xl">{{ $promo_strip_button_text ?: 'EXPLORE' }}</div>
                    </div>
                    
                    @if(!$promo_strip_enabled)
                        <p class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest">Rail is currently dormant</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
