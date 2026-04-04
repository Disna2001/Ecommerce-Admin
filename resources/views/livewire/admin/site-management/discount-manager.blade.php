<div class="mx-auto max-w-6xl space-y-6">
    @if(session('message'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('message') }}</div>
    @endif

    @include('components.admin.discounts.hero')
    @include('components.admin.discounts.table')
    @include('components.admin.discounts.modal')
</div>
