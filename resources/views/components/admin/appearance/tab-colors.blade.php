<div class="mt-12 pt-12 border-t border-slate-100 space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 shadow-inner">
            <i class="fas fa-swatchbook text-lg"></i>
        </div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Chromatic Palette</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Theme & Accent Orchestration</p>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        @foreach([
            ['primary_color', 'Primary Brand', 'Main accents, buttons, and active states'],
            ['secondary_color', 'Secondary Theme', 'Supporting tones and structural accents'],
            ['accent_color', 'Signal Accent', 'Highlights and micro-interactions'],
            ['text_color', 'Core Typography', 'Primary body text and headings'],
            ['bg_color', 'Surface Base', 'Main application background tone'],
            ['nav_bg_color', 'Navigation Deck', 'Global header and menu surface']
        ] as [$field, $label, $desc])
            <div class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:border-slate-900">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-slate-900 transition-colors">{{ $label }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">{{ $desc }}</p>
                    </div>
                    <div class="h-8 w-8 rounded-full border border-slate-100 shadow-inner" style="background-color: {{ $this->$field }}"></div>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="relative h-12 w-12 flex-shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                        <input type="color" wire:model.live="{{ $field }}" class="absolute -inset-2 h-[200%] w-[200%] cursor-pointer border-none bg-transparent">
                    </div>
                    <div class="relative flex-1">
                        <input type="text" wire:model.live="{{ $field }}" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 font-mono text-xs font-black text-slate-900 transition-all focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 uppercase">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300">
                            <i class="fas fa-hashtag text-[10px]"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
