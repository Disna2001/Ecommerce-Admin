<div x-data="{ show: true }" x-show="show" class="fixed inset-0 z-[110] flex items-center justify-center p-4">
    <div @click="$wire.closeStatusModal()" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
    <div class="relative w-full max-w-lg rounded-[2.5rem] bg-white p-10 shadow-2xl border border-slate-200">
        <div class="flex items-center gap-4 mb-10">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-xl">
                <i class="fas fa-arrows-rotate text-sm"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Lifecycle Protocol</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Adjust Order Fulfillment Stage</p>
            </div>
        </div>

        <form wire:submit="updateStatus" class="space-y-8">
            <div class="space-y-4">
                <div class="flex items-center gap-3 px-1">
                    <i class="fas fa-flag text-[10px] text-slate-400"></i>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Target Stage</label>
                </div>
                <select wire:model="newStatus" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-4 text-xs font-black text-slate-900 shadow-inner focus:bg-white focus:ring-0">
                    @foreach(\App\Models\Order::STATUSES as $val => $label)
                        <option value="{{ $val }}">{{ $label['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-4">
                <div class="flex items-center gap-3 px-1">
                    <i class="fas fa-note-sticky text-[10px] text-slate-400"></i>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Registry Note (Optional)</label>
                </div>
                <textarea wire:model="statusNote" placeholder="Enter administrative narrative for this transition..." rows="4" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-4 text-xs font-bold text-slate-900 shadow-inner focus:bg-white focus:ring-0"></textarea>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="button" @click="$wire.closeStatusModal()" class="flex-1 h-14 rounded-2xl bg-slate-50 text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] hover:bg-rose-50 hover:text-rose-600 transition-all">Abort</button>
                <button type="submit" class="flex-[2] h-14 rounded-2xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-slate-200 hover:bg-indigo-600 transition-all">Authorize Transition</button>
            </div>
        </form>
    </div>
</div>
