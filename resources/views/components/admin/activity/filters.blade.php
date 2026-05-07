<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex-1 relative group">
            <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-slate-900 transition-colors"></i>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by action, description, or subject..." class="w-full rounded-2xl border-slate-100 bg-slate-50 pl-14 py-4 text-sm font-bold shadow-inner focus:bg-white focus:border-slate-900 focus:ring-0 transition-all">
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <select wire:model.live="userFilter" class="rounded-xl border-slate-100 bg-slate-50 px-4 py-2 text-xs font-bold shadow-none focus:ring-0 transition-all focus:bg-white">
                <option value="">All Agents</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>

            <div class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 p-1">
                <input type="date" wire:model.live="dateFrom" class="border-0 bg-transparent py-1 px-3 text-[10px] font-black uppercase tracking-wider focus:ring-0 w-32">
                <span class="text-[10px] text-slate-300"><i class="fas fa-arrow-right"></i></span>
                <input type="date" wire:model.live="dateTo" class="border-0 bg-transparent py-1 px-3 text-[10px] font-black uppercase tracking-wider focus:ring-0 w-32">
            </div>

            <button type="button" wire:click="clearFilters" class="h-10 w-10 flex items-center justify-center rounded-xl bg-white text-rose-400 border border-slate-100 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Purge Filters">
                <i class="fas fa-rotate-left text-xs"></i>
            </button>
        </div>
    </div>
</div>
