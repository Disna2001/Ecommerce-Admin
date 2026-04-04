<div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
    <div class="grid gap-4 xl:grid-cols-6">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search stock or notes..." class="rounded-2xl border-slate-200 text-sm shadow-none xl:col-span-2">
        <select wire:model.live="directionFilter" class="rounded-2xl border-slate-200 text-sm shadow-none">
            <option value="">All directions</option>
            <option value="in">In</option>
            <option value="out">Out</option>
        </select>
        <select wire:model.live="contextFilter" class="rounded-2xl border-slate-200 text-sm shadow-none">
            <option value="">All contexts</option>
            @foreach($contexts as $context)
                <option value="{{ $context }}">{{ $context }}</option>
            @endforeach
        </select>
        <input type="date" wire:model.live="dateFrom" class="rounded-2xl border-slate-200 text-sm shadow-none">
        <input type="date" wire:model.live="dateTo" class="rounded-2xl border-slate-200 text-sm shadow-none">
    </div>

    <div class="mt-4 flex flex-wrap gap-3">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">Checkout: <span class="font-semibold text-slate-900">{{ $stats['checkout'] }}</span></div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">POS: <span class="font-semibold text-slate-900">{{ $stats['pos'] }}</span></div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">Restored: <span class="font-semibold text-slate-900">{{ $stats['restored'] }}</span></div>
        <button wire:click="clearFilters" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Clear Filters</button>
    </div>
</div>
