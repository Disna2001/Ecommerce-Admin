<div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6">Recent System Signals</p>
    
    <div class="space-y-6">
        @forelse($recentSignals as $signal)
            <div class="relative pl-6 border-l-2 border-slate-200 group-hover:border-slate-900 transition-colors">
                <div class="absolute -left-[9px] top-0 h-4 w-4 rounded-full border-4 border-white bg-slate-400"></div>
                <div class="flex items-center justify-between gap-4">
                    <p class="text-xs font-black text-slate-900 uppercase tracking-tighter">{{ str_replace('_', ' ', $signal->context) }}</p>
                    <span class="text-[9px] font-bold text-slate-400 uppercase">{{ $signal->created_at->diffForHumans() }}</span>
                </div>
                <p class="mt-1 text-[10px] font-medium text-slate-500 leading-relaxed">{{ \Illuminate\Support\Str::limit($signal->notes, 80) }}</p>
            </div>
        @empty
            <p class="text-xs text-slate-400 italic">No telemetry signals recorded.</p>
        @endforelse
    </div>
</div>
