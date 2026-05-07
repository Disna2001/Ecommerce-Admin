<div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center px-4 py-8">
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm" wire:click="closeDetailModal"></div>
        
        <div class="relative z-10 w-full max-w-3xl overflow-hidden rounded-[2.5rem] border border-slate-200 bg-white shadow-2xl">
            <div class="border-b border-slate-100 bg-white px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-[1rem] bg-slate-900 text-white shadow-lg shadow-slate-200"><i class="fas fa-tower-broadcast text-lg"></i></div>
                        <div>
                            <h3 class="text-xl font-black tracking-tight text-slate-900">Transmission Dossier</h3>
                            <p class="text-xs font-medium text-slate-400 uppercase tracking-widest">{{ $selectedOutbox->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeDetailModal" class="h-10 w-10 flex items-center justify-center rounded-full border border-slate-100 bg-slate-50 text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-500">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
            </div>

            <div class="max-h-[75vh] overflow-y-auto px-8 py-8 space-y-8">
                <div class="grid gap-6 md:grid-cols-3">
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pipeline Channel</p>
                        <div class="flex items-center gap-2">
                            <i class="fab {{ $selectedOutbox->channel === 'whatsapp' ? 'fa-whatsapp text-emerald-500' : 'fa-at text-indigo-500' }}"></i>
                            <p class="text-sm font-bold text-slate-900 capitalize">{{ $selectedOutbox->channel }}</p>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Delivery Status</p>
                        <p class="text-sm font-bold text-{{ $selectedOutbox->status === 'sent' ? 'emerald' : ($selectedOutbox->status === 'failed' ? 'rose' : 'amber') }}-600 uppercase">{{ $selectedOutbox->status }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Recipient Identity</p>
                        <p class="text-sm font-bold text-slate-900 truncate">{{ $selectedOutbox->recipient }}</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Subject Narrative</p>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 text-sm font-bold text-slate-700 leading-relaxed italic">
                        "{{ $selectedOutbox->subject ?: 'No formal subject defined for this signal.' }}"
                    </div>
                </div>

                @if($selectedOutbox->failure_message)
                    <div class="space-y-2">
                        <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest">Critical Error Signature</p>
                        <div class="rounded-2xl border border-rose-100 bg-rose-50 p-4 text-xs font-bold text-rose-700 leading-relaxed">
                            {{ $selectedOutbox->failure_message }}
                        </div>
                    </div>
                @endif

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Attempt Registry</p>
                        <p class="text-sm font-bold text-slate-900">{{ $selectedOutbox->attempt_count }} Attempts Recorded</p>
                    </div>
                    <div class="space-y-1 text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Last Activity</p>
                        <p class="text-sm font-bold text-slate-900">{{ $selectedOutbox->last_attempt_at?->format('H:i:s') ?? 'N/A' }}</p>
                    </div>
                </div>

                @if(!empty($selectedOutbox->payload))
                    <div class="space-y-2">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Signal Payload (JSON)</p>
                        <div class="rounded-2xl bg-slate-900 p-6 shadow-inner overflow-x-auto">
                            <pre class="text-[11px] font-mono text-emerald-400 leading-relaxed">{{ json_encode($selectedOutbox->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50/50 px-8 py-6">
                <div class="flex gap-3">
                    @if($selectedOutbox->status === 'failed')
                        <button type="button" wire:click="retryOutbox({{ $selectedOutbox->id }})" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-6 py-3 text-[10px] font-black text-white uppercase tracking-widest shadow-xl shadow-emerald-100 transition-all hover:bg-emerald-700 hover:scale-[1.02]">
                            <i class="fas fa-rotate"></i> Re-Trigger Signal
                        </button>
                    @endif
                    
                    @php
                        $relatedUrl = match($selectedOutbox->related_type) {
                            \App\Models\Order::class => route('admin.orders', ['focusOrder' => $selectedOutbox->related_id]),
                            \App\Models\Invoice::class => route('admin.invoices', ['focusInvoice' => $selectedOutbox->related_id]),
                            default => null
                        };
                    @endphp

                    @if($relatedUrl)
                        <a href="{{ $relatedUrl }}" wire:navigate class="inline-flex items-center gap-2 rounded-2xl bg-white border border-slate-200 px-6 py-3 text-[10px] font-black text-slate-600 uppercase tracking-widest transition-all hover:border-slate-900 hover:text-slate-900">
                            <i class="fas fa-arrow-up-right-from-square"></i> Open Context
                        </a>
                    @endif
                </div>
                <button type="button" wire:click="closeDetailModal" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900">Close Dossier</button>
            </div>
        </div>
    </div>
</div>
