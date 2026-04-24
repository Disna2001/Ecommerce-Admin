<div class="fixed inset-0 z-[95] flex items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm">
    <div class="w-full max-w-xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Dispatch Update</p>
                    <h3 class="mt-2 text-xl font-semibold text-slate-900">Add Tracking</h3>
                </div>
                <button wire:click="closeTrackingModal" class="rounded-full border border-slate-200 bg-slate-50 p-3 text-slate-500 transition hover:bg-slate-100"><i class="fas fa-xmark"></i></button>
            </div>
        </div>
        <div class="grid gap-4 p-6">
            <div>
                <label class="block text-sm font-medium text-slate-700">Tracking number</label>
                <input type="text" wire:model="trackingNumber" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none" placeholder="EX123456789LK">
                @error('trackingNumber')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Courier</label>
                <input type="text" wire:model="courier" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none" placeholder="SL Post, DHL, FedEx">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Tracking URL</label>
                <input type="url" wire:model="trackingUrl" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none" placeholder="https://tracking.example.com/...">
                @error('trackingUrl')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>
            <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm leading-6 text-sky-700">
                Saving tracking automatically moves the order into the shipped stage and notifies the customer.
            </div>
        </div>
        <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
            <button wire:click="closeTrackingModal" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Cancel</button>
            <button wire:click="saveTracking" class="rounded-full bg-sky-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-700">Save tracking</button>
        </div>
    </div>
</div>
