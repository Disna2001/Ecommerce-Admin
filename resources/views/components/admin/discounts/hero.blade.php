<div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.32em] text-slate-400 dark:text-slate-500">Promotion Engine</p>
        <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white">Discount management</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">Keep campaign rules, coupon codes, and checkout incentives simple enough for operators to trust.</p>
    </div>
    <div class="flex flex-wrap gap-3">
        <button wire:click="openCouponBuilder" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700">
            <i class="fas fa-ticket"></i>
            New coupon
        </button>
        <button wire:click="openFlashSaleBuilder" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-3 text-sm font-semibold text-amber-700 transition hover:bg-amber-100 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-200">
            <i class="fas fa-bolt"></i>
            Flash sale
        </button>
        <button wire:click="openModal" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
            <i class="fas fa-plus"></i>
            Custom rule
        </button>
    </div>
</div>
