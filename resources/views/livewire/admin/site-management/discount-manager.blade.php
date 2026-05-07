<div class="space-y-6">
    <!-- Promo Control Deck -->
    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm overflow-hidden relative">
        <div class="absolute right-0 top-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-slate-50 opacity-50"></div>
        <div class="relative z-10 flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Commerce Incentives</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900">Promotion Engine</h1>
                <p class="mt-2 max-w-xl text-sm font-medium leading-relaxed text-slate-500">Architect promotional coupons, automated discounts, and flash sale countdowns to drive high-velocity commerce.</p>
            </div>
            <div class="flex items-center gap-3">
                 <button wire:click="openCouponBuilder" class="group relative flex items-center gap-3 rounded-2xl bg-white border border-slate-200 px-6 py-4 text-[10px] font-black text-slate-900 uppercase tracking-[0.2em] shadow-sm transition-all hover:border-slate-900 active:scale-[0.98]">
                    <i class="fas fa-ticket text-[10px] text-slate-400 group-hover:text-slate-900 transition-colors"></i>
                    Coupon Builder
                </button>
                 <button wire:click="openFlashSaleBuilder" class="group relative flex items-center gap-3 rounded-2xl bg-slate-900 px-8 py-4 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-2xl shadow-slate-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fas fa-bolt text-[10px] opacity-50 group-hover:opacity-100 transition-opacity"></i>
                    Flash Sale Hub
                </button>
            </div>
        </div>
    </div>

    <!-- Strategy Presets Hub -->
    <div class="grid gap-4 md:grid-cols-3">
        @foreach([
            ['welcome', 'Welcome Drop', 'fa-gift', 'sky', '10% New customer logic'],
            ['bundle', 'Volume Bundle', 'fa-box-open', 'emerald', 'Fixed saving on bulk orders'],
            ['weekend', 'Weekend Pulse', 'fa-stopwatch', 'amber', 'Timed conversion booster']
        ] as [$preset, $label, $icon, $color, $sub])
            <button wire:click="applyPreset('{{ $preset }}')" class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:border-slate-900 hover:shadow-xl text-left">
                <div class="flex items-center gap-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-{{ $color }}-50 text-{{ $color }}-600 group-hover:bg-slate-900 group-hover:text-white transition-colors">
                        <i class="fas {{ $icon }} text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-black text-slate-900 uppercase tracking-tighter">{{ $label }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $sub }}</p>
                    </div>
                </div>
            </button>
        @endforeach
    </div>

    <!-- Promotion Intelligence -->
    <div class="grid gap-4 md:grid-cols-4">
        @foreach([
            ['active', 'Active Offers', 'fa-signal', 'emerald'],
            ['coupons', 'Active Coupons', 'fa-ticket', 'indigo'],
            ['auto_apply', 'Auto Discounts', 'fa-magic', 'sky'],
            ['scheduled', 'Expiring Soon', 'fa-hourglass-half', 'rose']
        ] as [$key, $label, $icon, $color])
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm group">
                <div class="flex items-center gap-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-{{ $color }}-50 text-{{ $color }}-600">
                        <i class="fas {{ $icon }} text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 leading-none mb-1">{{ $label }}</p>
                        <span class="text-xl font-black text-slate-900 leading-none">{{ $discountStats[$key] }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Promo Discovery -->
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="relative max-w-lg">
            <input type="text" wire:model.live="search" placeholder="Filter by promo name or coupon code..." class="w-full rounded-2xl border-slate-200 bg-slate-50 pl-12 pr-4 py-3 text-sm font-bold text-slate-900 shadow-inner focus:border-slate-900 focus:ring-0">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300">
                <i class="fas fa-search text-xs"></i>
            </div>
        </div>
    </div>

    <!-- Promo Inventory -->
    <div class="rounded-[2.5rem] border border-slate-200 bg-white p-2 shadow-sm">
        <div class="rounded-[2.25rem] bg-slate-50/50 p-6 min-h-[400px]">
            @include('components.admin.discounts.table')
        </div>
    </div>

    <!-- Strategy Workspace -->
    @include('components.admin.discounts.modal')

    <div wire:loading class="fixed bottom-8 right-8 z-[60]">
        <div class="flex items-center gap-3 rounded-2xl bg-slate-900 px-5 py-3 text-white shadow-2xl shadow-slate-400">
            <div class="h-4 w-4 animate-spin rounded-full border-2 border-white/20 border-t-white"></div>
            <span class="text-[10px] font-black uppercase tracking-widest">Architecting Promo...</span>
        </div>
    </div>
</div>
