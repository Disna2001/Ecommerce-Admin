<x-admin.ui.panel padding="p-6">
    <x-slot:header>
        <div class="mb-6">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Hosting, URL, and business identity</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Control the public URL, HTTPS behavior, locale, and customer-facing business details used by emails, PDFs, invoices, and storefront help areas.</p>
        </div>
    </x-slot:header>

    <div class="grid gap-4 lg:grid-cols-2">
        <div class="lg:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Public App URL</label><input type="url" wire:model="app_public_url" placeholder="https://yourdomain.com" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 lg:col-span-2"><input type="checkbox" wire:model="force_https" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Force HTTPS for generated URLs and links</label>
        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">App Timezone</label><input type="text" wire:model="app_timezone" placeholder="Asia/Colombo" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">App Locale</label><input type="text" wire:model="app_locale" placeholder="en" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Currency Code</label><input type="text" wire:model="currency_code" placeholder="LKR" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Currency Symbol</label><input type="text" wire:model="currency_symbol" placeholder="Rs" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Support Email</label><input type="email" wire:model="support_email" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Support Phone</label><input type="text" wire:model="support_phone" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div class="lg:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Company Address</label><textarea wire:model="company_address" rows="3" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></textarea></div>
        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Company Tax ID</label><input type="text" wire:model="company_tax_id" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Asset CDN Base URL</label><input type="url" wire:model="asset_cdn_url" placeholder="https://cdn.yourdomain.com" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
    </div>

    <div class="mt-6 rounded-2xl border border-dashed border-slate-200 px-4 py-4 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
        Save these values before running `php artisan system:prepare-hosting` on the server. Public URLs, invoices, help pages, and support widgets will use these settings automatically.
    </div>
</x-admin.ui.panel>
