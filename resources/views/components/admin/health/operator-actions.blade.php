<div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-start gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
            <i class="fas fa-arrow-up-right-dots"></i>
        </div>
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Operator Actions</h3>
            <p class="mt-1 text-sm text-slate-500">Quick jumps into the pages you’ll use most during incidents.</p>
        </div>
    </div>
    <div class="mt-5 grid gap-3">
        <a href="{{ route('admin.notification-outbox') }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-white">
            <span class="flex items-center gap-3"><i class="fas fa-inbox text-indigo-500"></i><span>Open Notification Outbox</span></span>
        </a>
        <a href="{{ route('admin.settings') }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-white">
            <span class="flex items-center gap-3"><i class="fas fa-sliders text-violet-500"></i><span>Review System Settings</span></span>
        </a>
        <a href="{{ route('admin.stock-movements') }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-white">
            <span class="flex items-center gap-3"><i class="fas fa-arrow-right-arrow-left text-emerald-500"></i><span>Inspect Stock Ledger</span></span>
        </a>
        <a href="{{ route('admin.activity-logs') }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-white">
            <span class="flex items-center gap-3"><i class="fas fa-clock-rotate-left text-rose-500"></i><span>Open Audit Trail</span></span>
        </a>
    </div>
</div>
