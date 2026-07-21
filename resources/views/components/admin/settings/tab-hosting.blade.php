@props([])
<div class="space-y-6">
    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
            <i class="fas fa-server text-sm"></i>
        </div>
        <div>
            <h3 class="text-base font-bold text-slate-900">Core Systems</h3>
            <p class="text-xs text-slate-500">Configure domain, currency, locale, and store contact info.</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2 text-xs font-semibold">
        <!-- Environment & Domain -->
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <h4 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">Environment & Domain</h4>
            
            <div class="space-y-3">
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Canonical App URL</label>
                    <input type="url" wire:model="app_public_url" placeholder="https://yourdomain.com" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                </div>

                <label class="flex cursor-pointer items-center gap-2.5 rounded-lg border border-slate-100 bg-slate-50 p-3 hover:bg-white hover:border-slate-200 transition-colors">
                    <input type="checkbox" wire:model="force_https" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-0">
                    <span class="font-bold text-slate-800">Enforce SSL Protocols (HTTPS)</span>
                </label>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">System Timezone</label>
                        <input type="text" wire:model="app_timezone" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                    </div>
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Global Locale</label>
                        <input type="text" wire:model="app_locale" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                    </div>
                </div>
            </div>
        </div>

        <!-- Currency & Finance -->
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <h4 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">Currency & Finance</h4>
            
            <div class="space-y-3">
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Currency Code</label>
                        <input type="text" wire:model="currency_code" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                    </div>
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Currency Symbol</label>
                        <input type="text" wire:model="currency_symbol" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Asset CDN Integration</label>
                    <input type="url" wire:model="asset_cdn_url" placeholder="https://cdn.yourdomain.com" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Commercial Tax ID</label>
                    <input type="text" wire:model="company_tax_id" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                </div>
            </div>
        </div>

        <!-- Store Contact Info -->
        <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <h4 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">Store Contact Info</h4>
            
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-3">
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Public Support Email</label>
                        <input type="email" wire:model="support_email" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                    </div>
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Support Helpline</label>
                        <input type="text" wire:model="support_phone" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Registered Corporate Address</label>
                    <textarea wire:model="company_address" rows="3" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0 resize-none"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
