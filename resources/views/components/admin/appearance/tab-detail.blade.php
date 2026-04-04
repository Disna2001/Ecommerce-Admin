<x-admin.ui.panel title="Product Detail Page" description="Customize the trust messaging, stock language, and related-products behavior used on the single product page.">
    <div class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
                <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Trust cards</h4>
                <div class="mt-4 grid gap-4">
                    @foreach([
                        ['detail_trust_one_title', 'detail_trust_one_text'],
                        ['detail_trust_two_title', 'detail_trust_two_text'],
                        ['detail_trust_three_title', 'detail_trust_three_text'],
                    ] as [$titleKey, $textKey])
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-950">
                            <input type="text" wire:model="{{ $titleKey }}" class="w-full rounded-2xl border-slate-200 text-sm font-semibold shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            <textarea wire:model="{{ $textKey }}" rows="3" class="mt-3 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></textarea>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
                <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Value and stock messaging</h4>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Value title</label>
                        <input type="text" wire:model="detail_value_title" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Value text</label>
                        <textarea wire:model="detail_value_text" rows="3" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Value CTA</label>
                        <input type="text" wire:model="detail_value_cta" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">In stock label</label>
                        <input type="text" wire:model="detail_in_stock_label" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Low stock template</label>
                        <input type="text" wire:model="detail_low_stock_template" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Out of stock label</label>
                        <input type="text" wire:model="detail_out_of_stock_label" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
                <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Section visibility</h4>
                <div class="mt-4 grid gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Related section title</label>
                        <input type="text" wire:model="detail_related_title" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                        <input type="checkbox" wire:model="detail_show_reviews" class="rounded border-slate-300 text-slate-900">
                        Show reviews tab and review form
                    </label>
                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                        <input type="checkbox" wire:model="detail_show_related" class="rounded border-slate-300 text-slate-900">
                        Show related products section
                    </label>
                </div>
            </div>
        </div>

        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-500">Detail preview</p>
            <div class="mt-4 space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900">
                    <h4 class="text-2xl font-black text-slate-900 dark:text-white">Product title preview</h4>
                    <p class="mt-2 text-sm font-medium text-emerald-600">{{ $detail_in_stock_label }} | {{ str_replace('{quantity}', '3', $detail_low_stock_template) }}</p>
                </div>
                <div class="grid gap-3">
                    @foreach([[$detail_trust_one_title, $detail_trust_one_text], [$detail_trust_two_title, $detail_trust_two_text], [$detail_trust_three_title, $detail_trust_three_text]] as [$title, $text])
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $title }}</p>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $text }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/10">
                    <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">{{ $detail_value_title }}</p>
                    <p class="mt-2 text-sm text-emerald-700/90 dark:text-emerald-200/80">{{ $detail_value_text }}</p>
                    <div class="mt-4 inline-flex rounded-full bg-white px-4 py-2 text-xs font-bold text-emerald-700 dark:bg-slate-900 dark:text-emerald-300">{{ $detail_value_cta }}</div>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $detail_related_title }}</p>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $detail_show_related ? 'Related products are visible.' : 'Related products are hidden.' }}</p>
                </div>
            </div>
        </div>
    </div>
</x-admin.ui.panel>
