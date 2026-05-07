<div class="space-y-6">
    <!-- Campaign Control Deck -->
    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm overflow-hidden relative">
        <div class="absolute right-0 top-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-slate-50 opacity-50"></div>
        <div class="relative z-10 flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Marketing Intelligence</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900">Campaign Banners</h1>
                <p class="mt-2 max-w-xl text-sm font-medium leading-relaxed text-slate-500">Orchestrate high-impact visual campaigns, schedule promotional drops, and manage your storefront's marketing signals.</p>
            </div>
            <div class="flex items-center gap-3">
                 <button wire:click="openModal" class="group relative flex items-center gap-3 rounded-2xl bg-slate-900 px-8 py-4 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-2xl shadow-slate-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fas fa-plus text-[10px] opacity-50 group-hover:opacity-100 transition-opacity"></i>
                    Deploy New Campaign
                </button>
            </div>
        </div>
    </div>

    <!-- Rapid Presets Hub -->
    <div class="grid gap-4 md:grid-cols-3">
        @foreach([
            ['hero_launch', 'Hero Spotlight', 'fa-mountain-sun', 'indigo', 'Main storefront presence'],
            ['promo_strip', 'Promo Rail', 'fa-layer-group', 'emerald', 'Horizontal marketing strip'],
            ['top_notice', 'Global Notice', 'fa-bullhorn', 'amber', 'High-visibility alert']
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

    <!-- Campaign Intelligence -->
    <div class="grid gap-4 md:grid-cols-4">
        @foreach([
            ['total', 'Total Banners', 'fa-folder-tree', 'slate'],
            ['live', 'Live Campaigns', 'fa-signal', 'emerald'],
            ['scheduled', 'Scheduled', 'fa-clock', 'sky'],
            ['hero', 'Primary Heroes', 'fa-star', 'amber']
        ] as [$key, $label, $icon, $color])
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm group">
                <div class="flex items-center gap-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-{{ $color }}-50 text-{{ $color }}-600">
                        <i class="fas {{ $icon }} text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 leading-none mb-1">{{ $label }}</p>
                        <span class="text-xl font-black text-slate-900 leading-none">{{ $bannerStats[$key] }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Campaign Inventory -->
    <div class="rounded-[2.5rem] border border-slate-200 bg-white p-2 shadow-sm">
        <div class="rounded-[2.25rem] bg-slate-50/50 p-6 min-h-[400px]">
            @include('components.admin.banners.table')
        </div>
    </div>

    <!-- Deployment Workspace -->
    @include('components.admin.banners.modal')

    <div wire:loading class="fixed bottom-8 right-8 z-[60]">
        <div class="flex items-center gap-3 rounded-2xl bg-slate-900 px-5 py-3 text-white shadow-2xl shadow-slate-400">
            <div class="h-4 w-4 animate-spin rounded-full border-2 border-white/20 border-t-white"></div>
            <span class="text-[10px] font-black uppercase tracking-widest">Deploying Campaign...</span>
        </div>
    </div>
</div>
