<section class="admin-surface overflow-hidden rounded-[2rem] border border-white/60 bg-white/95 p-5 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/85">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="min-w-0">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">System Readiness</p>
            <h3 class="mt-2 text-2xl font-black text-slate-900 dark:text-white">Setup status at a glance</h3>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">
                Keep the settings screen compact: configure hosting first, then email, billing output, WhatsApp, AI, and secrets.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            @forelse($integrationSummary['enabled_channels'] as $channel)
                <span class="rounded-full bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-200">{{ $channel }}</span>
            @empty
                <span class="rounded-full bg-amber-100 px-3 py-2 text-xs font-semibold text-amber-700 dark:bg-amber-500/15 dark:text-amber-200">No channels enabled</span>
            @endforelse
        </div>
    </div>

    <div class="mt-5 grid min-w-0 gap-3 sm:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-7">
        <x-admin.ui.metric label="Email" :value="$statusCards['email_ready'] ? 'Ready' : 'Setup'" hint="Sender and transport." />
        <x-admin.ui.metric label="WhatsApp" :value="$statusCards['whatsapp_ready'] ? 'Live' : ($statusCards['whatsapp_enabled'] ? 'Review' : 'Off')" hint="Automation state." />
        <x-admin.ui.metric label="AI" :value="$statusCards['ai_ready'] ? 'Ready' : ($statusCards['ai_enabled'] ? 'Review' : 'Off')" hint="Assistant setup." />
        <x-admin.ui.metric label="Hosting" :value="$statusCards['hosting_ready'] ? 'Ready' : 'Review'" hint="URL and locale." />
        <x-admin.ui.metric label="Billing" :value="$statusCards['billing_ready'] ? 'Ready' : 'Review'" hint="PDF and printer routing." />
        <x-admin.ui.metric label="Business" :value="$statusCards['business_ready'] ? 'Ready' : 'Review'" hint="Contact details." />
        <x-admin.ui.metric label="Secrets" :value="$integrationSummary['configured_secrets']" hint="Encrypted credentials." tone="accent" />
    </div>

    <div class="mt-5 grid gap-4 lg:grid-cols-[1fr_1.2fr]">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm font-semibold text-slate-800 dark:text-white">Operator guide</p>
            <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
                Finish hosting and business identity first, then email delivery, bill profile routing, WhatsApp automation, and AI credentials.
            </p>
        </div>

        <div class="grid gap-2 sm:grid-cols-2">
            @foreach($checklist as $item)
                <div class="flex items-start justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-3 text-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="min-w-0 font-medium text-slate-800 dark:text-white">{{ $item['label'] }}</p>
                    <span class="shrink-0 rounded-full px-3 py-1 text-xs font-semibold {{ $item['ready'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' }}">
                        {{ $item['ready'] ? 'Ready' : 'Review' }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</section>
