<x-admin.ui.panel title="Navigation and Support Identity" description="Compactly control the public navigation labels and support/contact details customers see around the storefront.">
    <div class="grid gap-6 xl:grid-cols-[1fr_1fr]">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
            <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Navigation labels</h4>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Products label</label>
                    <input type="text" wire:model="nav_products_label" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Categories label</label>
                    <input type="text" wire:model="nav_categories_label" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Deals label</label>
                    <input type="text" wire:model="nav_deals_label" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Reviews label</label>
                    <input type="text" wire:model="nav_reviews_label" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Track label</label>
                    <input type="text" wire:model="nav_track_label" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Help label</label>
                    <input type="text" wire:model="nav_help_label" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                </div>
            </div>
            <div class="mt-5 grid gap-3">
                <label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                    <input type="checkbox" wire:model="show_deals_link" class="rounded border-slate-300 text-slate-900">
                    Show deals link in the header
                </label>
                <label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                    <input type="checkbox" wire:model="show_new_arrivals_link" class="rounded border-slate-300 text-slate-900">
                    Keep new-arrivals collection active in the storefront flow
                </label>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
                <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Customer support details</h4>
                <div class="mt-4 grid gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Support email</label>
                        <input type="email" wire:model="support_email" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        @error('support_email') <span class="mt-2 block text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Support phone</label>
                        <input type="text" wire:model="support_phone" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">WhatsApp</label>
                        <input type="text" wire:model="support_whatsapp" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Support hours</label>
                        <input type="text" wire:model="support_hours" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-500">Header preview</p>
                <div class="mt-4 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex flex-wrap items-center gap-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                        <span>{{ $nav_products_label }}</span>
                        <span>{{ $nav_categories_label }}</span>
                        @if($show_deals_link)<span>{{ $nav_deals_label }}</span>@endif
                        <span>{{ $nav_reviews_label }}</span>
                        <span>{{ $nav_track_label }}</span>
                        <span>{{ $nav_help_label }}</span>
                    </div>
                    <div class="mt-5 space-y-2 text-sm text-slate-500 dark:text-slate-400">
                        @if($support_email)<div>{{ $support_email }}</div>@endif
                        @if($support_phone)<div>{{ $support_phone }}</div>@endif
                        @if($support_whatsapp)<div>WhatsApp: {{ $support_whatsapp }}</div>@endif
                        <div>{{ $support_hours }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.ui.panel>
