<div x-data="{ show: true }" x-show="show" class="fixed inset-0 z-[110] flex items-center justify-center p-4">
    <div @click="$wire.closeTrackingModal()" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
    <div class="relative w-full max-w-xl rounded-[2.5rem] bg-white p-10 shadow-2xl border border-slate-200">
        <div class="flex items-center gap-4 mb-10">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-xl shadow-indigo-500/20">
                <i class="fas fa-truck-ramp-box text-sm"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Logistics Protocol</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Initialize Order Shipment</p>
            </div>
        </div>

        <form wire:submit="saveTracking" class="space-y-6">
            <div class="space-y-4">
                <div class="flex items-center gap-3 px-1">
                    <i class="fas fa-barcode text-[10px] text-slate-400"></i>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Tracking Reference #</label>
                </div>
                <input type="text" wire:model="trackingNumber" placeholder="Enter carrier reference number..." class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-4 text-xs font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                @error('trackingNumber') <p class="text-[9px] font-bold text-rose-500 uppercase tracking-widest px-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div class="space-y-4">
                    <div class="flex items-center gap-3 px-1">
                        <i class="fas fa-building-columns text-[10px] text-slate-400"></i>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Courier Carrier</label>
                    </div>
                    <input type="text" wire:model="courier" placeholder="e.g. DHL, Fedex..." class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-4 text-xs font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                </div>
                <div class="space-y-4">
                    <div class="flex items-center gap-3 px-1">
                        <i class="fas fa-link text-[10px] text-slate-400"></i>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Traceability URL</label>
                    </div>
                    <input type="text" wire:model="trackingUrl" placeholder="https://..." class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-4 text-xs font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                </div>
            </div>

            <div class="p-6 rounded-2xl bg-indigo-50 border border-indigo-100 mb-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-envelope-circle-check text-indigo-600 text-xs"></i>
                    <p class="text-[9px] font-bold text-indigo-600 uppercase tracking-widest leading-relaxed">System will automatically notify the customer of this shipment protocol via Email/WhatsApp.</p>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="button" @click="$wire.closeTrackingModal()" class="flex-1 h-14 rounded-2xl bg-slate-50 text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] hover:bg-rose-50 hover:text-rose-600 transition-all">Abort</button>
                <button type="submit" class="flex-[2] h-14 rounded-2xl bg-indigo-600 text-white text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-indigo-500/20 hover:scale-105 transition-transform">Authorize Shipment</button>
            </div>
        </form>
    </div>
</div>
