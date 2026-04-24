<div class="fixed inset-0 z-[95] flex items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm">
    <div class="w-full max-w-xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Return Handling</p>
                    <h3 class="mt-2 text-xl font-semibold text-slate-900">Process Return Request</h3>
                </div>
                <button wire:click="closeReturnModal" class="rounded-full border border-slate-200 bg-slate-50 p-3 text-slate-500 transition hover:bg-slate-100"><i class="fas fa-xmark"></i></button>
            </div>
        </div>
        <div class="space-y-3 p-6">
            @foreach([['approve', 'Approve return', 'Customer can move forward with the return.', 'fa-circle-check', 'emerald'], ['reject', 'Reject return', 'Close the request without a return.', 'fa-circle-xmark', 'rose'], ['refund', 'Approve and refund', 'Restore stock and mark payment as refunded.', 'fa-money-bill-wave', 'violet']] as [$value, $title, $description, $icon, $tone])
                <label class="flex cursor-pointer items-start gap-4 rounded-2xl border-2 p-4 transition {{ $returnAction === $value ? ($tone === 'emerald' ? 'border-emerald-300 bg-emerald-50' : ($tone === 'rose' ? 'border-rose-300 bg-rose-50' : 'border-violet-300 bg-violet-50')) : 'border-slate-200 bg-white' }}">
                    <input type="radio" wire:model.live="returnAction" value="{{ $value }}" class="sr-only">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $tone === 'emerald' ? 'bg-emerald-100 text-emerald-600' : ($tone === 'rose' ? 'bg-rose-100 text-rose-600' : 'bg-violet-100 text-violet-600') }}"><i class="fas {{ $icon }}"></i></div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ $title }}</p>
                        <p class="mt-1 text-xs leading-6 text-slate-500">{{ $description }}</p>
                    </div>
                </label>
            @endforeach
        </div>
        <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
            <button wire:click="closeReturnModal" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Cancel</button>
            <button wire:click="handleReturn" class="rounded-full bg-amber-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-amber-600">Confirm action</button>
        </div>
    </div>
</div>
