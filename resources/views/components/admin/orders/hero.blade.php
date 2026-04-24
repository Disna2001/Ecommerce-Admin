<div class="rounded-[1.9rem] bg-gradient-to-r from-slate-900 via-indigo-900 to-sky-700 p-6 text-white shadow-xl">
    <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/60">Order Operations</p>
            <h2 class="mt-3 text-3xl font-black">Manage payments, dispatch, and returns from one structured workspace.</h2>
            <p class="mt-3 max-w-2xl text-sm leading-7 text-white/75">
                This screen is rebuilt for operations work first: attention queues, cleaner filters, and detail panels that help the team move faster with less scrolling.
            </p>
        </div>
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.18em] text-white/60">Today</p>
                <p class="mt-2 text-2xl font-black">{{ $this->stats['today'] }}</p>
                <p class="mt-1 text-xs text-white/60">Orders created</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.18em] text-white/60">Payment Review</p>
                <p class="mt-2 text-2xl font-black">{{ $this->stats['payment_reviews'] }}</p>
                <p class="mt-1 text-xs text-white/60">Awaiting verification</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.18em] text-white/60">Awaiting Tracking</p>
                <p class="mt-2 text-2xl font-black">{{ $this->stats['awaiting_tracking'] }}</p>
                <p class="mt-1 text-xs text-white/60">Confirmed but not shipped</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.18em] text-white/60">Open Orders</p>
                <p class="mt-2 text-2xl font-black">{{ $this->stats['pending'] + $this->stats['processing'] + $this->stats['shipped'] }}</p>
                <p class="mt-1 text-xs text-white/60">Still active</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.18em] text-white/60">Returns</p>
                <p class="mt-2 text-2xl font-black">{{ $this->stats['returns'] }}</p>
                <p class="mt-1 text-xs text-white/60">Exception flow</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.18em] text-white/60">Revenue</p>
                <p class="mt-2 text-2xl font-black">Rs {{ number_format($this->stats['revenue'], 0) }}</p>
                <p class="mt-1 text-xs text-white/60">Completed + delivered</p>
            </div>
        </div>
    </div>
</div>
