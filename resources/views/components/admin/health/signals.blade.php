<div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-start gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50 text-violet-600">
            <i class="fas fa-wave-square"></i>
        </div>
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Recent Operational Signals</h3>
            <p class="mt-1 text-sm text-slate-500">Latest admin events that help explain current system state.</p>
        </div>
    </div>
    <div class="mt-5 space-y-3">
        @forelse($recentSignals as $signal)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm font-semibold text-slate-900">{{ \Illuminate\Support\Str::headline(str_replace('.', ' ', $signal->action)) }}</p>
                <p class="mt-2 text-xs leading-6 text-slate-500">
                    {{ $signal->description ?: 'An admin action was recorded.' }}
                    @if($signal->user)
                        by {{ $signal->user->name }}
                    @endif
                </p>
                <p class="mt-2 text-xs font-medium text-slate-400">{{ $signal->created_at->diffForHumans() }}</p>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 p-5 text-sm text-slate-500">No recent signals are available yet.</div>
        @endforelse
    </div>
</div>
