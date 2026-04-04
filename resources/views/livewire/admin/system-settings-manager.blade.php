<div class="space-y-6">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.32em] text-slate-400 dark:text-slate-500">Operations Stack</p>
            <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white">Communications, AI, and integration controls</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">Run delivery channels, AI assistant behavior, and operational credentials from one structured admin workspace.</p>
        </div>
        <button wire:click="save" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/10 transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">
            <span wire:loading.remove wire:target="save"><i class="fas fa-save"></i> Save Settings</span>
            <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin"></i> Saving...</span>
        </button>
    </div>

    @if($saved)
        <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">System settings saved successfully.</div>
    @endif

    <section class="admin-surface rounded-[2rem] border border-white/60 bg-[radial-gradient(circle_at_top_left,_rgba(16,185,129,0.18),_transparent_35%),linear-gradient(135deg,rgba(255,255,255,0.96),rgba(248,250,252,0.92))] p-6 shadow-[0_25px_80px_rgba(15,23,42,0.10)] dark:border-white/10 dark:bg-[radial-gradient(circle_at_top_left,_rgba(16,185,129,0.20),_transparent_28%),linear-gradient(135deg,rgba(15,23,42,0.95),rgba(17,24,39,0.92))]">
        <div class="grid gap-6 xl:grid-cols-[1.7fr_0.9fr]">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                <div class="rounded-3xl border border-white/70 bg-white/80 p-4 shadow-sm dark:border-white/10 dark:bg-slate-900/70"><p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500">Email</p><p class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ $statusCards['email_ready'] ? 'Ready' : 'Setup' }}</p><p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Sender and transport health.</p></div>
                <div class="rounded-3xl border border-white/70 bg-white/80 p-4 shadow-sm dark:border-white/10 dark:bg-slate-900/70"><p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500">WhatsApp</p><p class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ $statusCards['whatsapp_ready'] ? 'Live' : ($whatsapp_enabled ? 'Review' : 'Off') }}</p><p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Automation channel state.</p></div>
                <div class="rounded-3xl border border-white/70 bg-white/80 p-4 shadow-sm dark:border-white/10 dark:bg-slate-900/70"><p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500">AI</p><p class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ $statusCards['ai_ready'] ? 'Ready' : ($ai_enabled ? 'Review' : 'Off') }}</p><p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Assistant model and key status.</p></div>
                <div class="rounded-3xl border border-white/70 bg-white/80 p-4 shadow-sm dark:border-white/10 dark:bg-slate-900/70"><p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500">Hosting</p><p class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ $statusCards['hosting_ready'] ? 'Ready' : 'Review' }}</p><p class="mt-2 text-sm text-slate-500 dark:text-slate-400">URL, locale, timezone, and deploy identity.</p></div>
                <div class="rounded-3xl border border-white/70 bg-white/80 p-4 shadow-sm dark:border-white/10 dark:bg-slate-900/70"><p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500">Business</p><p class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ $statusCards['business_ready'] ? 'Ready' : 'Review' }}</p><p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Customer-facing contact details.</p></div>
                <div class="rounded-3xl border border-white/70 bg-gradient-to-br from-emerald-500 via-cyan-500 to-blue-500 p-4 shadow-lg shadow-emerald-500/20 dark:border-white/10"><p class="text-xs font-semibold uppercase tracking-[0.35em] text-white/70">Configured Secrets</p><p class="mt-3 text-3xl font-black text-white">{{ $integrationSummary['configured_secrets'] }}</p><p class="mt-2 text-sm text-white/80">Stored encrypted credentials.</p></div>
            </div>

            <aside class="rounded-[1.75rem] border border-white/70 bg-white/85 p-5 shadow-sm dark:border-white/10 dark:bg-slate-900/75">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Enabled Channels</h3>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">{{ count($integrationSummary['enabled_channels']) }}</span>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">
                    @forelse($integrationSummary['enabled_channels'] as $channel)
                        <span class="rounded-full bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-200">{{ $channel }}</span>
                    @empty
                        <span class="text-sm text-slate-500 dark:text-slate-400">No channels configured yet.</span>
                    @endforelse
                </div>
                <div class="mt-5 rounded-2xl border border-dashed border-slate-200 px-4 py-4 dark:border-slate-700">
                    <p class="text-sm font-semibold text-slate-800 dark:text-white">Operator guide</p>
                    <ul class="mt-3 space-y-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
                        <li>Finish hosting and business identity first so links, invoices, and emails point to the right place.</li>
                        <li>Finish email next so customers always receive order progress updates.</li>
                        <li>Enable WhatsApp only after endpoint and token are tested.</li>
                        <li>Turn on AI once the model and key are both confirmed.</li>
                    </ul>
                </div>
                <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm font-semibold text-slate-800 dark:text-white">Deployment checklist</p>
                    <div class="mt-3 space-y-2">
                        @foreach($checklist as $item)
                            <div class="flex items-start justify-between gap-3 rounded-2xl bg-white px-3 py-3 text-sm dark:bg-slate-950">
                                <div>
                                    <p class="font-medium text-slate-800 dark:text-white">{{ $item['label'] }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $item['ready'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' }}">
                                    {{ $item['ready'] ? 'Ready' : 'Review' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-[250px_minmax(0,1fr)]">
        <aside class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-4 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
            <p class="px-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-400 dark:text-slate-500">Sections</p>
            <div class="mt-3 space-y-2">
                @foreach(['communications' => ['Email & Alerts', 'fa-envelope'], 'hosting' => ['Hosting & Identity', 'fa-server'], 'api_keys' => ['API Keys', 'fa-key'], 'whatsapp' => ['WhatsApp', 'fa-comment-dots'], 'ai' => ['AI Operations', 'fa-robot'], 'access' => ['Access Guide', 'fa-user-shield']] as $tab => [$label, $icon])
                    <button wire:click="$set('activeTab', '{{ $tab }}')" @class(['flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-left text-sm font-medium transition', 'bg-slate-900 text-white shadow-md shadow-slate-900/10 dark:bg-white dark:text-slate-900' => $activeTab === $tab, 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-900 dark:hover:text-white' => $activeTab !== $tab])>
                        <i class="fas {{ $icon }} w-4 text-center"></i><span>{{ $label }}</span>
                    </button>
                @endforeach
            </div>
        </aside>

        <div class="space-y-6">
            @if($activeTab === 'communications')
                <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-6 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                    <div class="mb-6"><h3 class="text-xl font-bold text-slate-900 dark:text-white">Email sending and alert routing</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Choose the email identity and transport the system should use for order, support, and operational notifications.</p></div>
                    <div class="grid gap-4 lg:grid-cols-2">
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Mailer</label><select wire:model="mail_mailer" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"><option value="smtp">SMTP</option><option value="log">Log</option><option value="ses">Amazon SES</option><option value="mailgun">Mailgun</option></select></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">From Name</label><input type="text" wire:model="mail_from_name" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">From Address</label><input type="email" wire:model="mail_from_address" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">SMTP Host</label><input type="text" wire:model="mail_smtp_host" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">SMTP Port</label><input type="text" wire:model="mail_smtp_port" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Encryption</label><input type="text" wire:model="mail_smtp_encryption" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">SMTP Username</label><input type="text" wire:model="mail_smtp_username" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">SMTP Password</label><input type="password" wire:model="mail_smtp_password" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Order Notification Email</label><input type="email" wire:model="order_notification_email" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Support Notification Email</label><input type="email" wire:model="support_notification_email" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                    </div>
                    <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                            <div>
                                <h4 class="text-lg font-semibold text-slate-900 dark:text-white">Live mail test</h4>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Send a real test email with the current form values so you can verify hosting before going live.</p>
                            </div>
                            <div class="flex w-full flex-col gap-3 sm:flex-row xl:w-auto">
                                <input type="email" wire:model="test_email_recipient" placeholder="ops@example.com" class="w-full min-w-[260px] rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                <button wire:click="sendTestEmail" type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">
                                    <span wire:loading.remove wire:target="sendTestEmail"><i class="fas fa-paper-plane"></i> Send Test Email</span>
                                    <span wire:loading wire:target="sendTestEmail"><i class="fas fa-spinner fa-spin"></i> Sending...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($activeTab === 'hosting')
                <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-6 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Hosting, URL, and business identity</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Control the public URL, HTTPS behavior, locale, and customer-facing business details used by emails, PDFs, invoices, and storefront help areas.</p>
                    </div>
                    <div class="grid gap-4 lg:grid-cols-2">
                        <div class="lg:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Public App URL</label><input type="url" wire:model="app_public_url" placeholder="https://yourdomain.com" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 lg:col-span-2"><input type="checkbox" wire:model="force_https" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Force HTTPS for generated URLs and links</label>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">App Timezone</label><input type="text" wire:model="app_timezone" placeholder="Asia/Colombo" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">App Locale</label><input type="text" wire:model="app_locale" placeholder="en" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Currency Code</label><input type="text" wire:model="currency_code" placeholder="LKR" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Currency Symbol</label><input type="text" wire:model="currency_symbol" placeholder="Rs" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Support Email</label><input type="email" wire:model="support_email" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Support Phone</label><input type="text" wire:model="support_phone" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div class="lg:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Company Address</label><textarea wire:model="company_address" rows="3" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></textarea></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Company Tax ID</label><input type="text" wire:model="company_tax_id" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Asset CDN Base URL</label><input type="url" wire:model="asset_cdn_url" placeholder="https://cdn.yourdomain.com" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                    </div>
                    <div class="mt-6 rounded-2xl border border-dashed border-slate-200 px-4 py-4 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                        Save these values before running `php artisan system:prepare-hosting` on the server. Public URLs, invoices, help pages, and support widgets will use these settings automatically.
                    </div>
                </div>
            @endif

            @if($activeTab === 'api_keys')
                <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-6 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                    <div class="mb-6"><h3 class="text-xl font-bold text-slate-900 dark:text-white">API credentials vault</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Keep core platform keys in one place so integrations are easier to identify and rotate.</p></div>
                    <div class="grid gap-4 lg:grid-cols-2">
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Mail API Key</label><input type="password" wire:model="mail_api_key" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Mail API Secret</label><input type="password" wire:model="mail_api_secret" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">WhatsApp API Key / Token</label><input type="password" wire:model="whatsapp_api_key" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">AI API Key</label><input type="password" wire:model="ai_api_key" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div class="lg:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Custom Integration API Key</label><input type="password" wire:model="custom_integrations_api_key" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                    </div>
                    <p class="mt-4 text-xs leading-6 text-slate-500 dark:text-slate-400">Secret fields are stored encrypted in site settings. Use this tab for tokens and keys, and use `Hosting & Identity` for public URLs and customer-facing contact data.</p>
                </div>
            @endif

            @if($activeTab === 'whatsapp')
                <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-6 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                    <div class="mb-6"><h3 class="text-xl font-bold text-slate-900 dark:text-white">WhatsApp automation</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Store provider details and message templates for automated order and payment updates.</p></div>
                    <div class="grid gap-4 lg:grid-cols-2">
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 lg:col-span-2 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"><input type="checkbox" wire:model="whatsapp_enabled" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Enable WhatsApp automated messaging</label>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Provider</label><select wire:model="whatsapp_provider" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"><option value="meta_cloud">Meta Cloud API</option><option value="twilio">Twilio</option><option value="custom">Custom Provider</option></select></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Business Phone Number</label><input type="text" wire:model="whatsapp_phone_number" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div class="lg:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">API URL / Endpoint</label><input type="text" wire:model="whatsapp_api_url" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div class="lg:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">API Key / Token</label><input type="password" wire:model="whatsapp_api_key" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div class="lg:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Webhook Verify Token</label><input type="text" wire:model="whatsapp_webhook_verify_token" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div class="lg:col-span-2 rounded-2xl border border-dashed border-slate-200 px-4 py-4 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                            <p class="font-semibold text-slate-800 dark:text-white">Meta webhook setup</p>
                            <p class="mt-2">Callback URL: <span class="font-mono text-violet-600">{{ rtrim($app_public_url ?: config('app.url'), '/') }}/whatsapp/webhook</span></p>
                            <p class="mt-2">Paste this callback URL into Meta Configuration. Use the same verify token here and there, then subscribe to WhatsApp message status events.</p>
                        </div>
                        <div class="lg:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Order Template</label><textarea wire:model="whatsapp_order_template" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></textarea></div>
                        <div class="lg:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Payment Template</label><textarea wire:model="whatsapp_payment_template" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></textarea></div>
                    </div>
                </div>
            @endif

            @if($activeTab === 'ai')
                <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-6 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                    <div class="mb-6"><h3 class="text-xl font-bold text-slate-900 dark:text-white">AI operations</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Control the admin AI assistant, select the model, and define which business areas it should help manage.</p></div>
                    <div class="mb-6 grid gap-4 lg:grid-cols-3">
                        @foreach([['title' => 'Fast Everyday', 'model' => 'gpt-4o-mini', 'desc' => 'Quick summaries and routine admin help.'], ['title' => 'Balanced', 'model' => 'gpt-4.1-mini', 'desc' => 'Stronger day-to-day analysis.'], ['title' => 'Advanced', 'model' => 'gpt-5', 'desc' => 'Use if your account supports a newer flagship model.']] as $preset)
                            <button type="button" wire:click="$set('ai_model', '{{ $preset['model'] }}')" class="rounded-2xl border px-4 py-4 text-left transition {{ $ai_model === $preset['model'] ? 'border-slate-900 bg-slate-900 text-white shadow-lg shadow-slate-900/10 dark:border-white dark:bg-white dark:text-slate-900' : 'border-slate-200 bg-slate-50 text-slate-700 hover:border-slate-300 hover:bg-white dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                                <p class="text-sm font-semibold">{{ $preset['title'] }}</p><p class="mt-1 text-xs font-mono {{ $ai_model === $preset['model'] ? 'text-white/70 dark:text-slate-500' : 'text-violet-600' }}">{{ $preset['model'] }}</p><p class="mt-3 text-sm leading-6 {{ $ai_model === $preset['model'] ? 'text-white/75 dark:text-slate-500' : 'text-slate-500 dark:text-slate-400' }}">{{ $preset['desc'] }}</p>
                            </button>
                        @endforeach
                    </div>
                    <div class="grid gap-4 lg:grid-cols-2">
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 lg:col-span-2 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"><input type="checkbox" wire:model="ai_enabled" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Enable AI assistant inside admin</label>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">AI Provider</label><select wire:model="ai_provider" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"><option value="openai">OpenAI</option><option value="custom">Custom API</option></select></div>
                        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Model</label><input type="text" wire:model="ai_model" list="ai-model-options" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"><datalist id="ai-model-options"><option value="gpt-4o-mini"></option><option value="gpt-4.1-mini"></option><option value="gpt-4.1"></option><option value="gpt-4o"></option><option value="gpt-5"></option><option value="gpt-5-mini"></option></datalist></div>
                        <div class="lg:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">API Key</label><input type="password" wire:model="ai_api_key" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"><input type="checkbox" wire:model="ai_sales_tracking_enabled" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Enable AI sales tracking guidance</label>
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"><input type="checkbox" wire:model="ai_inventory_guidance_enabled" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Enable AI inventory guidance</label>
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 lg:col-span-2 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"><input type="checkbox" wire:model="ai_management_guidance_enabled" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Enable AI management and operations recommendations</label>
                        <div class="lg:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">AI Mission</label><input type="text" wire:model="ai_goal_text" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div class="lg:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Assistant Context Prompt</label><textarea wire:model="ai_prompt_context" rows="5" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></textarea></div>
                    </div>
                </div>
            @endif

            @if($activeTab === 'access')
                <div class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-6 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
                    <div class="mb-6"><h3 class="text-xl font-bold text-slate-900 dark:text-white">Permission-based access guide</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">The admin area now blocks modules based on granted permissions instead of only relying on one admin role.</p></div>
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
                </div>
            @endif
        </div>
    </div>
</div>
