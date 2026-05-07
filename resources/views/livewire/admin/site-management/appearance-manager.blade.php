<div class="space-y-6">
    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm overflow-hidden relative">
        <div class="absolute right-0 top-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-slate-50 opacity-50"></div>
        <div class="relative z-10 flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Storefront Architect</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900">Appearance & Brand</h1>
                <p class="mt-2 max-w-xl text-sm font-medium leading-relaxed text-slate-500">Sculpt your brand identity and design a high-conversion shopping experience for your customers.</p>
            </div>
            <div class="flex items-center gap-3">
                 <button wire:click="saveAll" class="group relative flex items-center gap-3 rounded-2xl bg-slate-900 px-8 py-4 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-2xl shadow-slate-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fas fa-save text-[10px] opacity-50 group-hover:opacity-100 transition-opacity"></i>
                    Save Storefront Config
                </button>
            </div>
        </div>
    </div>

    @if($saved)
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 animate-in fade-in slide-in-from-top-2">
            <i class="fas fa-check-circle mr-2"></i> Storefront parameters have been successfully deployed.
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[300px_minmax(0,1fr)]">
        <!-- Vertical Blueprint Navigation -->
        <div class="space-y-6">
            <div class="rounded-[2.5rem] border border-slate-200 bg-white p-6 shadow-sm">
                <nav class="flex flex-col gap-1.5">
                    <p class="mb-4 px-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Blueprint Sections</p>
                    
                    @foreach([
                        'branding' => ['Visual Identity', 'fa-palette', 'indigo', $tabStats['branding']],
                        'hero' => ['Storefront Hero', 'fa-mountain-sun', 'sky', $tabStats['hero']],
                        'homepage' => ['Content Engine', 'fa-layer-group', 'emerald', $tabStats['homepage']],
                        'categories' => ['Product Taxonomy', 'fa-tags', 'amber', $tabStats['categories']],
                        'detail' => ['Listing Design', 'fa-file-lines', 'violet', $tabStats['detail']],
                        'payment' => ['Checkout Protocol', 'fa-credit-card', 'rose', $tabStats['payment']],
                        'integrations' => ['Social Intelligence', 'fa-share-nodes', 'slate', $tabStats['integrations']],
                        'navigation' => ['Navigation & Footer', 'fa-compass', 'orange', $tabStats['navigation']],
                    ] as $tab => [$label, $icon, $color, $stat])
                        <button type="button" wire:click="$set('activeTab', '{{ $tab }}')" class="group flex flex-col gap-1 rounded-2xl px-4 py-4 transition-all {{ $activeTab === $tab ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-xl {{ $activeTab === $tab ? 'bg-white/20' : 'bg-slate-100 text-slate-400 group-hover:bg-white group-hover:text-'.$color.'-500' }} transition-colors"><i class="fas {{ $icon }} text-xs"></i></div>
                                    <span class="text-sm font-bold">{{ $label }}</span>
                                </div>
                                <i class="fas fa-chevron-right text-[10px] opacity-20"></i>
                            </div>
                            <div class="ml-13 flex items-center gap-2 pl-13">
                                <div class="h-1 w-1 rounded-full {{ $activeTab === $tab ? 'bg-white/40' : 'bg-slate-300' }}"></div>
                                <span class="text-[9px] font-black uppercase tracking-widest opacity-40">{{ $stat }}</span>
                            </div>
                        </button>
                    @endforeach
                </nav>
            </div>

            <!-- Real-time Preview Link -->
            <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 text-center shadow-sm">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-50 text-slate-200 mb-4">
                    <i class="fas fa-eye"></i>
                </div>
                <h4 class="text-sm font-black text-slate-900">Live Preview</h4>
                <p class="mt-1 text-[10px] font-medium text-slate-400 leading-relaxed uppercase tracking-widest">Observe your changes live on the storefront</p>
                <a href="/" target="_blank" class="mt-6 inline-flex items-center gap-2 text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:text-indigo-700">
                    Open Storefront <i class="fas fa-external-link-alt text-[8px]"></i>
                </a>
            </div>
        </div>

        <!-- Designer Workspace -->
        <div class="min-w-0">
            <div class="rounded-[2.5rem] border border-slate-200 bg-white p-2 shadow-sm">
                <div class="rounded-[2rem] bg-slate-50/50 p-8 min-h-[700px]">
                    @if($activeTab === 'branding')
                        @include('components.admin.appearance.tab-branding')
                        @include('components.admin.appearance.tab-colors')
                    @elseif($activeTab === 'hero')
                        @include('components.admin.appearance.tab-hero')
                        @include('components.admin.appearance.tab-topbar')
                    @elseif($activeTab === 'homepage')
                        @include('components.admin.appearance.tab-homepage')
                        @include('components.admin.appearance.tab-sections')
                    @elseif($activeTab === 'categories')
                        @include('components.admin.appearance.tab-categories')
                    @elseif($activeTab === 'detail')
                        @include('components.admin.appearance.tab-detail')
                    @elseif($activeTab === 'payment')
                        @include('components.admin.appearance.tab-payment')
                    @elseif($activeTab === 'integrations')
                        @include('components.admin.appearance.tab-integrations')
                    @elseif($activeTab === 'navigation')
                        @include('components.admin.appearance.tab-navigation')
                        @include('components.admin.appearance.tab-footer')
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div wire:loading class="fixed bottom-8 right-8 z-[60]">
        <div class="flex items-center gap-3 rounded-2xl bg-slate-900 px-5 py-3 text-white shadow-2xl shadow-slate-400">
            <div class="h-4 w-4 animate-spin rounded-full border-2 border-white/20 border-t-white"></div>
            <span class="text-[10px] font-black uppercase tracking-widest">Deploying Visuals...</span>
        </div>
    </div>
</div>
