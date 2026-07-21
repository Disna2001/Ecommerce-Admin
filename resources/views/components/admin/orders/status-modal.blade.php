<div x-data="{ show: true }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
    <div class="relative w-full max-w-md rounded-xl border border-slate-200 bg-white p-6 shadow-xl space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Update Order Status</h3>
            <button type="button" @click="$wire.closeStatusModal()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
        </div>

        <form wire:submit="updateStatus" class="space-y-4 text-xs">
            <div class="space-y-1">
                <label class="block font-bold text-slate-700">Status</label>
                <select wire:model="newStatus" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                    @foreach(\App\Models\Order::STATUSES as $val => $label)
                        <option value="{{ $val }}">{{ $label['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="block font-bold text-slate-700">Status Note (Optional)</label>
                <textarea wire:model="statusNote" placeholder="Enter reason or details for this status change..." rows="3" class="w-full rounded-lg border-slate-200 px-3 py-2 font-medium text-slate-900 focus:ring-0"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" @click="$wire.closeStatusModal()" class="rounded-lg border border-slate-200 bg-white px-4 py-2 font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white hover:bg-slate-800 shadow-xs">Save Status</button>
            </div>
        </form>
    </div>
</div>
