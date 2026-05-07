<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-1 rounded-[2.5rem] bg-slate-900 p-10 text-white shadow-2xl relative overflow-hidden">
        <div class="absolute right-0 top-0 -mr-12 -mt-12 h-48 w-48 rounded-full bg-{{ $scoreTone === 'emerald' ? 'emerald' : ($scoreTone === 'amber' ? 'amber' : 'rose') }}-500/10 animate-pulse"></div>
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40">Readiness Score</p>
        <div class="mt-8 flex items-baseline gap-2">
            <span class="text-7xl font-black tracking-tighter text-{{ $scoreTone === 'emerald' ? 'emerald-400' : ($scoreTone === 'amber' ? 'amber-400' : 'rose-400') }}">{{ $score }}</span>
            <span class="text-xl font-bold text-white/30">/ 100</span>
        </div>
        <p class="mt-4 text-sm font-medium leading-relaxed opacity-60">System is currently operating with <span class="text-white font-black">{{ $scoreTone === 'emerald' ? 'Optimal' : ($scoreTone === 'amber' ? 'Degraded' : 'Critical') }}</span> integrity protocols.</p>
    </div>

    <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach($metrics as $metric)
            <div class="group relative rounded-[2rem] border border-slate-200 bg-white p-6 transition-all hover:border-slate-900 hover:shadow-xl">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-colors">
                    <i class="fas {{ $metric['icon'] }} text-xs"></i>
                </div>
                <p class="mt-4 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $metric['label'] }}</p>
                <div class="mt-1 flex items-center gap-2">
                    <span class="text-lg font-black text-slate-900">{{ $metric['value'] }}</span>
                    <span class="text-[10px] font-bold text-{{ $metric['status_tone'] === 'emerald' ? 'emerald' : ($metric['status_tone'] === 'amber' ? 'amber' : 'rose') }}-500 uppercase">{{ $metric['status'] }}</span>
                </div>
            </div>
        @endforeach
    </div>
</div>
