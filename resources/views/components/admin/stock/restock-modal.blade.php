@props([
    'restockProductName' => '',
    'restockCurrentQuantity' => 0,
    'restockReorderLevel' => 0,
    'restockQuantity' => 0,
    'restockUnitCost' => 0
])

<div 
    x-data="{ show: @entangle('isRestockOpen') }" 
    x-show="show" 
    class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md overflow-y-auto"
    x-cloak
>
    <div 
        x-show="show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        class="relative w-full max-w-xl rounded-2xl sm:rounded-[3rem] border border-slate-200 bg-white shadow-2xl overflow-hidden"
    >
        <form wire:submit.prevent="processRestock" class="flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 bg-slate-50/50 px-5 py-4 sm:px-8 sm:py-6">
                <div class="flex items-center gap-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-xl">
                        <i class="fas fa-plus-circle text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 leading-none mb-1">Inventory: Refill</p>
                        <h3 class="text-xl font-black tracking-tight text-slate-900">Restock Protocol</h3>
                    </div>
                </div>
                <button type="button" @click="show = false" class="flex h-8 w-8 items-center justify-center rounded-xl bg-white text-slate-400 shadow-sm transition-all hover:bg-rose-500 hover:text-white">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="px-5 py-6 sm:px-8 sm:py-10 space-y-6 sm:space-y-8">
                <div class="rounded-2xl bg-slate-50 p-6 border border-slate-100">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-3">Asset Identity</p>
                    <h4 class="text-sm font-black text-slate-900 tracking-tight leading-tight uppercase">{{ $restockProductName }}</h4>
                    <div class="mt-4 flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-slate-400">
                        <span>Current Reserve</span>
                        <span class="{{ $restockCurrentQuantity <= $restockReorderLevel ? 'text-rose-500' : 'text-slate-900' }}">{{ $restockCurrentQuantity }} Units</span>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Restock Volume (Units)</label>
                        <input type="number" wire:model="restockQuantity" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-4 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                        @error('restockQuantity') <p class="text-[9px] font-black text-rose-500 uppercase px-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Acquisition Unit Cost (Current)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-black text-slate-300">Rs.</span>
                            <input type="number" step="0.01" wire:model="restockUnitCost" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 pl-12 pr-4 py-4 text-sm font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                        </div>
                        @error('restockUnitCost') <p class="text-[9px] font-black text-rose-500 uppercase px-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block px-1 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Movement Notes</label>
                        <textarea wire:model="restockNotes" rows="3" placeholder="Explain the origin of this stock movement..." class="w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-4 text-[11px] font-medium text-slate-600 shadow-inner focus:bg-white focus:ring-0 resize-none leading-relaxed"></textarea>
                    </div>
                </div>

                <!-- Strategic Insight -->
                <div class="rounded-2xl border border-indigo-100 bg-indigo-50/50 p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                            <i class="fas fa-calculator text-[10px]"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest leading-none">Investment Total</p>
                            <p class="mt-2 text-xl font-black text-indigo-600 tracking-tighter">Rs {{ number_format((float)$restockQuantity * (float)$restockUnitCost, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="border-t border-slate-100 bg-slate-50/50 px-5 py-4 sm:px-8 sm:py-6">
                <button type="submit" class="flex w-full items-center justify-center gap-3 rounded-2xl bg-slate-900 px-8 py-4 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-2xl shadow-slate-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fas fa-arrow-down text-[10px] opacity-50"></i>
                    Finalize Intake
                </button>
            </div>
        </form>
    </div>
</div>
