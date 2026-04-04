<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Total Notifications</p>
        <p class="mt-3 text-3xl font-black text-slate-900">{{ $stats['total'] }}</p>
        <p class="mt-2 text-sm text-slate-500">Queued, sent, failed, and skipped delivery records.</p>
    </div>
    <div class="rounded-[1.75rem] border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-600">Delivered</p>
        <p class="mt-3 text-3xl font-black text-emerald-700">{{ $stats['sent'] }}</p>
        <p class="mt-2 text-sm text-emerald-700">Notifications marked as sent successfully.</p>
    </div>
    <div class="rounded-[1.75rem] border border-rose-200 bg-rose-50 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-rose-600">Failed</p>
        <p class="mt-3 text-3xl font-black text-rose-700">{{ $stats['failed'] }}</p>
        <p class="mt-2 text-sm text-rose-700">Records needing operator attention or retry logic.</p>
    </div>
    <div class="rounded-[1.75rem] border border-indigo-200 bg-indigo-50 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-indigo-600">Retries & Rate</p>
        <p class="mt-3 text-3xl font-black text-indigo-700">{{ $stats['retried'] }}</p>
        <p class="mt-2 text-sm text-indigo-700">{{ $stats['failure_rate'] }}% overall failure rate.</p>
    </div>
</div>
