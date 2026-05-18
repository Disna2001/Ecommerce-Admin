<div class="space-y-6">
    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm overflow-hidden relative">
        <div class="absolute right-0 top-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-slate-50 opacity-50"></div>
        <div class="relative z-10 flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">System Infrastructure</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900">Control Center</h1>
                <p class="mt-2 max-w-xl text-sm font-medium leading-relaxed text-slate-500">Orchestrate your commerce ecosystem, configure intelligence protocols, and manage global service integrations from a unified bridge.</p>
            </div>
            <div class="flex items-center gap-3">
                 <button wire:click="save" class="group relative flex items-center gap-3 rounded-2xl bg-slate-900 px-8 py-4 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-2xl shadow-slate-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fas fa-save text-[10px] opacity-50 group-hover:opacity-100 transition-opacity"></i>
                    Deploy Configurations
                </button>
            </div>
        </div>
    </div>

    @if($saved)
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 animate-in fade-in slide-in-from-top-2">
            <i class="fas fa-check-circle mr-2"></i> System parameters have been successfully propagated.
        </div>
    @endif

    <div class="grid min-w-0 gap-6 xl:grid-cols-[300px_minmax(0,1fr)]">
        <!-- Sidebar Navigation -->
        <div class="space-y-6">
            <div class="rounded-[2.5rem] border border-slate-200 bg-white p-6 shadow-sm">
                <nav class="flex flex-col gap-1.5">
                    <p class="mb-4 px-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Environment</p>
                    
                    @foreach([
                        'hosting' => ['Core Systems', 'fa-server', 'sky'],
                        'communications' => ['Communications', 'fa-envelope-open-text', 'indigo'],
                        'billing' => ['Commerce & Print', 'fa-receipt', 'emerald'],
                        'whatsapp' => ['WhatsApp Meta', 'fa-comment-dots', 'amber'],
                        'ai' => ['Intelligence', 'fa-brain', 'rose'],
                        'maintenance' => ['Maintenance & Recovery', 'fa-shield-halved', 'rose'],
                        'api_keys' => ['API Credentials', 'fa-key', 'slate'],
                    ] as $tab => [$label, $icon, $color])
                        <button type="button" wire:click="$set('activeTab', '{{ $tab }}')" class="group flex items-center justify-between rounded-2xl px-4 py-4 transition-all {{ $activeTab === $tab ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <div class="flex items-center gap-4">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl {{ $activeTab === $tab ? 'bg-white/20' : 'bg-slate-100 text-slate-400 group-hover:bg-white group-hover:text-'.$color.'-500' }} transition-colors"><i class="fas {{ $icon }} text-xs"></i></div>
                                <span class="text-sm font-bold">{{ $label }}</span>
                            </div>
                            <i class="fas fa-chevron-right text-[10px] opacity-20"></i>
                        </button>
                    @endforeach

                    <div class="my-6 border-t border-slate-100"></div>
                    <p class="mb-4 px-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Security</p>
                    
                    <a href="{{ route('admin.users') }}" class="group flex items-center justify-between rounded-2xl px-4 py-4 text-slate-600 transition-all hover:bg-slate-50 hover:text-slate-900">
                        <div class="flex items-center gap-4">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-400 group-hover:bg-white transition-colors"><i class="fas fa-user-shield text-xs"></i></div>
                            <span class="text-sm font-bold">Access Control</span>
                        </div>
                        <i class="fas fa-external-link-alt text-[10px] opacity-20"></i>
                    </a>
                </nav>
            </div>

            @if($activeTab === 'hosting')
                 <div class="rounded-[2rem] border border-slate-200 bg-slate-900 p-8 text-white shadow-2xl">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40">System Status</p>
                    <div class="mt-6 flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/20 text-emerald-400"><i class="fas fa-check-double"></i></div>
                        <div>
                            <p class="text-xs font-bold opacity-60">Engine Performance</p>
                            <p class="text-xl font-black">Optimized</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Workspace Content -->
        <div class="min-w-0">
            <div class="rounded-[2.5rem] border border-slate-200 bg-white p-2 shadow-sm">
                <div class="rounded-[2rem] bg-slate-50/50 p-8 min-h-[600px]">
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
                        <x-admin.settings.tab-whatsapp :app-public-url="$app_public_url" />
                    @elseif($activeTab === 'ai')
                        <x-admin.settings.tab-ai :ai-model="$ai_model" />
                    @elseif($activeTab === 'maintenance')
                        <x-admin.settings.tab-maintenance :backup-file="$backupFile" :maintenance-mode="$maintenance_mode" />
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div wire:loading class="fixed bottom-8 right-8 z-[60]">
        <div class="flex items-center gap-3 rounded-2xl bg-slate-900 px-5 py-3 text-white shadow-2xl shadow-slate-400">
            <div class="h-4 w-4 animate-spin rounded-full border-2 border-white/20 border-t-white"></div>
            <span class="text-[10px] font-black uppercase tracking-widest">Applying Changes...</span>
        </div>
    </div>
</div>
