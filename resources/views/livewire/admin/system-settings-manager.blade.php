<div class="space-y-6">
    <x-admin.settings.header />

    @if($saved)
        <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">System settings saved successfully.</div>
    @endif

    <x-admin.settings.summary :status-cards="$statusCards" :integration-summary="$integrationSummary" :checklist="$checklist" />

    <div class="grid min-w-0 gap-6 xl:grid-cols-[250px_minmax(0,1fr)]">
        <x-admin.settings.nav :active-tab="$activeTab" />

        <div class="min-w-0 space-y-6 overflow-hidden">
            @if($activeTab === 'communications')
                <x-admin.settings.tab-communications />
            @endif

            @if($activeTab === 'hosting')
                <x-admin.settings.tab-hosting />
            @endif

            @if($activeTab === 'billing')
                <x-admin.settings.tab-billing :billing-profiles="$billing_profiles" :billing-default-profiles="$billing_default_profiles" />
            @endif

            @if($activeTab === 'api_keys')
                <x-admin.settings.tab-api-keys />
            @endif

            @if($activeTab === 'whatsapp')
                <x-admin.settings.tab-whatsapp :app-public-url="$app_public_url" />
            @endif

            @if($activeTab === 'ai')
                <x-admin.settings.tab-ai :ai-model="$ai_model" />
            @endif

            @if($activeTab === 'access')
                <x-admin.settings.tab-access :permission-count="$permissionCount" />
            @endif
        </div>
    </div>
</div>
