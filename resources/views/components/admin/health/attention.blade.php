<div class="space-y-6">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 shadow-inner"><i class="fas fa-eye text-lg"></i></div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Diagnostic Observations</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Active System Warnings & Advice</p>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($attention as $item)
            <a href="{{ $item['route'] }}" class="group flex items-start gap-5 rounded-[2rem] border border-slate-200 bg-white p-6 transition-all hover:border-{{ $item['tone'] }}-400 hover:shadow-xl hover:shadow-{{ $item['tone'] }}-50">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-{{ $item['tone'] }}-50 text-{{ $item['tone'] }}-600 group-hover:bg-{{ $item['tone'] }}-500 group-hover:text-white transition-colors">
                    <i class="fas {{ $item['icon'] ?? 'fa-circle-info' }} text-sm"></i>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-black text-slate-900">{{ $item['title'] }}</p>
                        <span class="rounded-lg bg-{{ $item['tone'] }}-100 px-2 py-0.5 text-[10px] font-black text-{{ $item['tone'] }}-700">{{ $item['count'] }}</span>
                    </div>
                    <p class="text-xs font-medium leading-relaxed text-slate-500">{{ $item['note'] }}</p>
                </div>
            </a>
        @empty
            <div class="rounded-[2.5rem] border-2 border-dashed border-slate-100 p-12 text-center bg-slate-50/50">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-slate-200 shadow-sm mb-4"><i class="fas fa-shield-check text-2xl"></i></div>
                <p class="text-sm font-black text-slate-900">Operational Integrity Maintained</p>
                <p class="mt-1 text-xs font-medium text-slate-400">No diagnostic warnings require immediate administrative attention.</p>
            </div>
        @endforelse
    </div>
</div>
