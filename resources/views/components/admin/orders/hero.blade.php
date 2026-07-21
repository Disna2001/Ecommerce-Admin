@php
    $stats = $this->stats;
@endphp

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-admin.dashboard.stat-card label="Processing Queue" :value="number_format($stats['processing'])" icon="fa-boxes-stacked" tone="indigo" />
    <x-admin.dashboard.stat-card label="Payment Reviews" :value="number_format($stats['payment_reviews'])" icon="fa-money-check-dollar" tone="emerald" />
    <x-admin.dashboard.stat-card label="Awaiting Tracking" :value="number_format($stats['awaiting_tracking'])" icon="fa-truck-fast" tone="amber" />
    <x-admin.dashboard.stat-card label="Return Requests" :value="number_format($stats['returns'])" icon="fa-rotate-left" tone="rose" />
</div>
