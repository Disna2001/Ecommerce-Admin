<div class="mt-12 pt-12 border-t border-slate-100 space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-inner">
            <i class="fas fa-anchor text-lg"></i>
        </div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Closing Narrative</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Footer identity & Social connectivity</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1fr_400px]">
        <div class="space-y-8">
            <!-- Global Identity -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Global Identity</p>
                <div class="space-y-6">
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Footer Tagline</label>
                        <input type="text" wire:model="footer_tagline" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Copyright Signal</label>
                        <input type="text" wire:model="footer_copyright" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                </div>
            </div>

            <!-- Social Connectivity -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Social Connectivity matrix</p>
                <div class="grid gap-6 md:grid-cols-2">
                    @foreach([
                        ['facebook', 'Facebook URL', 'facebook_url', 'indigo'],
                        ['twitter', 'Twitter / X URL', 'twitter_url', 'sky'],
                        ['instagram', 'Instagram URL', 'instagram_url', 'rose'],
                        ['pinterest', 'Pinterest URL', 'pinterest_url', 'red']
                    ] as [$icon, $label, $key, $color])
                        <div class="group">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-{{ $color }}-500 transition-colors">{{ $label }}</label>
                            <div class="mt-2 relative">
                                <input type="text" wire:model="{{ $key }}" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner pl-10 focus:border-{{ $color }}-500 focus:ring-0">
                                <i class="fab fa-{{ $icon }} absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-[10px]"></i>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- High-Fidelity Preview Hub -->
        <div class="space-y-6">
            <div class="rounded-[2.5rem] border border-slate-900 bg-slate-900 p-8 shadow-2xl relative overflow-hidden group">
                <div class="absolute right-0 top-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-white/5 group-hover:scale-110 transition-transform"></div>
                
                <p class="relative z-10 text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-8">Footer Preview</p>
                
                <div class="relative z-10 space-y-8">
                    <div>
                        <p class="text-sm font-black text-white tracking-tight leading-relaxed">{{ $footer_tagline ?: 'Your closing narrative goes here...' }}</p>
                    </div>

                    <div class="flex gap-4">
                        @foreach(['facebook_url', 'twitter_url', 'instagram_url', 'pinterest_url'] as $idx => $key)
                            @if($this->$key)
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white/60">
                                    <i class="fab fa-{{ explode('_', $key)[0] }} text-xs"></i>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="pt-8 border-t border-white/10">
                        <p class="text-[9px] font-black text-white/30 uppercase tracking-widest">{{ $footer_copyright ?: '© 2026 Your Store. All Rights Reserved.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
