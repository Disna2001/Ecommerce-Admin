<div class="fixed inset-0 z-[95] flex items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm">
    <div class="w-full max-w-lg overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Order Control</p>
                    <h3 class="mt-2 text-xl font-semibold text-slate-900">Update Status</h3>
                </div>
                <button wire:click="closeStatusModal" class="rounded-full border border-slate-200 bg-slate-50 p-3 text-slate-500 transition hover:bg-slate-100"><i class="fas fa-xmark"></i></button>
            </div>
        </div>
        <div class="space-y-4 p-6">
            <div>
                <label class="block text-sm font-medium text-slate-700">New status</label>
                <select wire:model="newStatus" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                    @foreach(\App\Models\Order::STATUSES as $key => $status)
                        <option value="{{ $key }}">{{ $status['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Operator note</label>
                <textarea wire:model="statusNote" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none" placeholder="Add context for the timeline and customer notice if needed."></textarea>
            </div>
        </div>
        <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
            <button wire:click="closeStatusModal" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Cancel</button>
            <button wire:click="updateStatus" class="rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Save status</button>
        </div>
    </div>
</div>
