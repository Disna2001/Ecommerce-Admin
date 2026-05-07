<div class="space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 shadow-inner">
            <i class="fas fa-layer-group text-lg"></i>
        </div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Content Engine</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Narrative Flow & Trust signals</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Utility Intelligence -->
        <div class="lg:col-span-2 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Utility Intelligence</p>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                <div class="group">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Badge Intel</label>
                    <input type="text" wire:model="utility_badge_text" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                </div>
                <div class="group">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Signal Left</label>
                    <input type="text" wire:model="utility_left_text" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                </div>
                <div class="group">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Signal Center</label>
                    <input type="text" wire:model="utility_center_text" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                </div>
                <div class="group">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Search Prompt</label>
                    <input type="text" wire:model="home_search_placeholder" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                </div>
            </div>
        </div>

        <!-- Trust Matrix -->
        <div class="lg:col-span-2 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Trust Matrix (Feature Highlights)</p>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach(['one', 'two', 'three', 'four'] as $idx)
                    <div class="group relative">
                        <input type="text" wire:model="feature_{{ $idx }}_text" class="w-full rounded-xl border-slate-200 bg-slate-50 pl-10 pr-4 py-3 text-xs font-bold text-slate-900 shadow-inner placeholder:text-slate-300" placeholder="Feature {{ $idx }}">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300">
                            <i class="fas fa-shield-check text-[10px]"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Social Proof -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm group">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6 group-focus-within:text-emerald-500 transition-colors">Social Proof Block</p>
            <div class="space-y-4">
                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Section Headline</label>
                    <input type="text" wire:model="reviews_section_title" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Contextual Subtitle</label>
                    <textarea wire:model="reviews_section_subtitle" rows="2" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner"></textarea>
                </div>
            </div>
        </div>

        <!-- Conversion Finale -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm group">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6 group-focus-within:text-emerald-500 transition-colors">Conversion Finale (Final CTA)</p>
            <div class="space-y-4">
                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Campaign Closer</label>
                    <input type="text" wire:model="final_cta_title" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Button Label</label>
                        <input type="text" wire:model="final_cta_button_text" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Target URL</label>
                        <input type="text" wire:model="final_cta_button_link" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
