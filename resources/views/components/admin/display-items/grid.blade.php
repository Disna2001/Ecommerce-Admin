<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @forelse($stocks as $stock)
        <div class="group relative rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1 overflow-hidden">
            <!-- Publishing Toggle -->
            <div class="absolute right-4 top-4 z-20">
                 <button 
                    wire:click="toggleStorefront({{ $stock->id }})"
                    class="h-8 w-8 rounded-xl flex items-center justify-center transition-all {{ $stock->storefront_enabled ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'bg-slate-100 text-slate-400' }}"
                >
                    <i class="fas fa-eye text-[10px]"></i>
                </button>
            </div>

            <div class="flex flex-col gap-5">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 rounded-2xl bg-slate-50 border border-slate-100 overflow-hidden flex-shrink-0">
                        @php $img = collect($stock->images)->first(); @endphp
                        @if($img)
                            <img src="{{ asset('storage/' . ($img['path'] ?? '')) }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-slate-200"><i class="fas fa-box text-lg"></i></div>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs font-black text-slate-900 tracking-tight truncate">{{ $stock->name }}</h4>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ $stock->sku }}</p>
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-[10px] font-black text-slate-900">Rs {{ number_format($stock->selling_price, 0) }}</span>
                            <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $stock->category?->name ?? 'Uncategorized' }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 pt-4 border-t border-slate-50">
                    @foreach([
                        'featured' => ['fa-star', 'Featured'],
                        'new' => ['fa-clock', 'New'],
                        'deal' => ['fa-tag', 'Deal']
                    ] as $type => [$icon, $label])
                        @php 
                            $prop = $type === 'new' ? 'newArrivalsIds' : ($type === 'deal' ? 'dealIds' : 'featuredIds');
                            $isActive = in_array($stock->id, $this->$prop);
                            $toggleMethod = 'toggle' . ucfirst($type === 'new' ? 'newArrival' : $type);
                        @endphp
                        <button 
                            wire:click="{{ $toggleMethod }}({{ $stock->id }})"
                            class="flex flex-col items-center gap-1.5 rounded-xl py-2.5 transition-all {{ $isActive ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-slate-900' }}"
                        >
                            <i class="fas {{ $icon }} text-[9px]"></i>
                            <span class="text-[8px] font-black uppercase tracking-tighter">{{ $label }}</span>
                        </button>
                    @endforeach
                </div>

                @if($stock->storefront_enabled)
                    <div class="flex items-center justify-between gap-3 pt-2">
                        <div class="flex flex-col">
                            <span class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Push Quantity</span>
                            <div class="flex items-center gap-2 mt-1">
                                <button wire:click="adjustStorefrontQuantity({{ $stock->id }}, -1)" class="h-6 w-6 rounded-md bg-slate-100 flex items-center justify-center text-[10px] font-black">-</button>
                                <span class="text-[10px] font-black text-slate-900 w-4 text-center">{{ $stock->storefront_quantity }}</span>
                                <button wire:click="adjustStorefrontQuantity({{ $stock->id }}, 1)" class="h-6 w-6 rounded-md bg-slate-100 flex items-center justify-center text-[10px] font-black">+</button>
                            </div>
                        </div>
                        <div class="text-right">
                             <span class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Warehouse Pulse</span>
                             <p class="mt-1 text-[10px] font-black {{ $stock->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $stock->quantity }} Available</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="col-span-full py-20 text-center">
            <i class="fas fa-search text-4xl text-slate-100 mb-4"></i>
            <p class="text-sm font-black text-slate-900 uppercase tracking-tight">No products identified in registry</p>
            <p class="mt-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Broaden your search or adjust your inventory filters.</p>
        </div>
    @endforelse
</div>

<div class="mt-8">
    {{ $stocks->links() }}
</div>
