<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Total Movements</p>
            <p class="mt-3 text-3xl font-black text-slate-900">{{ $stats['total'] }}</p>
            <p class="mt-2 text-sm text-slate-500">Every centralized stock-in and stock-out operation.</p>
        </div>
        <div class="rounded-[1.75rem] border border-rose-200 bg-rose-50 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-rose-600">Stock Out</p>
            <p class="mt-3 text-3xl font-black text-rose-700">{{ $stats['out'] }}</p>
            <p class="mt-2 text-sm text-rose-700">Checkout and POS deductions.</p>
        </div>
        <div class="rounded-[1.75rem] border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-600">Stock In</p>
            <p class="mt-3 text-3xl font-black text-emerald-700">{{ $stats['in'] }}</p>
            <p class="mt-2 text-sm text-emerald-700">Restorations from cancellations and refunds.</p>
        </div>
    </div>

    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-4 xl:grid-cols-6">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search stock or notes..." class="rounded-2xl border-slate-200 text-sm shadow-none xl:col-span-2">
            <select wire:model.live="directionFilter" class="rounded-2xl border-slate-200 text-sm shadow-none">
                <option value="">All directions</option>
                <option value="in">In</option>
                <option value="out">Out</option>
            </select>
            <select wire:model.live="contextFilter" class="rounded-2xl border-slate-200 text-sm shadow-none">
                <option value="">All contexts</option>
                @foreach($contexts as $context)
                    <option value="{{ $context }}">{{ $context }}</option>
                @endforeach
            </select>
            <input type="date" wire:model.live="dateFrom" class="rounded-2xl border-slate-200 text-sm shadow-none">
            <input type="date" wire:model.live="dateTo" class="rounded-2xl border-slate-200 text-sm shadow-none">
        </div>

        <div class="mt-4 flex flex-wrap gap-3">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">Checkout: <span class="font-semibold text-slate-900">{{ $stats['checkout'] }}</span></div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">POS: <span class="font-semibold text-slate-900">{{ $stats['pos'] }}</span></div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">Restored: <span class="font-semibold text-slate-900">{{ $stats['restored'] }}</span></div>
            <button wire:click="clearFilters" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Clear Filters</button>
        </div>
    </div>

    <div class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">Stock</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">Direction</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">Quantity</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">Before / After</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">Context</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">Actor</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">View</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($movements as $movement)
                        <tr>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-900">{{ $movement->stock?->name ?? 'Deleted stock' }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $movement->stock?->sku ?? 'n/a' }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $movement->direction === 'out' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ strtoupper($movement->direction) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-slate-600">{{ $movement->quantity }}</td>
                            <td class="px-4 py-4 text-slate-600">{{ $movement->before_quantity }} → {{ $movement->after_quantity }}</td>
                            <td class="px-4 py-4 text-slate-600">{{ $movement->context }}</td>
                            <td class="px-4 py-4 text-slate-600">{{ $movement->user?->name ?? 'System / Unknown' }}</td>
                            <td class="px-4 py-4">
                                <button wire:click="openDetailModal({{ $movement->id }})" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-white">
                                    <i class="fas fa-eye"></i> Details
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center text-slate-500">No stock movement records match the current filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $movements->links() }}</div>
    </div>

    @if($showDetailModal && $selectedMovement)
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
                                <p><span class="font-semibold text-slate-900">Before / After:</span> {{ $selectedMovement->before_quantity }} → {{ $selectedMovement->after_quantity }}</p>
                                <p><span class="font-semibold text-slate-900">Context:</span> {{ $selectedMovement->context }}</p>
                                <p><span class="font-semibold text-slate-900">Actor:</span> {{ $selectedMovement->user?->name ?? 'System / Unknown' }}</p>
                                <p><span class="font-semibold text-slate-900">When:</span> {{ optional($selectedMovement->created_at)->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Reference</p>
                            <p class="mt-3 text-sm text-slate-600">{{ class_basename($selectedMovement->reference_type ?? 'General') }} @if($selectedMovement->reference_id)#{{ $selectedMovement->reference_id }}@endif</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Notes</p>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $selectedMovement->notes ?: 'No extra notes were recorded.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
