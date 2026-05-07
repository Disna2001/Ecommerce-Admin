<div class="space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50 text-violet-600 shadow-inner"><i class="fas fa-shield-halved text-lg"></i></div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Permission Architecture</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Administrative Governance & Scope</p>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['Dashboard', 'view dashboard', 'See overview, sales metrics, and quick actions.', 'fa-chart-pie'],
            ['Orders', 'view orders', 'Open order list, payment review, and fulfillment.', 'fa-cart-shopping'],
            ['Inventory', 'view inventory', 'Manage stock, categories, brands, and item setup.', 'fa-box-archive'],
            ['Settings', 'view settings', 'Access system, communications, WhatsApp, and AI config.', 'fa-sliders']
        ] as [$title, $permission, $description, $icon])
            <div class="group relative rounded-[2rem] border border-slate-200 bg-white p-6 transition-all hover:border-violet-400 hover:shadow-xl hover:shadow-violet-50">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-400 group-hover:bg-violet-500 group-hover:text-white transition-colors"><i class="fas {{ $icon }} text-xs"></i></div>
                <p class="mt-4 text-sm font-black text-slate-900">{{ $title }}</p>
                <p class="mt-1 text-[10px] font-bold font-mono text-violet-500 uppercase tracking-widest">{{ $permission }}</p>
                <p class="mt-4 text-xs leading-relaxed text-slate-400">{{ $description }}</p>
            </div>
        @endforeach
    </div>

    <div class="rounded-[2.5rem] border border-slate-200 bg-slate-900 p-8 shadow-2xl text-white relative overflow-hidden">
        <div class="absolute right-0 top-0 -mr-8 -mt-8 h-32 w-32 rounded-full bg-violet-500/10"></div>
        <div class="flex items-center gap-6">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 text-white shadow-inner"><i class="fas fa-fingerprint text-2xl"></i></div>
            <div>
                <p class="text-xs font-black uppercase tracking-[0.2em] text-white/40">Active Governance Protocol</p>
                <p class="mt-2 text-lg font-bold leading-relaxed">System is currently enforcing <span class="text-violet-400">{{ $permissionCount }} unique protocols</span>. Access is granted only via strict role-to-permission mapping.</p>
            </div>
        </div>
    </div>
</div>
