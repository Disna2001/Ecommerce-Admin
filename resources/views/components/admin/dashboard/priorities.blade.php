<div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5 flex items-start gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
            <x-admin.icon name="fa-triangle-exclamation" />
        </div>
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Operational Priorities</h3>
            <p class="mt-1 text-sm text-slate-500">The highest-signal tasks to check first.</p>
        </div>
    </div>
    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4"><p class="text-sm font-semibold text-amber-900">Pending Orders</p><p class="mt-2 text-3xl font-black text-amber-700">{{ $pendingOrders }}</p><p class="mt-2 text-sm text-amber-800">Orders still waiting for confirmation or processing.</p></div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4"><p class="text-sm font-semibold text-emerald-900">Payment Reviews</p><p class="mt-2 text-3xl font-black text-emerald-700">{{ $pendingPaymentReviews }}</p><p class="mt-2 text-sm text-emerald-800">Submitted proofs waiting for verification.</p></div>
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4"><p class="text-sm font-semibold text-rose-900">Low Stock</p><p class="mt-2 text-3xl font-black text-rose-700">{{ $lowStockCount }}</p><p class="mt-2 text-sm text-rose-800">Products at or below reorder threshold.</p></div>
    </div>
</div>
