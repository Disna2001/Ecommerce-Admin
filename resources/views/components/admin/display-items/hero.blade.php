<div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.32em] text-slate-400 dark:text-slate-500">Homepage Merchandising</p>
        <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white">Display items</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">Choose which products appear in Featured, New Arrivals, and Deals without scrolling through one oversized view.</p>
    </div>
    <button wire:click="save" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-orange-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-600/20 transition hover:bg-orange-700">
        <span wire:loading.remove wire:target="save"><i class="fas fa-save"></i> Save selections</span>
        <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin"></i> Saving</span>
    </button>
</div>
