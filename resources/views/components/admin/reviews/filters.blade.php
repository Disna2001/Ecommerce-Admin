<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-1 items-center gap-3">
            <div class="relative flex-1">
                <input type="text" wire:model.live="search" placeholder="Search by reviewer, product, or content..." class="w-full rounded-2xl border-slate-200 bg-slate-50 pl-12 pr-4 py-3 text-sm font-bold text-slate-900 shadow-inner focus:border-slate-900 focus:ring-0">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300">
                    <i class="fas fa-search text-xs"></i>
                </div>
            </div>
            <select wire:model.live="filterRating" class="rounded-2xl border-slate-200 bg-slate-50 px-5 py-3 text-sm font-bold text-slate-900 shadow-inner">
                <option value="">Any Score</option>
                @foreach([5,4,3,2,1] as $r)
                    <option value="{{ $r }}">{{ $r }} Stars</option>
                @endforeach
            </select>
        </div>
        
        <div class="flex items-center gap-2 rounded-2xl bg-slate-50 p-1.5 shadow-inner">
            @foreach([
                '' => 'All Proof',
                'approved' => 'Published',
                'pending' => 'Queue',
                'flagged' => 'Alerts'
            ] as $val => $label)
                <button 
                    wire:click="$set('filterStatus', '{{ $val }}')"
                    class="rounded-xl px-4 py-2 text-[10px] font-black uppercase tracking-widest transition-all {{ $filterStatus === $val ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400 hover:text-slate-600' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>
</div>
