<div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6">Readiness Checklist</p>
    
    <div class="space-y-4">
        @foreach($checklist as $item)
            <div class="flex items-center gap-4">
                <div class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full border-2 {{ $item['ready'] ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-200 text-transparent' }} transition-colors">
                    <i class="fas fa-check text-[10px]"></i>
                </div>
                <div>
                    <p class="text-xs font-bold {{ $item['ready'] ? 'text-slate-900' : 'text-slate-400' }}">{{ $item['title'] }}</p>
                    <p class="text-[9px] font-medium text-slate-400">{{ $item['help'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
