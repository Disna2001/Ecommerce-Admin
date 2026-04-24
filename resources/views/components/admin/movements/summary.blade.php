<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Total Movements</p>
        <p class="mt-3 text-3xl font-black text-slate-900">{{ $stats['total'] }}</p>
        <p class="mt-2 text-sm text-slate-500">Every centralized stock-in and stock-out operation.</p>
    </div>
    <div class="rounded-[1.75rem] border border-rose-200 bg-rose-50 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-rose-600">Stock Out</p>
        <p class="mt-3 text-3xl font-black text-rose-700">{{ $stats['out'] }}</p>
        <p class="mt-2 text-sm text-rose-700">Checkout and POS deductions.</p>
    </div>
    <div class="rounded-[1.75rem] border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-600">Stock In</p>
        <p class="mt-3 text-3xl font-black text-emerald-700">{{ $stats['in'] }}</p>
        <p class="mt-2 text-sm text-emerald-700">Restorations from cancellations and refunds.</p>
    </div>
</div>
