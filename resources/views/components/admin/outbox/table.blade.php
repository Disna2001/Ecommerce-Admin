<div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-xl shadow-slate-200/40">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Timestamp</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Channel</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Recipient</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Status</th>
                    <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Operations</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($notifications as $n)
                    <tr class="group transition-colors hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <p class="text-xs font-black text-slate-900">{{ $n->created_at->format('M d, H:i') }}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $n->created_at->diffForHumans() }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 flex items-center justify-center rounded-lg {{ $n->channel === 'whatsapp' ? 'bg-emerald-50 text-emerald-600' : 'bg-indigo-50 text-indigo-600' }}">
                                    <i class="fab {{ $n->channel === 'whatsapp' ? 'fa-whatsapp' : 'fa-at' }} text-[10px]"></i>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">{{ $n->channel }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <p class="text-xs font-bold text-slate-700">{{ $n->recipient }}</p>
                                <p class="text-[9px] font-medium text-slate-400 truncate max-w-[200px]">{{ $n->subject ?? 'No subject defined' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                             @php
                                $statusTone = match($n->status) {
                                    'sent' => 'emerald',
                                    'queued' => 'amber',
                                    'failed' => 'rose',
                                    'skipped' => 'slate',
                                    default => 'slate'
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-{{ $statusTone }}-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-{{ $statusTone }}-700 border border-{{ $statusTone }}-100 shadow-sm">
                                <span class="h-1.5 w-1.5 rounded-full bg-{{ $statusTone }}-500 {{ $n->status === 'queued' ? 'animate-pulse' : '' }}"></span>
                                {{ $n->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($n->status === 'failed')
                                    <button wire:click="retryOutbox({{ $n->id }})" class="h-8 w-8 rounded-lg text-emerald-400 transition hover:bg-emerald-500 hover:text-white shadow-sm" title="Retry Delivery">
                                        <i class="fas fa-rotate text-xs"></i>
                                    </button>
                                @endif
                                <button wire:click="openDetailModal({{ $n->id }})" class="h-8 w-8 rounded-lg text-slate-400 transition hover:bg-slate-900 hover:text-white shadow-sm">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 text-slate-200">
                                    <i class="fas fa-satellite-dish text-2xl"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-500">No transmission records found in this pipeline</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $notifications->links() }}
</div>
