@props(['quickActions'])

<div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
    <div class="flex items-center gap-4 mb-8">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white shadow-lg">
            <i class="fas fa-bolt-lightning text-xs"></i>
        </div>
        <div>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Administrative Velocity</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">High-Frequency Shortcuts</p>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        @foreach($quickActions as $action)
            <a href="{{ $action['route'] }}" class="group flex flex-col justify-between p-6 rounded-[2rem] border border-slate-100 bg-slate-50 hover:bg-white hover:border-slate-200 hover:shadow-xl transition-all duration-500">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white shadow-sm text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-all duration-500 mb-6">
                    <i class="fas {{ $action['icon'] }} text-sm"></i>
                </div>
                <div>
                    <h5 class="text-[11px] font-black text-slate-900 uppercase tracking-widest">{{ $action['title'] }}</h5>
                    <p class="mt-2 text-[9px] font-bold text-slate-400 uppercase tracking-tighter leading-relaxed">{{ $action['desc'] }}</p>
                </div>
                <div class="mt-6 flex items-center gap-2 text-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity">
                    <span class="text-[8px] font-black uppercase tracking-[0.2em]">Initialize</span>
                    <i class="fas fa-arrow-right text-[8px]"></i>
                </div>
            </a>
        @endforeach
    </div>
</div>
