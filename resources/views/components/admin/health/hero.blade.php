<div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Production Readiness</p>
            <h2 class="mt-2 text-2xl font-bold text-slate-900">System Health Center</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Track queue readiness, delivery health, storage availability, and service configuration from one production-focused screen.</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border {{ $scoreTone === 'emerald' ? 'border-emerald-200 bg-emerald-50' : ($scoreTone === 'amber' ? 'border-amber-200 bg-amber-50' : 'border-rose-200 bg-rose-50') }} p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] {{ $scoreTone === 'emerald' ? 'text-emerald-500' : ($scoreTone === 'amber' ? 'text-amber-500' : 'text-rose-500') }}">Hosting Score</p>
                <p class="mt-2 text-3xl font-black {{ $scoreTone === 'emerald' ? 'text-emerald-700' : ($scoreTone === 'amber' ? 'text-amber-700' : 'text-rose-700') }}">{{ $score }}%</p>
                <p class="mt-1 text-sm {{ $scoreTone === 'emerald' ? 'text-emerald-700/80' : ($scoreTone === 'amber' ? 'text-amber-700/80' : 'text-rose-700/80') }}">Overall hosted readiness.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Queued</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $metrics['queued'] }}</p>
                <p class="mt-1 text-sm text-slate-500">Outbox entries waiting.</p>
            </div>
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-rose-500">Stale Queue</p>
                <p class="mt-2 text-3xl font-black text-rose-700">{{ $metrics['stale_queued'] }}</p>
                <p class="mt-1 text-sm text-rose-700/80">Older than {{ $this->staleWindowMinutes }} minutes.</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-500">Failed</p>
                <p class="mt-2 text-3xl font-black text-amber-700">{{ $metrics['failed'] }}</p>
                <p class="mt-1 text-sm text-amber-700/80">Need review or retry.</p>
            </div>
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-500">Retried</p>
                <p class="mt-2 text-3xl font-black text-indigo-700">{{ $metrics['retried'] }}</p>
                <p class="mt-1 text-sm text-indigo-700/80">Entries with multiple attempts.</p>
            </div>
        </div>
    </div>
</div>
