@props(['returnAction' => 'approve'])

<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
    <div class="w-full max-w-lg rounded-xl border border-slate-200 bg-white p-6 shadow-xl space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Process Return Request</h3>
            <button wire:click="closeReturnModal" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
        </div>

        <div class="space-y-3 text-xs">
            @foreach([
                ['approve', 'Approve Return', 'Customer can ship item back for processing.', 'fa-circle-check', 'emerald'], 
                ['reject', 'Reject Return', 'Decline return request without refund.', 'fa-circle-xmark', 'rose'], 
                ['refund', 'Approve & Refund', 'Restore inventory stock and mark order refunded.', 'fa-money-bill-wave', 'indigo']
            ] as [$value, $title, $description, $icon, $tone])
                <label class="flex cursor-pointer items-start gap-3 rounded-lg border p-3.5 transition {{ $returnAction === $value ? 'border-slate-900 bg-slate-50' : 'border-slate-200 bg-white hover:bg-slate-50/50' }}">
                    <input type="radio" wire:model.live="returnAction" value="{{ $value }}" class="sr-only">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-{{ $tone }}-100 text-{{ $tone }}-700 shrink-0"><i class="fas {{ $icon }} text-xs"></i></div>
                    <div>
                        <p class="font-bold text-slate-900">{{ $title }}</p>
                        <p class="mt-0.5 text-slate-500">{{ $description }}</p>
                    </div>
                </label>
            @endforeach
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 text-xs">
            <button wire:click="closeReturnModal" class="rounded-lg border border-slate-200 bg-white px-4 py-2 font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
            <button wire:click="handleReturn" class="rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white hover:bg-slate-800 shadow-xs">Confirm Action</button>
        </div>
    </div>
</div>
