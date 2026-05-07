<div class="space-y-6">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 shadow-inner"><i class="fas fa-microchip text-lg"></i></div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Protocol Surveillance</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Core Infrastructure Readiness</p>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @foreach($checks as $check)
            @php
                $isHealthy = $check['status'] === 'healthy';
                $isWarning = $check['status'] === 'warning';
                $tone = $isHealthy ? 'emerald' : ($isWarning ? 'rose' : 'slate');
            @endphp
            <div class="group relative rounded-[2.5rem] border border-slate-200 bg-white p-6 transition-all hover:border-{{ $tone }}-400 hover:shadow-xl hover:shadow-{{ $tone }}-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $isHealthy ? 'bg-emerald-50 text-emerald-600' : ($isWarning ? 'bg-rose-50 text-rose-600' : 'bg-slate-50 text-slate-400') }} shadow-inner transition-colors">
                            <i class="fas {{ $check['icon'] ?? ($isHealthy ? 'fa-check-double' : 'fa-triangle-exclamation') }} text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-900">{{ $check['label'] }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $check['value'] }}</p>
                        </div>
                    </div>
                    <div class="h-2 w-2 rounded-full {{ $isHealthy ? 'bg-emerald-500' : ($isWarning ? 'bg-rose-500 animate-ping' : 'bg-slate-300') }}"></div>
                </div>
                <p class="mt-4 text-xs font-medium leading-relaxed text-slate-500 italic">"{{ $check['help'] }}"</p>
            </div>
        @endforeach
    </div>
</div>
