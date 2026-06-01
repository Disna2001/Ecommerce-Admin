<div class="space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50 text-violet-600 shadow-inner">
            <i class="fas fa-file-lines text-lg"></i>
        </div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Listing Architecture</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Detail Page Design & Trust signals</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1fr_400px]">
        <div class="space-y-8">
            <!-- Trust Configuration -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Trust Signal Matrix</p>
                <div class="space-y-4">
                    @foreach([
                        ['detail_trust_one_title', 'detail_trust_one_text', 'Primary Signal'],
                        ['detail_trust_two_title', 'detail_trust_two_text', 'Secondary Signal'],
                        ['detail_trust_three_title', 'detail_trust_three_text', 'Tertiary Signal'],
                    ] as [$titleKey, $textKey, $label])
                        <div class="group rounded-2xl border border-slate-100 bg-slate-50/50 p-4 transition-all focus-within:bg-white focus-within:shadow-md">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-300 group-focus-within:text-violet-500 transition-colors">{{ $label }}</ol>
                            <input type="text" wire:model="{{ $titleKey }}" class="mt-2 w-full rounded-xl border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-900 shadow-sm focus:ring-4 focus:ring-violet-500/10">
                            <textarea wire:model="{{ $textKey }}" rows="2" class="mt-2 w-full rounded-xl border-slate-200 bg-white px-4 py-2 text-[11px] font-bold text-slate-500 shadow-sm focus:ring-4 focus:ring-violet-500/10"></textarea>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Value Messaging -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Value & Stock Intelligence</p>
                <div class="grid gap-6">
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Value Headline</label>
                        <input type="text" wire:model="detail_value_title" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Value Narrative</label>
                        <textarea wire:model="detail_value_text" rows="2" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner"></textarea>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="group">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Value CTA</label>
                            <input type="text" wire:model="detail_value_cta" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                        </div>
                        <div class="group">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">In Stock Protocol</ol>
                            <input type="text" wire:model="detail_in_stock_label" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                        </div>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="group">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Low Stock Template</ol>
                            <input type="text" wire:model="detail_low_stock_template" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                        </div>
                        <div class="group">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Out of Stock Signal</ol>
                            <input type="text" wire:model="detail_out_of_stock_label" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Global Visibility -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Component Surveillance</p>
                <div class="space-y-4">
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Related Section Title</label>
                        <input type="text" wire:model="detail_related_title" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <button 
                                type="button"
                                wire:click="$set('detail_show_reviews', {{ !$detail_show_reviews ? 'true' : 'false' }})"
                                class="relative inline-flex h-7 w-14 items-center rounded-full transition-colors focus:outline-none {{ $detail_show_reviews ? 'bg-violet-500' : 'bg-slate-200' }}"
                            >
                                <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform {{ $detail_show_reviews ? 'translate-x-8' : 'translate-x-1' }}"></span>
                            </button>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Review Tab</span>
                        </div>
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <button 
                                type="button"
                                wire:click="$set('detail_show_related', {{ !$detail_show_related ? 'true' : 'false' }})"
                                class="relative inline-flex h-7 w-14 items-center rounded-full transition-colors focus:outline-none {{ $detail_show_related ? 'bg-violet-500' : 'bg-slate-200' }}"
                            >
                                <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform {{ $detail_show_related ? 'translate-x-8' : 'translate-x-1' }}"></span>
                            </button>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Related Grid</span>
                        </div>
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <button 
                                type="button"
                                wire:click="$set('detail_image_magnifier_enabled', {{ !$detail_image_magnifier_enabled ? 'true' : 'false' }})"
                                class="relative inline-flex h-7 w-14 items-center rounded-full transition-colors focus:outline-none {{ $detail_image_magnifier_enabled ? 'bg-violet-500' : 'bg-slate-200' }}"
                            >
                                <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform {{ $detail_image_magnifier_enabled ? 'translate-x-8' : 'translate-x-1' }}"></span>
                            </button>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Image Magnifier</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- High-Fidelity Preview Hub -->
        <div class="space-y-6">
            <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-8">Blueprint Preview</p>
                
                <div class="space-y-6">
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-6 shadow-inner">
                        <h4 class="text-lg font-black text-slate-900 leading-none">Listing Headline</h4>
                        <div class="mt-4 flex items-center gap-3">
                            <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                            <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">{{ $detail_in_stock_label }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ str_replace('{quantity}', '3', $detail_low_stock_template) }}</span>
                        </div>
                    </div>

                    <div class="grid gap-3">
                        @foreach([[$detail_trust_one_title, $detail_trust_one_text], [$detail_trust_two_title, $detail_trust_two_text], [$detail_trust_three_title, $detail_trust_three_text]] as [$title, $text])
                            <div class="rounded-2xl border border-slate-50 bg-white p-4 shadow-sm">
                                <div class="flex items-center gap-3 mb-2">
                                    <i class="fas fa-shield-check text-[10px] text-violet-500"></i>
                                    <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest">{{ $title ?: 'Signal Title' }}</p>
                                </div>
                                <p class="text-[9px] font-bold text-slate-400 leading-relaxed">{{ $text ?: 'Signal description goes here...' }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-6">
                        <p class="text-[10px] font-black text-emerald-800 uppercase tracking-widest mb-2">{{ $detail_value_title ?: 'Value Proposition' }}</p>
                        <p class="text-[10px] font-bold text-emerald-700/80 leading-relaxed mb-6">{{ $detail_value_text ?: 'Proposing value to the shopper...' }}</p>
                        <div class="inline-flex rounded-xl bg-white px-4 py-2 text-[9px] font-black text-emerald-600 uppercase tracking-widest shadow-sm">{{ $detail_value_cta ?: 'ACTION' }}</div>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-3">{{ $detail_related_title ?: 'Related Grid' }}</p>
                        <div class="flex gap-2">
                            <div class="h-12 w-12 rounded-xl bg-slate-50 border border-slate-100"></div>
                            <div class="h-12 w-12 rounded-xl bg-slate-50 border border-slate-100"></div>
                            <div class="h-12 w-12 rounded-xl bg-slate-50 border border-slate-100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
