<div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-start gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
            <i class="fas fa-list-check"></i>
        </div>
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Deployment Checklist</h3>
            <p class="mt-1 text-sm text-slate-500">The quickest hosted-go-live review for this current environment.</p>
        </div>
    </div>
    <div class="mt-5 space-y-3">
        @foreach($checklist as $item)
            <div class="flex items-start justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div>
                    <p class="text-sm font-semibold text-slate-900">{{ $item['title'] }}</p>
                    <p class="mt-2 text-xs leading-6 text-slate-500">{{ $item['help'] }}</p>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $item['ready'] ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ $item['ready'] ? 'Ready' : 'Review' }}
                </span>
            </div>
        @endforeach
    </div>
</div>
