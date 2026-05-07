@props(['items'])

<div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
    <div class="flex items-center gap-4 mb-8">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white shadow-lg">
            <i class="fas fa-server text-xs"></i>
        </div>
        <div>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">System Integrity</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Service & Protocol Status</p>
        </div>
    </div>

    <div class="space-y-4">
        @foreach($items as [$label, $status, $icon, $colorClass])
            <div class="flex items-center justify-between p-5 rounded-2xl bg-slate-50 border border-slate-100 group hover:bg-white hover:border-slate-200 hover:shadow-md transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-inner text-slate-400 group-hover:text-slate-900 transition-colors">
                        <i class="fas {{ $icon }} text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest leading-none">{{ $label }}</p>
                        <p class="mt-1.5 text-[9px] font-bold {{ $colorClass }} uppercase tracking-tighter">{{ $status }}</p>
                    </div>
                </div>
                <div class="h-2 w-2 rounded-full {{ str_contains($colorClass, 'emerald') ? 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]' : (str_contains($colorClass, 'rose') ? 'bg-rose-500 shadow-[0_0_10px_rgba(239,68,68,0.5)]' : 'bg-amber-500 shadow-[0_0_10px_rgba(245,158,11,0.5)]') }}"></div>
            </div>
        @endforeach
    </div>
</div>
