<div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5 flex items-start gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600">
            <i class="fas fa-siren-on"></i>
        </div>
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Immediate Attention</h3>
            <p class="mt-1 text-sm text-slate-500">Operational issues that should be handled before routine admin work.</p>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @forelse($attention as $item)
            <a href="{{ $item['route'] }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-white">
                <div class="flex items-start gap-4">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl {{ $item['tone'] === 'rose' ? 'bg-rose-100 text-rose-600' : ($item['tone'] === 'amber' ? 'bg-amber-100 text-amber-600' : ($item['tone'] === 'emerald' ? 'bg-emerald-100 text-emerald-600' : 'bg-indigo-100 text-indigo-600')) }}">
                        <i class="fas {{ $item['icon'] }}"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ $item['title'] }}</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $item['count'] }}</p>
                        <p class="mt-2 text-xs leading-6 text-slate-500">{{ $item['note'] }}</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 p-5 text-sm text-slate-500">No urgent production issues are visible right now.</div>
        @endforelse
    </div>
</div>
