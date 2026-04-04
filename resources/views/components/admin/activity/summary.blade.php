<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Total Activity</p>
        <p class="mt-3 text-3xl font-black text-slate-900">{{ number_format($stats['total']) }}</p>
        <p class="mt-2 text-sm text-slate-500">All recorded admin actions across the system.</p>
    </div>
    <div class="rounded-[1.75rem] border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-600">Today</p>
        <p class="mt-3 text-3xl font-black text-emerald-700">{{ number_format($stats['today']) }}</p>
        <p class="mt-2 text-sm text-emerald-700">Actions captured since midnight.</p>
    </div>
    <div class="rounded-[1.75rem] border border-violet-200 bg-violet-50 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-violet-600">High Impact</p>
        <p class="mt-3 text-3xl font-black text-violet-700">{{ number_format($stats['order_actions'] + $stats['invoice_actions'] + $stats['settings_changes']) }}</p>
        <p class="mt-2 text-sm text-violet-700">Orders, invoices, and settings changes combined.</p>
    </div>
</div>
