@props([
    'stocks',
    'movementSummary'
])

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
    <x-admin.dashboard.stat-card label="Total SKUs" :value="$stocks->total() . ' items'" icon="fa-boxes-stacked" tone="indigo" />
    <x-admin.dashboard.stat-card label="Low Stock" :value="$this->lowStockCount . ' low stock'" icon="fa-triangle-exclamation" tone="amber" />
    <x-admin.dashboard.stat-card label="Stock In" :value="$movementSummary['today_in'] . ' units'" icon="fa-arrow-down" tone="emerald" />
    <x-admin.dashboard.stat-card label="Stock Out" :value="$movementSummary['today_out'] . ' units'" icon="fa-arrow-up" tone="sky" />
    <x-admin.dashboard.stat-card label="Inventory Value" :value="'Rs ' . number_format($this->totalValue, 0)" icon="fa-coins" tone="slate" />
</div>
