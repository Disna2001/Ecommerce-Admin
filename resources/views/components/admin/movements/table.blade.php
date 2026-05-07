<div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-xl shadow-slate-200/40">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Timestamp</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Resource</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Mutation</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Context</th>
                    <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Operations</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($movements as $m)
                    <tr class="group transition-colors hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <p class="text-xs font-black text-slate-900">{{ $m->created_at->format('M d, H:i') }}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $m->created_at->diffForHumans() }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <p class="text-xs font-black text-slate-900">{{ $m->stock->name ?? 'Deleted Resource' }}</p>
                                <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-tighter">{{ $m->stock->sku ?? 'N/A' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg {{ $m->direction === 'in' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600 shadow-inner' }}">
                                    <i class="fas {{ $m->direction === 'in' ? 'fa-arrow-down-long' : 'fa-arrow-up-long' }} text-[10px]"></i>
                                </div>
                                <div class="space-y-0.5">
                                    <p class="text-sm font-black {{ $m->direction === 'in' ? 'text-emerald-700' : 'text-rose-700' }}">{{ $m->direction === 'in' ? '+' : '-' }}{{ $m->quantity }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Post-Balance: {{ $m->post_quantity }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-lg bg-slate-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-slate-600 border border-slate-100 shadow-sm">
                                {{ str_replace('_', ' ', $m->context) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="openDetailModal({{ $m->id }})" class="h-8 w-8 rounded-lg text-slate-400 transition hover:bg-slate-900 hover:text-white shadow-sm">
                                <i class="fas fa-eye text-xs"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 text-slate-200">
                                    <i class="fas fa-box-open text-2xl"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-500">No inventory mutations recorded in this segment</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $movements->links() }}
</div>
