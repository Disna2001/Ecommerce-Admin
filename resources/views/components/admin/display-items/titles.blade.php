<x-admin.ui.panel title="Section Titles" description="Keep homepage merchandising labels short and consistent.">
    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-indigo-200 bg-indigo-50/80 p-4 dark:border-indigo-400/20 dark:bg-indigo-400/10"><label class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-300">Featured</label><input type="text" wire:model="featuredSectionTitle" class="mt-3 w-full rounded-2xl border-indigo-200 text-sm shadow-none dark:border-indigo-400/20 dark:bg-slate-950 dark:text-white"><textarea wire:model="featuredSectionSubtitle" rows="3" class="mt-3 w-full rounded-2xl border-indigo-200 text-sm shadow-none dark:border-indigo-400/20 dark:bg-slate-950 dark:text-white" placeholder="Short supporting copy"></textarea></div>
        <div class="rounded-3xl border border-fuchsia-200 bg-fuchsia-50/80 p-4 dark:border-fuchsia-400/20 dark:bg-fuchsia-400/10"><label class="text-xs font-semibold uppercase tracking-[0.2em] text-fuchsia-600 dark:text-fuchsia-300">New Arrivals</label><input type="text" wire:model="newArrivalsSectionTitle" class="mt-3 w-full rounded-2xl border-fuchsia-200 text-sm shadow-none dark:border-fuchsia-400/20 dark:bg-slate-950 dark:text-white"><textarea wire:model="newArrivalsSectionSubtitle" rows="3" class="mt-3 w-full rounded-2xl border-fuchsia-200 text-sm shadow-none dark:border-fuchsia-400/20 dark:bg-slate-950 dark:text-white" placeholder="Short supporting copy"></textarea></div>
        <div class="rounded-3xl border border-orange-200 bg-orange-50/80 p-4 dark:border-orange-400/20 dark:bg-orange-400/10"><label class="text-xs font-semibold uppercase tracking-[0.2em] text-orange-600 dark:text-orange-300">Deals</label><input type="text" wire:model="dealsSectionTitle" class="mt-3 w-full rounded-2xl border-orange-200 text-sm shadow-none dark:border-orange-400/20 dark:bg-slate-950 dark:text-white"><textarea wire:model="dealsSectionSubtitle" rows="3" class="mt-3 w-full rounded-2xl border-orange-200 text-sm shadow-none dark:border-orange-400/20 dark:bg-slate-950 dark:text-white" placeholder="Short supporting copy"></textarea></div>
    </div>
</x-admin.ui.panel>
<x-admin.ui.panel title="Rail Display Settings" description="Control how product rails look on the storefront and whether customers see stock signals.">
    <div class="grid gap-4 lg:grid-cols-2 xl:grid-cols-4">
        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Rail layout</label>
            <select wire:model="railLayout" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                <option value="immersive">Immersive</option>
                <option value="compact">Compact</option>
                <option value="editorial">Editorial</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Items per rail</label>
            <input type="number" min="4" max="12" wire:model="productsPerRail" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
        </div>
        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
            <input type="checkbox" wire:model="showRailQuantity" class="rounded border-slate-300 text-slate-900">
            Show quantity on cards
        </label>
        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
            <input type="checkbox" wire:model="showRailStockStatus" class="rounded border-slate-300 text-slate-900">
            Show stock status badge
        </label>
    </div>
</x-admin.ui.panel>
