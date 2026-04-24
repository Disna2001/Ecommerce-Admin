<x-admin.ui.panel padding="p-6">
    <x-slot:header>
        <div class="mb-6">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Permission-based access guide</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">The admin area now blocks modules based on granted permissions instead of only relying on one admin role.</p>
        </div>
    </x-slot:header>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach([['Dashboard', 'view dashboard', 'See overview, sales metrics, and quick actions.'], ['Orders', 'view orders', 'Open order list, payment review, and fulfillment.'], ['Inventory', 'view inventory', 'Manage stock, categories, brands, and item setup.'], ['Settings', 'view settings', 'Access system, communications, WhatsApp, and AI config.']] as [$title, $permission, $description])
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $title }}</p><p class="mt-2 text-xs font-mono text-violet-600">{{ $permission }}</p><p class="mt-3 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $description }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900">
        <p class="text-sm font-semibold text-slate-900 dark:text-white">Current Permission Catalog</p>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">There are currently <span class="font-semibold text-slate-900 dark:text-white">{{ $permissionCount }}</span> permissions in the system. Use Roles & Permissions to assign them safely.</p>
    </div>
</x-admin.ui.panel>
