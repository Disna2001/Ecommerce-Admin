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
                        <td class="px-4 py-4"><p class="font-semibold text-slate-900">{{ $movement->stock?->name ?? 'Deleted stock' }}</p><p class="mt-1 text-xs text-slate-400">{{ $movement->stock?->sku ?? 'n/a' }}</p></td>
                        <td class="px-4 py-4"><span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $movement->direction === 'out' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">{{ strtoupper($movement->direction) }}</span></td>
                        <td class="px-4 py-4 text-slate-600">{{ $movement->quantity }}</td>
                        <td class="px-4 py-4 text-slate-600">{{ $movement->before_quantity }} -> {{ $movement->after_quantity }}</td>
                        <td class="px-4 py-4 text-slate-600">{{ $movement->context }}</td>
                        <td class="px-4 py-4 text-slate-600">{{ $movement->user?->name ?? 'System / Unknown' }}</td>
                        <td class="px-4 py-4"><button wire:click="openDetailModal({{ $movement->id }})" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-white"><i class="fas fa-eye"></i> Details</button></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-slate-500">No stock movement records match the current filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $movements->links() }}</div>
</div>
