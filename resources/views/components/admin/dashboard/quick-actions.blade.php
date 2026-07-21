@props(['quickActions'])

<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-center gap-3 mb-5 pb-3 border-b border-slate-100">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-700 font-semibold">
            <i class="fas fa-bolt-lightning text-xs"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-slate-900">Quick Actions</h3>
            <p class="text-xs text-slate-500 font-medium">Frequently accessed administrative shortcuts</p>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        @foreach($quickActions as $action)
            <a href="{{ $action['route'] }}" class="group flex flex-col justify-between p-5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-slate-300 hover:shadow-sm transition-all duration-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white border border-slate-200 shadow-xs text-slate-700 group-hover:bg-slate-900 group-hover:text-white transition-colors duration-200">
                        <i class="fas {{ $action['icon'] }} text-xs"></i>
                    </div>
                    <i class="fas fa-arrow-right text-xs text-slate-400 group-hover:text-slate-900 group-hover:translate-x-0.5 transition-all"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wide">{{ $action['title'] }}</h4>
                    <p class="mt-1 text-xs text-slate-500 font-medium leading-relaxed">{{ $action['desc'] }}</p>
                </div>
            </a>
        @endforeach
    </div>
</div>
