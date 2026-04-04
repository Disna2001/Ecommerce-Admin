<div class="space-y-6">
    @if (session()->has('message'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('message') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{{ session('error') }}</div>
    @endif

    <x-admin.outbox.summary :stats="$stats" />
    <x-admin.outbox.analytics :analytics="$analytics" />
    <x-admin.outbox.filters :stats="$stats" />
    <x-admin.outbox.table :notifications="$notifications" />

    @if($showDetailModal && $selectedOutbox)
        <x-admin.outbox.detail-modal :selected-outbox="$selectedOutbox" />
    @endif
</div>
