<x-admin.ui.panel title="Moderation Flow" description="Filter the queue, then apply the smallest moderation action needed.">
    <div class="flex flex-wrap items-center gap-3">
        <div class="relative min-w-[220px] flex-1">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search reviewer, product, or review text" class="w-full rounded-2xl border-slate-200 pl-10 pr-10 text-sm shadow-none focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
            <i class="fas fa-search absolute left-4 top-3 text-xs text-slate-400"></i>
            <div wire:loading wire:target="search" class="absolute right-4 top-3 text-xs text-slate-400">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
        </div>
        <select wire:model.live="filterStatus" class="rounded-2xl border-slate-200 text-sm shadow-none focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
            <option value="">All statuses</option>
            <option value="approved">Approved</option>
            <option value="pending">Pending</option>
            <option value="flagged">Flagged</option>
        </select>
        <select wire:model.live="filterRating" class="rounded-2xl border-slate-200 text-sm shadow-none focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
            <option value="">All ratings</option>
            @for($r = 5; $r >= 1; $r--)
                <option value="{{ $r }}">{{ str_repeat('*', $r) }} {{ $r }} star{{ $r > 1 ? 's' : '' }}</option>
            @endfor
        </select>
        <select wire:model.live="perPage" class="rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
            <option value="15">15 / page</option>
            <option value="25">25 / page</option>
            <option value="50">50 / page</option>
        </select>
        @if($search || $filterStatus || $filterRating)
            <button wire:click="$set('search', ''); $set('filterStatus', ''); $set('filterRating', '')" class="rounded-2xl border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-400/30 dark:text-rose-300 dark:hover:bg-rose-400/10">
                Clear filters
            </button>
        @endif
    </div>

    @if(count($selected) > 0)
        <div class="mt-4 flex flex-wrap items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ count($selected) }} reviews selected</p>
            <button wire:click="bulkApprove" wire:loading.attr="disabled" class="rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                <span wire:loading.remove wire:target="bulkApprove">Approve all</span>
                <span wire:loading wire:target="bulkApprove"><i class="fas fa-spinner fa-spin"></i></span>
            </button>
            <button wire:click="bulkReject" class="rounded-2xl bg-slate-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-slate-200 dark:text-slate-900 dark:hover:bg-white">Unpublish all</button>
            <button wire:click="bulkDelete" wire:confirm="Delete all {{ count($selected) }} selected reviews? This cannot be undone." class="rounded-2xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-700">Delete all</button>
        </div>
    @endif
</x-admin.ui.panel>
