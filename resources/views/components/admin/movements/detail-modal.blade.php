<div class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
    <div class="max-h-[85vh] w-full max-w-4xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Stock Movement Detail</p>
                <h3 class="mt-2 text-xl font-semibold text-slate-900">{{ $selectedMovement->stock?->name ?? 'Deleted stock' }}</h3>
            </div>
            <button wire:click="closeDetailModal" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-white">Close</button>
        </div>
        <div class="grid max-h-[calc(85vh-88px)] gap-6 overflow-y-auto p-6 lg:grid-cols-2">
            <div class="space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Movement Summary</p>
                    <div class="mt-3 space-y-2 text-sm text-slate-600">
                        <p><span class="font-semibold text-slate-900">Direction:</span> {{ strtoupper($selectedMovement->direction) }}</p>
                        <p><span class="font-semibold text-slate-900">Quantity:</span> {{ $selectedMovement->quantity }}</p>
                        <p><span class="font-semibold text-slate-900">Before / After:</span> {{ $selectedMovement->before_quantity }} -> {{ $selectedMovement->after_quantity }}</p>
                        <p><span class="font-semibold text-slate-900">Context:</span> {{ $selectedMovement->context }}</p>
                        <p><span class="font-semibold text-slate-900">Actor:</span> {{ $selectedMovement->user?->name ?? 'System / Unknown' }}</p>
                        <p><span class="font-semibold text-slate-900">When:</span> {{ optional($selectedMovement->created_at)->format('Y-m-d H:i:s') }}</p>
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Reference</p><p class="mt-3 text-sm text-slate-600">{{ class_basename($selectedMovement->reference_type ?? 'General') }} @if($selectedMovement->reference_id)#{{ $selectedMovement->reference_id }}@endif</p></div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Notes</p><p class="mt-3 text-sm leading-6 text-slate-600">{{ $selectedMovement->notes ?: 'No extra notes were recorded.' }}</p></div>
            </div>
        </div>
    </div>
</div>
