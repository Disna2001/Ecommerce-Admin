<div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5 flex items-start gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
            <x-admin.icon name="fa-arrow-right-arrow-left" />
        </div>
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Stock & Delivery Pulse</h3>
            <p class="mt-1 text-sm text-slate-500">A quick read on movement volume and notification throughput today.</p>
        </div>
    </div>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4"><p class="text-sm font-semibold text-sky-900">Stock Out</p><p class="mt-2 text-3xl font-black text-sky-700">{{ $todayStockOut }}</p><p class="mt-2 text-sm text-sky-800">Units deducted today.</p></div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4"><p class="text-sm font-semibold text-emerald-900">Restocks</p><p class="mt-2 text-3xl font-black text-emerald-700">{{ $todayStockIn }}</p><p class="mt-2 text-sm text-emerald-800">Units added back today.</p></div>
        <div class="rounded-2xl border border-violet-200 bg-violet-50 p-4"><p class="text-sm font-semibold text-violet-900">Reversals</p><p class="mt-2 text-3xl font-black text-violet-700">{{ $todayReversals }}</p><p class="mt-2 text-sm text-violet-800">Cancellations and returns.</p></div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-sm font-semibold text-slate-900">Delivered Messages</p><p class="mt-2 text-3xl font-black text-slate-900">{{ $sentOutboxToday }}</p><p class="mt-2 text-sm text-slate-600">Outbox entries sent today.</p></div>
    </div>
</div>
