<div class="rounded-[2.5rem] border border-slate-200 bg-white p-10 shadow-sm space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white shadow-lg">
            <i class="fas fa-heading text-xs"></i>
        </div>
        <div>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Collection Blueprints</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Section Titles & Narratives</p>
        </div>
    </div>

    <div class="grid gap-8">
        @foreach([
            ['featured', 'fa-star', 'Featured Collection'],
            ['new_arrivals', 'fa-clock', 'New Arrivals Rail'],
            ['deals', 'fa-tag', 'Promotional Deck']
        ] as [$key, $icon, $label])
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <i class="fas {{ $icon }} text-[10px] text-slate-400"></i>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $label }}</label>
                </div>
                <div class="grid gap-4">
                    <input type="text" wire:model="{{ $key }}SectionTitle" placeholder="Enter Headline..." class="w-full rounded-xl border-slate-100 bg-slate-50 px-4 py-3 text-xs font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                    <input type="text" wire:model="{{ $key }}SectionSubtitle" placeholder="Enter Narrative Subtitle..." class="w-full rounded-xl border-slate-100 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-400 shadow-inner focus:bg-white focus:ring-0">
                </div>
            </div>
        @endforeach
    </div>
</div>
