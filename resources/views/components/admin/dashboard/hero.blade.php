<div class="rounded-[2rem] bg-gradient-to-r from-slate-900 via-indigo-900 to-violet-700 p-6 text-white shadow-xl">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-white/60">Admin Operations Center</p>
            <h2 class="mt-3 text-3xl font-black">Run sales, delivery, stock, and control actions from one structured dashboard.</h2>
            <p class="mt-3 max-w-2xl text-sm leading-7 text-white/75">
                Focus first on what needs attention, then move into inventory, delivery health, storefront management, and AI-guided control tasks without hopping between disconnected pages.
            </p>
        </div>
        <div class="grid gap-3 sm:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-4 backdrop-blur"><p class="text-xs uppercase tracking-[0.2em] text-white/60">Today</p><p class="mt-2 text-2xl font-black">{{ $todayOrders }}</p><p class="mt-1 text-xs text-white/60">Orders created</p></div>
            <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-4 backdrop-blur"><p class="text-xs uppercase tracking-[0.2em] text-white/60">Payments</p><p class="mt-2 text-2xl font-black">{{ $pendingPaymentReviews }}</p><p class="mt-1 text-xs text-white/60">Awaiting review</p></div>
            <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-4 backdrop-blur"><p class="text-xs uppercase tracking-[0.2em] text-white/60">Revenue</p><p class="mt-2 text-2xl font-black">Rs {{ number_format($monthRevenue, 0) }}</p><p class="mt-1 text-xs text-white/60">This month</p></div>
            <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-4 backdrop-blur"><p class="text-xs uppercase tracking-[0.2em] text-white/60">Outbox</p><p class="mt-2 text-2xl font-black">{{ $failedOutbox }}</p><p class="mt-1 text-xs text-white/60">Failed deliveries</p></div>
        </div>
    </div>
</div>
