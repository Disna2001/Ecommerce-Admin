@props(['todayOrders', 'pendingPaymentReviews', 'monthRevenue', 'failedOutbox'])

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <x-admin.dashboard.stat-card
        label="Monthly Revenue"
        value="Rs {{ number_format($monthRevenue, 0) }}"
        icon="fa-chart-line"
        tone="indigo"
        category="Performance"
    />
    <x-admin.dashboard.stat-card
        label="Daily Orders"
        :value="$todayOrders"
        icon="fa-cart-shopping"
        tone="emerald"
        category="Volume"
    />
    <x-admin.dashboard.stat-card
        label="Payment Reviews"
        :value="$pendingPaymentReviews"
        icon="fa-shield-check"
        tone="amber"
        category="Action Required"
    />
    <x-admin.dashboard.stat-card
        label="Failed Outbox"
        :value="$failedOutbox"
        icon="fa-wifi-exclamation"
        tone="rose"
        category="System Alert"
    />
</div>
