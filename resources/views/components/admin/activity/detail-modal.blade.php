<div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center px-4 py-8">
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm" wire:click="closeDetailModal"></div>
        
        <div class="relative z-10 w-full max-w-2xl overflow-hidden rounded-[2.5rem] border border-slate-200 bg-white shadow-2xl">
            <div class="border-b border-slate-100 bg-white px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-[1rem] bg-slate-900 text-white shadow-lg shadow-slate-200"><i class="fas fa-fingerprint text-lg"></i></div>
                        <div>
                            <h3 class="text-xl font-black tracking-tight text-slate-900">Log Dossier</h3>
                            <p class="text-xs font-medium text-slate-400 uppercase tracking-widest">{{ $selectedLog->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeDetailModal" class="h-10 w-10 flex items-center justify-center rounded-full border border-slate-100 bg-slate-50 text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-500">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
            </div>

            <div class="max-h-[70vh] overflow-y-auto px-8 py-8 space-y-8">
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Action Protocol</p>
                        <p class="text-sm font-bold text-slate-900">{{ $selectedLog->action }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Agent Identity</p>
                        <p class="text-sm font-bold text-slate-900">{{ $selectedLog->user->name ?? 'System Authority' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Subject Context</p>
                        <p class="text-sm font-bold text-slate-900">{{ class_basename($selectedLog->subject_type ?? 'General') }} #{{ $selectedLog->subject_id ?? 'N/A' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">IP Signature</p>
                        <p class="text-sm font-bold font-mono text-indigo-600">{{ $selectedLog->ip_address ?? '0.0.0.0' }}</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Administrative Description</p>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 text-sm font-medium text-slate-700 leading-relaxed italic">
                        "{{ $selectedLog->description ?: 'No narrative provided for this protocol.' }}"
                    </div>
                </div>

                @if(!empty($selectedLog->properties))
                    <div class="space-y-2">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">State Mutation Data</p>
                        <div class="rounded-2xl bg-slate-900 p-6 shadow-inner overflow-x-auto">
                            <pre class="text-[11px] font-mono text-emerald-400 leading-relaxed">{{ json_encode($selectedLog->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50/50 px-8 py-6">
                @if($selectedLog->related_url)
                    <a href="{{ $selectedLog->related_url }}" wire:navigate class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-6 py-3 text-[10px] font-black text-white uppercase tracking-widest shadow-xl shadow-indigo-100 transition-all hover:bg-indigo-700 hover:scale-[1.02]">
                        <i class="fas fa-arrow-up-right-from-square"></i> Open Related Workspace
                    </a>
                @else
                    <div></div>
                @endif
                <button type="button" wire:click="closeDetailModal" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900">Close Dossier</button>
            </div>
        </div>
    </div>
</div>
