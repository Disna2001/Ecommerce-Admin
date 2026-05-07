<div class="space-y-8">
    <!-- Curator Control Deck -->
    <div class="rounded-[3rem] border border-slate-200 bg-white p-10 shadow-2xl overflow-hidden relative">
        <div class="absolute right-0 top-0 -mr-24 -mt-24 h-80 w-80 rounded-full bg-slate-50 opacity-60"></div>
        <div class="relative z-10 flex flex-col gap-10 xl:flex-row xl:items-center xl:justify-between">
            <div class="max-w-2xl">
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-xl">
                        <i class="fas fa-layer-group text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-400 leading-none">Commerce Intelligence</p>
                        <h1 class="mt-1 text-4xl font-black tracking-tight text-slate-900">Storefront Curator</h1>
                    </div>
                </div>
                <p class="text-sm font-bold leading-relaxed text-slate-500">Orchestrate your marketplace identity. Curate high-impact collections, synchronize warehouse stock, and manage visual merchandising logic from a unified workspace.</p>
            </div>
            <div class="flex items-center gap-4">
                 <button wire:click="save" class="group relative flex h-14 items-center gap-4 rounded-[2rem] bg-slate-900 px-10 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-2xl shadow-slate-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fas fa-cloud-upload-alt text-indigo-400 group-hover:rotate-12 transition-transform"></i>
                    Deploy Changes
                </button>
            </div>
        </div>
    </div>

    <!-- Telemetry & Navigation -->
    <div class="grid gap-6 lg:grid-cols-12">
        <div class="lg:col-span-3 space-y-6">
            <!-- Sidebar Navigation -->
            <div class="rounded-[2.5rem] border border-slate-200 bg-white p-4 shadow-sm">
                <div class="space-y-1">
                    @foreach([
                        'curator' => ['fa-shapes', 'Registry Curator'],
                        'blueprint' => ['fa-map', 'Layout Blueprint'],
                        'push' => ['fa-bolt', 'Stock Push Protocol']
                    ] as $tab => [$icon, $label])
                        <button 
                            wire:click="$set('activeTab', '{{ $tab }}')"
                            class="flex w-full items-center gap-4 rounded-2xl px-5 py-4 text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === $tab ? 'bg-slate-900 text-white shadow-xl' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-900' }}"
                        >
                            <i class="fas {{ $icon }} {{ $activeTab === $tab ? 'text-indigo-400' : 'opacity-40' }}"></i>
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Summary Telemetry -->
             <div class="rounded-[2.5rem] border border-slate-200 bg-white p-6 shadow-sm space-y-6">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 px-2">Storefront Pulse</p>
                <div class="space-y-4">
                    @foreach([
                        ['Published', $displayStats['published'], 'emerald'],
                        ['Featured', $displayStats['featured'], 'indigo'],
                        ['New Arrivals', $displayStats['new'], 'sky'],
                        ['Best Deals', $displayStats['deals'], 'amber']
                    ] as [$label, $val, $color])
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-5 py-4">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $label }}</span>
                            <span class="text-xs font-black text-{{ $color }}-600">{{ $val }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="lg:col-span-9">
            @if($activeTab === 'curator')
                <div class="space-y-6 animate-in fade-in slide-in-from-right-8 duration-500">
                    @include('components.admin.display-items.filters')
                    @include('components.admin.display-items.grid')
                </div>
            @elseif($activeTab === 'blueprint')
                <div class="space-y-6 animate-in fade-in slide-in-from-right-8 duration-500">
                    @include('components.admin.display-items.titles')
                    
                    <div class="rounded-[2.5rem] border border-slate-200 bg-white p-10 shadow-sm grid gap-10 md:grid-cols-2">
                        <div class="space-y-8">
                            <div>
                                <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Display Architecture</h3>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Visual Merchandise Logic</p>
                            </div>
                            
                            <div class="space-y-6">
                                <div class="group">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Rail Layout Style</label>
                                    <select wire:model.live="railLayout" class="mt-2 w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-4 text-xs font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                        <option value="immersive">Immersive High-Fidelity</option>
                                        <option value="compact">Compact Administrative</option>
                                        <option value="minimal">Minimal Performance</option>
                                    </select>
                                </div>
                                <div class="group">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Products Per Section</label>
                                    <input type="number" min="4" max="12" wire:model.live="productsPerRail" class="mt-2 w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-4 text-xs font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-8">
                            <div>
                                <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Data Visibility</h3>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Consumer Telemetry</p>
                            </div>

                            <div class="space-y-4">
                                @foreach([
                                    'showRailQuantity' => ['Inventory Levels', 'Display real-time stock counts to customers'],
                                    'showRailStockStatus' => ['Availability Badges', 'Show "In Stock" or "Low Stock" indicators']
                                ] as $prop => [$label, $desc])
                                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-6 border border-slate-100">
                                        <div>
                                            <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest leading-none">{{ $label }}</p>
                                            <p class="mt-1 text-[9px] font-bold text-slate-400 uppercase tracking-tight">{{ $desc }}</p>
                                        </div>
                                        <button 
                                            wire:click="$set('{{ $prop }}', {{ !$$prop ? 'true' : 'false' }})"
                                            class="relative inline-flex h-7 w-14 items-center rounded-full transition-colors {{ $$prop ? 'bg-indigo-600' : 'bg-slate-300' }}"
                                        >
                                            <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform {{ $$prop ? 'translate-x-8' : 'translate-x-1' }}"></span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($activeTab === 'push')
                <div class="animate-in fade-in slide-in-from-right-8 duration-500">
                    <div class="rounded-[3rem] border border-slate-900 bg-slate-900 p-12 text-white shadow-2xl relative overflow-hidden group">
                        <div class="absolute right-0 top-0 -mr-24 -mt-24 h-96 w-96 rounded-full bg-white/5 group-hover:scale-110 transition-transform duration-1000"></div>
                        <div class="relative z-10 grid gap-12 lg:grid-cols-2 lg:items-center">
                            <div>
                                <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-indigo-500 shadow-2xl shadow-indigo-500/20 mb-8">
                                    <i class="fas fa-bolt text-2xl"></i>
                                </div>
                                <h2 class="text-3xl font-black tracking-tight mb-4">Stock Push Protocol</h2>
                                <p class="text-white/60 font-medium leading-relaxed max-w-md">Automatically synchronize offline inventory with the storefront registry. Define your payload and targeting logic to rapidly refresh your marketplace visibility.</p>
                                
                                <div class="mt-10 flex items-center gap-8">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-white/30 mb-2">Payload Limit</span>
                                        <div class="flex items-center gap-4">
                                            <button @click="$wire.pushQuantity = Math.max(1, $wire.pushQuantity - 1)" class="h-10 w-10 rounded-xl bg-white/10 hover:bg-white/20 transition-colors flex items-center justify-center">-</button>
                                            <span class="text-2xl font-black w-12 text-center">{{ $pushQuantity }}</span>
                                            <button @click="$wire.pushQuantity = Math.min(50, $wire.pushQuantity + 1)" class="h-10 w-10 rounded-xl bg-white/10 hover:bg-white/20 transition-colors flex items-center justify-center">+</button>
                                        </div>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-white/30 mb-2">Target Registry</span>
                                        <select wire:model="pushTarget" class="bg-white/10 border-none rounded-xl text-xs font-black uppercase tracking-widest px-4 py-3 focus:ring-0">
                                            <option value="low_visibility">Low Visibility Items</option>
                                            <option value="newest">Newest Arrivals</option>
                                            <option value="random">Random Distribution</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-center lg:justify-end">
                                <button wire:click="pushStockToStorefront" wire:loading.attr="disabled" class="group relative flex flex-col items-center justify-center gap-6 rounded-[4rem] border-4 border-white/10 bg-white/5 p-16 transition-all hover:bg-white/10 hover:border-indigo-500/50">
                                    <div class="relative">
                                        <div class="h-24 w-24 rounded-full border-4 border-indigo-500/20 border-t-indigo-500 animate-spin" wire:loading wire:target="pushStockToStorefront"></div>
                                        <div class="absolute inset-0 flex items-center justify-center" wire:loading.remove wire:target="pushStockToStorefront">
                                            <i class="fas fa-power-off text-5xl text-white group-hover:text-indigo-400 transition-colors"></i>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[10px] font-black uppercase tracking-[0.5em] text-white">Initialize Push</p>
                                        <p class="mt-2 text-[9px] font-bold text-white/40 uppercase tracking-widest">Execute Warehouse Sync</p>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div wire:loading class="fixed bottom-10 right-10 z-[60]">
        <div class="flex items-center gap-4 rounded-3xl bg-slate-900 px-8 py-4 text-white shadow-2xl">
            <div class="h-5 w-5 animate-spin rounded-full border-2 border-white/20 border-t-white"></div>
            <span class="text-[11px] font-black uppercase tracking-[0.2em]">Synchronizing Registry...</span>
        </div>
    </div>
</div>
