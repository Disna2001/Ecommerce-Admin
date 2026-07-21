<div x-data="{ show: true }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
    <div class="relative w-full max-w-lg rounded-xl border border-slate-200 bg-white p-6 shadow-xl space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Ship Order & Add Tracking</h3>
            <button type="button" @click="$wire.closeTrackingModal()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
        </div>

        <form wire:submit="saveTracking" class="space-y-4 text-xs">
            <div class="space-y-1">
                <label class="block font-bold text-slate-700">Tracking Number</label>
                <input type="text" wire:model="trackingNumber" placeholder="Enter tracking number..." class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                @error('trackingNumber') <span class="text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Courier / Carrier</label>
                    <input type="text" wire:model="courier" placeholder="DHL, FedEx, etc." class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                </div>
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Tracking URL (Optional)</label>
                    <input type="url" wire:model="trackingUrl" placeholder="https://..." class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                </div>
            </div>

            <div class="p-3 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-700 font-medium">
                The customer will automatically receive an email notification with these tracking details.
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" @click="$wire.closeTrackingModal()" class="rounded-lg border border-slate-200 bg-white px-4 py-2 font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 font-semibold text-white hover:bg-indigo-700 shadow-xs">Save & Ship Order</button>
            </div>
        </form>
    </div>
</div>
