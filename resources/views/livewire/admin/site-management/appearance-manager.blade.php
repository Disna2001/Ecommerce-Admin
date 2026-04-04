<div class="space-y-6">
    @include('components.admin.appearance.hero')

    @if($saved)
        <div class="rounded-3xl border border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm font-medium text-emerald-700">Storefront settings saved successfully.</div>
    @endif

    @include('components.admin.appearance.summary')

    <div class="grid gap-6 xl:grid-cols-[270px_minmax(0,1fr)]">
        @include('components.admin.appearance.nav')

        <div class="space-y-6">
            @if($activeTab === 'branding')
                @include('components.admin.appearance.tab-branding')
            @endif

            @if($activeTab === 'homepage')
                @include('components.admin.appearance.tab-homepage')
            @endif

            @if($activeTab === 'colors')
                @include('components.admin.appearance.tab-colors')
            @endif

            @if($activeTab === 'hero')
                @include('components.admin.appearance.tab-hero')
            @endif

            @if($activeTab === 'sections')
                @include('components.admin.appearance.tab-sections')
            @endif

            @if($activeTab === 'topbar')
                @include('components.admin.appearance.tab-topbar')
            @endif

            @if($activeTab === 'detail')
                @include('components.admin.appearance.tab-detail')
            @endif

            @if($activeTab === 'categories')
                @include('components.admin.appearance.tab-categories')
            @endif

            @if($activeTab === 'payment')
                @include('components.admin.appearance.tab-payment')
            @endif

            @if($activeTab === 'navigation')
                @include('components.admin.appearance.tab-navigation')
            @endif

            @if($activeTab === 'footer')
                @include('components.admin.appearance.tab-footer')
            @endif
        </div>
    </div>
</div>
