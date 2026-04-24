<div class="grid gap-4 md:grid-cols-3">
    <x-admin.ui.metric label="Featured" :value="count($featuredIds)" hint="Hero rail products" tone="blue" />
    <x-admin.ui.metric label="New Arrivals" :value="count($newArrivalsIds)" hint="Fresh catalog picks" tone="accent" />
    <x-admin.ui.metric label="Deals" :value="count($dealIds)" hint="Promotional items" tone="amber" />
</div>
<div class="mt-4 grid gap-4 md:grid-cols-3">
    <x-admin.ui.metric label="Rail Layout" :value="ucfirst($railLayout)" hint="Public product card rhythm" tone="slate" />
    <x-admin.ui.metric label="Items Per Rail" :value="$productsPerRail" hint="Homepage cards per section" tone="blue" />
    <x-admin.ui.metric label="Low Stock" :value="$displayStats['low_stock']" hint="Products near reorder level" tone="amber" />
</div>
