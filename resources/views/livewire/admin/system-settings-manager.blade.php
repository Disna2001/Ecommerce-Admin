<div class="space-y-6">
    <!-- Top Action Strip (Description & Global Save Scope) -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pb-2 border-b border-slate-200">
        <p class="text-xs text-slate-500 font-medium">Configure core store parameters, communications, integrations, and maintenance.</p>
        <div class="flex items-center gap-3 self-start sm:self-auto">
            <span class="text-[11px] font-medium text-slate-400 hidden sm:inline">Applies across all categories</span>
            <button wire:click="save" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-800 shadow-xs">
                <i class="fas fa-save text-xs"></i>
                <span>Save Settings</span>
            </button>
        </div>
    </div>

    @if($saved)
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">
            <i class="fas fa-check-circle mr-1.5"></i> All system settings saved successfully.
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-semibold text-rose-700">
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-rose-500"></i> Validation failed. Please review the highlighted fields below:
            </div>
            <ul class="list-disc list-inside pl-5 mt-1.5 text-xs text-rose-600 space-y-0.5 font-normal">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid min-w-0 gap-6 xl:grid-cols-[270px_minmax(0,1fr)]">
        <!-- Sidebar Navigation Rail (3 Grouped Categories) -->
        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs">
                <nav class="flex flex-col gap-5 text-xs">
                    
                    <!-- Group 1: Store Setup -->
                    <div class="space-y-1.5">
                        <div class="px-2">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-900">Store Setup</p>
                            <p class="text-[10px] font-medium text-slate-400">Domain, currency, and billing</p>
                        </div>
                        <div class="space-y-1 font-semibold">
                            <button type="button" wire:click="$set('activeTab', 'hosting')" class="w-full group flex items-center justify-between rounded-lg px-3 py-2 transition-all {{ $activeTab === 'hosting' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-md {{ $activeTab === 'hosting' ? 'bg-white/20' : 'bg-slate-100 text-slate-500' }}"><i class="fas fa-server text-[10px]"></i></div>
                                    <span>Core Systems</span>
                                </div>
                                <i class="fas fa-chevron-right text-[10px] opacity-40"></i>
                            </button>

                            <button type="button" wire:click="$set('activeTab', 'billing')" class="w-full group flex items-center justify-between rounded-lg px-3 py-2 transition-all {{ $activeTab === 'billing' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-md {{ $activeTab === 'billing' ? 'bg-white/20' : 'bg-slate-100 text-slate-500' }}"><i class="fas fa-receipt text-[10px]"></i></div>
                                    <span>Commerce & Print</span>
                                </div>
                                <i class="fas fa-chevron-right text-[10px] opacity-40"></i>
                            </button>
                        </div>
                    </div>

                    <div class="border-t border-slate-100"></div>

                    <!-- Group 2: Communications -->
                    <div class="space-y-1.5">
                        <div class="px-2">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-900">Communications</p>
                            <p class="text-[10px] font-medium text-slate-400">Email dispatch and WhatsApp</p>
                        </div>
                        <div class="space-y-1 font-semibold">
                            <button type="button" wire:click="$set('activeTab', 'communications')" class="w-full group flex items-center justify-between rounded-lg px-3 py-2 transition-all {{ $activeTab === 'communications' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-md {{ $activeTab === 'communications' ? 'bg-white/20' : 'bg-slate-100 text-slate-500' }}"><i class="fas fa-envelope-open-text text-[10px]"></i></div>
                                    <span>Communications (Mail)</span>
                                </div>
                                <i class="fas fa-chevron-right text-[10px] opacity-40"></i>
                            </button>

                            <button type="button" wire:click="$set('activeTab', 'whatsapp')" class="w-full group flex items-center justify-between rounded-lg px-3 py-2 transition-all {{ $activeTab === 'whatsapp' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-md {{ $activeTab === 'whatsapp' ? 'bg-white/20' : 'bg-slate-100 text-slate-500' }}"><i class="fas fa-comment-dots text-[10px]"></i></div>
                                    <span>WhatsApp Meta</span>
                                </div>
                                <i class="fas fa-chevron-right text-[10px] opacity-40"></i>
                            </button>
                        </div>
                    </div>

                    <div class="border-t border-slate-100"></div>

                    <!-- Group 3: Integrations & Advanced -->
                    <div class="space-y-1.5">
                        <div class="px-2">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-900">Integrations & Advanced</p>
                            <p class="text-[10px] font-medium text-slate-400">AI, API keys, and backups</p>
                        </div>
                        <div class="space-y-1 font-semibold">
                            <button type="button" wire:click="$set('activeTab', 'ai')" class="w-full group flex items-center justify-between rounded-lg px-3 py-2 transition-all {{ $activeTab === 'ai' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-md {{ $activeTab === 'ai' ? 'bg-white/20' : 'bg-slate-100 text-slate-500' }}"><i class="fas fa-brain text-[10px]"></i></div>
                                    <span>AI Assistant</span>
                                </div>
                                <i class="fas fa-chevron-right text-[10px] opacity-40"></i>
                            </button>

                            <button type="button" wire:click="$set('activeTab', 'api_keys')" class="w-full group flex items-center justify-between rounded-lg px-3 py-2 transition-all {{ $activeTab === 'api_keys' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-md {{ $activeTab === 'api_keys' ? 'bg-white/20' : 'bg-slate-100 text-slate-500' }}"><i class="fas fa-key text-[10px]"></i></div>
                                    <span>API Credentials</span>
                                </div>
                                <i class="fas fa-chevron-right text-[10px] opacity-40"></i>
                            </button>

                            <button type="button" wire:click="$set('activeTab', 'maintenance')" class="w-full group flex items-center justify-between rounded-lg px-3 py-2 transition-all {{ $activeTab === 'maintenance' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-md {{ $activeTab === 'maintenance' ? 'bg-white/20' : 'bg-slate-100 text-slate-500' }}"><i class="fas fa-shield-halved text-[10px]"></i></div>
                                    <span>Maintenance & Backup</span>
                                </div>
                                <i class="fas fa-chevron-right text-[10px] opacity-40"></i>
                            </button>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-2"></div>
                    <a href="{{ route('admin.users') }}" class="group flex items-center justify-between rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold transition-all">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-6 w-6 items-center justify-center rounded-md bg-slate-100 text-slate-500"><i class="fas fa-user-shield text-[10px]"></i></div>
                            <span>Access Control</span>
                        </div>
                        <i class="fas fa-external-link-alt text-[9px] opacity-40"></i>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Workspace Content -->
        <div class="min-w-0">
            <div class="min-h-[500px]">
                @if($activeTab === 'communications')
                    <x-admin.settings.tab-communications 
                        :mail_mailer="$mail_mailer"
                        :mail_from_name="$mail_from_name"
                        :mail_from_address="$mail_from_address"
                        :order_notification_email="$order_notification_email"
                        :support_notification_email="$support_notification_email"
                        :mail_smtp_host="$mail_smtp_host"
                        :mail_smtp_port="$mail_smtp_port"
                        :mail_smtp_encryption="$mail_smtp_encryption"
                        :mail_smtp_username="$mail_smtp_username"
                        :mail_smtp_password="$mail_smtp_password"
                        :test_email_recipient="$test_email_recipient"
                        :onesignal_enabled="$onesignal_enabled"
                        :onesignal_app_id="$onesignal_app_id"
                        :onesignal_rest_api_key="$onesignal_rest_api_key"
                    />
                @elseif($activeTab === 'hosting')
                    <x-admin.settings.tab-hosting />
                @elseif($activeTab === 'billing')
                    <x-admin.settings.tab-billing
                        :billing-profiles="$billing_profiles"
                        :billing-default-profiles="$billing_default_profiles"
                        :billing-preview-company="$billingPreviewCompany"
                        :billing-preview-documents="$billingPreviewDocuments"
                        :printer-catalog="$printerCatalog"
                        :currency="$currency_symbol"
                    />
                @elseif($activeTab === 'api_keys')
                    <x-admin.settings.tab-api-keys />
                @elseif($activeTab === 'whatsapp')
                    <x-admin.settings.tab-whatsapp 
                        :app_public_url="$app_public_url" 
                        :whatsapp_enabled="$whatsapp_enabled" 
                        :whatsapp_provider="$whatsapp_provider" 
                        :whatsapp_chat_enabled="$whatsapp_chat_enabled" 
                        :whatsapp_bot_enabled="$whatsapp_bot_enabled"
                        :test-bot-messages="$testBotMessages"
                        :test-bot-input="$testBotInput"
                        :bridge-state="$bridgeState"
                        :bridge-phone="$bridgePhone"
                        :bridge-qr-image="$bridgeQrImage"
                        :bridge-pairing="$bridgePairing"
                        :show-disconnect-confirm="$showDisconnectConfirm"
                    />
                @elseif($activeTab === 'ai')
                    <x-admin.settings.tab-ai :ai-model="$ai_model" />
                @elseif($activeTab === 'maintenance')
                    <x-admin.settings.tab-maintenance :backup-file="$backupFile" :maintenance-mode="$maintenance_mode" />
                @endif
            </div>
        </div>
    </div>

    <div wire:loading class="fixed bottom-6 right-6 z-50">
        <div class="flex items-center gap-2 rounded-lg bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white shadow-lg">
            <div class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/20 border-t-white"></div>
            <span>Saving Settings...</span>
        </div>
    </div>
</div>
