<div class="grid gap-6 md:grid-cols-3">
    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6">Provider Health</p>
        <div class="space-y-4">
            @foreach($analytics['providerHealth'] as $p)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-400"><i class="fas fa-server text-[10px]"></i></div>
                        <span class="text-xs font-bold text-slate-700 capitalize">{{ $p->provider }}</span>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-black text-slate-900">{{ $p->sent_count }} / {{ $p->total_count }}</p>
                        <div class="mt-1 h-1 w-20 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-emerald-500" style="width: {{ $p->total_count > 0 ? ($p->sent_count / $p->total_count) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6">Critical Failures</p>
        <div class="space-y-4">
            @forelse($analytics['failingRecipients'] as $f)
                <div class="flex items-center justify-between group">
                    <span class="text-xs font-bold text-slate-500 truncate max-w-[120px]">{{ $f->recipient }}</span>
                    <span class="rounded-lg bg-rose-50 px-2 py-1 text-[10px] font-black text-rose-600 transition-all group-hover:bg-rose-600 group-hover:text-white">{{ $f->failure_count }} FAILS</span>
                </div>
            @empty
                <p class="py-10 text-center text-xs text-slate-300 italic">No recurring failures.</p>
            @endforelse
        </div>
    </div>

    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6">Volume Matrix</p>
        <div class="space-y-4">
            @foreach($analytics['messageTypes'] as $t)
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-slate-600 uppercase tracking-tighter truncate max-w-[150px]">{{ class_basename($t->label) }}</span>
                        <span class="text-[10px] font-bold text-slate-400">{{ $t->total_count }} MSG</span>
                    </div>
                    <div class="h-1.5 w-full rounded-full bg-slate-50 overflow-hidden shadow-inner">
                        <div class="h-full bg-indigo-500" style="width: {{ min(100, ($t->total_count / max(1, optional($analytics['messageTypes']->first())->total_count ?: 1)) * 100) }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
