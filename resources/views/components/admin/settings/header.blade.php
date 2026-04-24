<div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.32em] text-slate-400 dark:text-slate-500">Operations Stack</p>
        <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white">Communications, billing, AI, and integration controls</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">Run delivery channels, bill output profiles, AI assistant behavior, and operational credentials from one structured admin workspace.</p>
    </div>
    <button wire:click="save" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/10 transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">
        <span wire:loading.remove wire:target="save"><i class="fas fa-save"></i> Save Settings</span>
        <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin"></i> Saving...</span>
    </button>
</div>
