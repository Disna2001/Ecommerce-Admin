<div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.32em] text-slate-400 dark:text-slate-500">Storefront Operations</p>
        <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white">Site management control center</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">Keep branding, homepage content, payment visibility, and storefront identity aligned from one guided workspace.</p>
    </div>
    <button wire:click="saveAll" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/15 transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">
        <span wire:loading.remove wire:target="saveAll,logo_image,favicon_image,hero_image"><i class="fas fa-save"></i> Save storefront settings</span>
        <span wire:loading wire:target="saveAll"><i class="fas fa-spinner fa-spin"></i> Saving</span>
        <span wire:loading wire:target="logo_image,favicon_image,hero_image"><i class="fas fa-cloud-upload-alt fa-bounce"></i> Uploading files</span>
    </button>
</div>
