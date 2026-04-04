<div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.32em] text-slate-400 dark:text-slate-500">Storefront Messaging</p>
        <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white">Banner management</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">Schedule homepage banners and promo strips in smaller, easier-to-scan widgets.</p>
    </div>
    <div class="flex flex-wrap gap-3">
        <button wire:click="applyPreset('hero_launch')" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">
            <i class="fas fa-panorama"></i>
            Hero preset
        </button>
        <button wire:click="applyPreset('promo_strip')" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-200">
            <i class="fas fa-bolt"></i>
            Promo preset
        </button>
        <button wire:click="openModal" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
            <i class="fas fa-plus"></i>
            Custom banner
        </button>
    </div>
</div>
