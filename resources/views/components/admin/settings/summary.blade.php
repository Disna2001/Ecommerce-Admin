<section class="admin-surface rounded-[2rem] border border-white/60 bg-[radial-gradient(circle_at_top_left,_rgba(16,185,129,0.18),_transparent_35%),linear-gradient(135deg,rgba(255,255,255,0.96),rgba(248,250,252,0.92))] p-6 shadow-[0_25px_80px_rgba(15,23,42,0.10)] dark:border-white/10 dark:bg-[radial-gradient(circle_at_top_left,_rgba(16,185,129,0.20),_transparent_28%),linear-gradient(135deg,rgba(15,23,42,0.95),rgba(17,24,39,0.92))]">
    <div class="grid gap-6 xl:grid-cols-[1.7fr_0.9fr]">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
            <x-admin.ui.metric label="Email" :value="$statusCards['email_ready'] ? 'Ready' : 'Setup'" hint="Sender and transport health." />
            <x-admin.ui.metric label="WhatsApp" :value="$statusCards['whatsapp_ready'] ? 'Live' : ($whatsapp_enabled ? 'Review' : 'Off')" hint="Automation channel state." />
            <x-admin.ui.metric label="AI" :value="$statusCards['ai_ready'] ? 'Ready' : ($ai_enabled ? 'Review' : 'Off')" hint="Assistant model and key status." />
            <x-admin.ui.metric label="Hosting" :value="$statusCards['hosting_ready'] ? 'Ready' : 'Review'" hint="URL, locale, timezone, and deploy identity." />
            <x-admin.ui.metric label="Business" :value="$statusCards['business_ready'] ? 'Ready' : 'Review'" hint="Customer-facing contact details." />
            <x-admin.ui.metric label="Configured Secrets" :value="$integrationSummary['configured_secrets']" hint="Stored encrypted credentials." tone="accent" />
        </div>

        <x-admin.ui.panel padding="p-5" body-class="space-y-5">
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Enabled Channels</h3>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">{{ count($integrationSummary['enabled_channels']) }}</span>
                </div>
            </x-slot:header>

            <div class="flex flex-wrap gap-2">
                @forelse($integrationSummary['enabled_channels'] as $channel)
                    <span class="rounded-full bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-200">{{ $channel }}</span>
                @empty
                    <span class="text-sm text-slate-500 dark:text-slate-400">No channels configured yet.</span>
                @endforelse
            </div>

            <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-4 dark:border-slate-700">
                <p class="text-sm font-semibold text-slate-800 dark:text-white">Operator guide</p>
                <ul class="mt-3 space-y-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
                    <li>Finish hosting and business identity first so links, invoices, and emails point to the right place.</li>
                    <li>Finish email next so customers always receive order progress updates.</li>
                    <li>Enable WhatsApp only after endpoint and token are tested.</li>
                    <li>Turn on AI once the model and key are both confirmed.</li>
                </ul>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-semibold text-slate-800 dark:text-white">Deployment checklist</p>
                <div class="mt-3 space-y-2">
                    @foreach($checklist as $item)
                        <div class="flex items-start justify-between gap-3 rounded-2xl bg-white px-3 py-3 text-sm dark:bg-slate-950">
                            <p class="font-medium text-slate-800 dark:text-white">{{ $item['label'] }}</p>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $item['ready'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' }}">
                                {{ $item['ready'] ? 'Ready' : 'Review' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-admin.ui.panel>
    </div>
</section>
