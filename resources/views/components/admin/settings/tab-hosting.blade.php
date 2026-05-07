@props([])
<div class="space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 shadow-inner"><i class="fas fa-server text-lg"></i></div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Core Infrastructure</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Environment & Global Identity</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="space-y-6 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <p class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Environment Deployment</p>
            
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Canonical App URL</label>
                    <input type="url" wire:model="app_public_url" placeholder="https://yourdomain.com" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-sky-500 focus:ring-0 transition-all">
                </div>

                <label class="group flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3 transition-all hover:bg-white hover:border-sky-500">
                    <input type="checkbox" wire:model="force_https" class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-0">
                    <span class="text-xs font-bold text-slate-600 group-hover:text-slate-900">Enforce SSL Protocols (HTTPS)</span>
                </label>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">System Timezone</label>
                        <input type="text" wire:model="app_timezone" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-sky-500 focus:ring-0 transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Global Locale</label>
                        <input type="text" wire:model="app_locale" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-sky-500 focus:ring-0 transition-all">
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <p class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Commerce & Finance</p>
            
            <div class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Currency Code</label>
                        <input type="text" wire:model="currency_code" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-0 transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Currency Symbol</label>
                        <input type="text" wire:model="currency_symbol" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-0 transition-all">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Asset CDN Integration</label>
                    <input type="url" wire:model="asset_cdn_url" placeholder="https://cdn.yourdomain.com" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-sky-500 focus:ring-0 transition-all">
                </div>

                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Commercial Tax ID</label>
                    <input type="text" wire:model="company_tax_id" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-sky-500 focus:ring-0 transition-all">
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6 rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
            <p class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Storefront Identity & Support</p>
            
            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Public Support Email</label>
                        <input type="email" wire:model="support_email" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-sky-500 focus:ring-0 transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Support Helpline</label>
                        <input type="text" wire:model="support_phone" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-sky-500 focus:ring-0 transition-all">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Registered Corporate Address</label>
                    <textarea wire:model="company_address" rows="4" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-6 py-4 text-sm font-bold shadow-inner focus:bg-white focus:border-sky-500 focus:ring-0 transition-all resize-none"></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-amber-50 border border-amber-100 p-4 flex gap-3">
        <i class="fas fa-info-circle text-amber-500 mt-0.5"></i>
        <p class="text-xs font-bold text-amber-700 leading-relaxed">Infrastructure changes require a system-wide propagation. Ensure these values match your server environment before deploying to production.</p>
    </div>
</div>
