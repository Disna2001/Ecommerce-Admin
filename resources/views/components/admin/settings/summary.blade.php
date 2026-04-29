<section class="admin-surface overflow-hidden rounded-2xl border border-slate-200/80 bg-white/78 p-4 shadow-sm dark:border-slate-800 dark:bg-slate-950/60">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
        <div class="min-w-0">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">System Readiness</p>
            <h3 class="mt-1.5 text-xl font-black text-slate-900 dark:text-white">Setup status at a glance</h3>
            <p class="mt-1.5 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">
                Configure hosting first, then email, billing, WhatsApp, AI, and secret storage.
            </p>
        </div>

        <div class="flex flex-wrap gap-2 lg:max-w-sm lg:justify-end">
            @forelse($integrationSummary['enabled_channels'] as $channel)
                <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">{{ $channel }}</span>
            @empty
                <span class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 dark:border-amber-900/60 dark:bg-amber-500/15 dark:text-amber-200">No channels enabled</span>
            @endforelse
        </div>
    </div>

    <div class="mt-4 grid min-w-0 gap-3 sm:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-7">
        <x-admin.ui.metric label="Email" :value="$statusCards['email_ready'] ? 'Ready' : 'Setup'" hint="Sender and transport." />
        <x-admin.ui.metric label="WhatsApp" :value="$statusCards['whatsapp_ready'] ? 'Live' : ($statusCards['whatsapp_enabled'] ? 'Review' : 'Off')" hint="Automation state." />
        <x-admin.ui.metric label="AI" :value="$statusCards['ai_ready'] ? 'Ready' : ($statusCards['ai_enabled'] ? 'Review' : 'Off')" hint="Assistant setup." />
        <x-admin.ui.metric label="Hosting" :value="$statusCards['hosting_ready'] ? 'Ready' : 'Review'" hint="URL and locale." />
        <x-admin.ui.metric label="Billing" :value="$statusCards['billing_ready'] ? 'Ready' : 'Review'" hint="PDF and printer routing." />
        <x-admin.ui.metric label="Business" :value="$statusCards['business_ready'] ? 'Ready' : 'Review'" hint="Contact details." />
        <x-admin.ui.metric label="Secrets" :value="$integrationSummary['configured_secrets']" hint="Encrypted credentials." tone="accent" />
    </div>

    <div class="mt-4 grid gap-3 lg:grid-cols-[0.72fr_1.28fr]">
        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/70 px-4 py-3 dark:border-slate-800 dark:bg-slate-900/60">
            <p class="text-sm font-semibold text-slate-800 dark:text-white">Operator guide</p>
            <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
                Finish hosting and business identity first, then confirm delivery channels and billing output.
            </p>
        </div>

        <div class="grid gap-2 sm:grid-cols-2">
            @foreach($checklist as $item)
                <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white/75 px-3 py-2.5 text-sm dark:border-slate-800 dark:bg-slate-900/60">
                    <p class="min-w-0 font-medium text-slate-800 dark:text-white">{{ $item['label'] }}</p>
                    <span class="shrink-0 rounded-full px-3 py-1 text-xs font-semibold {{ $item['ready'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' }}">
                        {{ $item['ready'] ? 'Ready' : 'Review' }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</section>
