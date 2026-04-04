<x-admin.ui.panel title="Homepage Sections and Promo Rails" description="Control section headings and add a modern promotional strip that sits between the hero and product collections.">
    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
                <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Collection Labels</h4>
                <div class="mt-4 grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Featured section</label>
                        <input type="text" wire:model="featured_section_title" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">New arrivals section</label>
                        <input type="text" wire:model="new_arrivals_section_title" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Deals section</label>
                        <input type="text" wire:model="deals_section_title" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Promo strip</h4>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">This is the modern campaign rail under the hero where you can push bundles, flash deals, or seasonal offers.</p>
                    </div>
                    <label class="inline-flex items-center gap-3 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                        <input type="checkbox" wire:model="promo_strip_enabled" class="rounded border-slate-300 text-slate-900">
                        Show rail
                    </label>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Badge</label>
                        <input type="text" wire:model="promo_strip_badge" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Button text</label>
                        <input type="text" wire:model="promo_strip_button_text" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Promo title</label>
                        <input type="text" wire:model="promo_strip_title" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Promo message</label>
                        <textarea wire:model="promo_strip_text" rows="3" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Button link</label>
                        <input type="text" wire:model="promo_strip_button_link" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Gradient from</label>
                            <div class="mt-2 flex items-center gap-3">
                                <input type="color" wire:model.live="promo_strip_from" class="h-11 w-14 rounded-xl border border-slate-200 bg-white p-1">
                                <input type="text" wire:model.live="promo_strip_from" class="flex-1 rounded-2xl border-slate-200 font-mono text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Gradient to</label>
                            <div class="mt-2 flex items-center gap-3">
                                <input type="color" wire:model.live="promo_strip_to" class="h-11 w-14 rounded-xl border border-slate-200 bg-white p-1">
                                <input type="text" wire:model.live="promo_strip_to" class="flex-1 rounded-2xl border-slate-200 font-mono text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-500">Live section preview</p>
            <div class="mt-4 space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Collections</p>
                    <div class="mt-3 space-y-3 text-sm font-semibold text-slate-800 dark:text-slate-100">
                        <div>{{ $deals_section_title }}</div>
                        <div>{{ $featured_section_title }}</div>
                        <div>{{ $new_arrivals_section_title }}</div>
                    </div>
                </div>
                <div class="rounded-[1.5rem] p-5 text-white" style="background:linear-gradient(135deg, {{ $promo_strip_from }}, {{ $promo_strip_to }})">
                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em]">{{ $promo_strip_badge }}</span>
                    <h5 class="mt-4 text-xl font-bold">{{ $promo_strip_title }}</h5>
                    <p class="mt-2 text-sm text-white/80">{{ $promo_strip_text }}</p>
                    <div class="mt-4 inline-flex rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-900">{{ $promo_strip_button_text }}</div>
                </div>
            </div>
        </div>
    </div>
</x-admin.ui.panel>
