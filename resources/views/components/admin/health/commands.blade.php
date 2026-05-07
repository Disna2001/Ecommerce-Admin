<div class="rounded-[2rem] border border-slate-200 bg-slate-900 p-8 text-white shadow-2xl relative overflow-hidden">
    <div class="absolute right-0 top-0 -mr-8 -mt-8 h-32 w-32 rounded-full bg-indigo-500/10"></div>
    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-6">Deployment Protocols</p>
    
    <div class="space-y-4">
        @foreach($deployCommands as $command)
            <div class="group flex flex-col gap-2 rounded-2xl bg-white/5 p-4 border border-white/5 transition-all hover:bg-white/10 hover:border-white/20">
                <p class="text-[10px] font-black uppercase tracking-widest text-indigo-400">{{ $command['label'] }}</p>
                <div class="flex items-center justify-between gap-4">
                    <code class="text-xs font-mono text-white/80 select-all">{{ $command['command'] }}</code>
                    <button x-data @click="navigator.clipboard.writeText('{{ $command['command'] }}')" class="h-8 w-8 flex items-center justify-center rounded-lg bg-white/10 text-white hover:bg-indigo-500 transition-colors">
                        <i class="fas fa-copy text-[10px]"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
