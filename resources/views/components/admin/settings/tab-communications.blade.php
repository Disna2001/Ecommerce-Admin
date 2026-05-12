@props(['mail_mailer' => 'smtp', 'mail_from_name' => '', 'mail_from_address' => '', 'order_notification_email' => '', 'support_notification_email' => '', 'mail_smtp_host' => '', 'mail_smtp_port' => '', 'mail_smtp_encryption' => '', 'mail_smtp_username' => '', 'mail_smtp_password' => '', 'test_email_recipient' => ''])

<div class="space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 shadow-inner"><i class="fas fa-envelope-open-text text-lg"></i></div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Communications Bridge</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Email Transport & Alert Routing</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="space-y-6 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <p class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Mail Identity</p>
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Transport Protocol</label>
                    <select wire:model.live="mail_mailer" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-indigo-500 focus:ring-0 transition-all">
                        <option value="smtp">SMTP (Legacy)</option>
                        <option value="resend">Resend (Recommended for Hosting)</option>
                        <option value="mailgun">Mailgun API</option>
                        <option value="ses">Amazon SES API</option>
                        <option value="log">Log (Testing only)</option>
                    </select>
                </div>

                @if($mail_mailer !== 'smtp' && $mail_mailer !== 'log')
                    <div class="space-y-1.5 animate-in fade-in slide-in-from-top-2">
                        <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">API Key / Secret</label>
                        <div class="relative" x-data="{ show: false }">
                            <input :type="show ? 'text' : 'password'" wire:model="mail_api_key" placeholder="Enter your {{ ucfirst($mail_mailer) }} API Key" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 pr-12 text-sm font-bold shadow-inner focus:bg-white focus:border-indigo-500 focus:ring-0 transition-all">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-4 text-slate-400 hover:text-indigo-600 transition-colors">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                @endif
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Sender Display Name</label>
                        <input type="text" wire:model="mail_from_name" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-indigo-500 focus:ring-0 transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Sender Address</label>
                        <input type="email" wire:model="mail_from_address" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-indigo-500 focus:ring-0 transition-all">
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <p class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Alert Routing</p>
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Commerce Notifications</label>
                    <input type="email" wire:model="order_notification_email" placeholder="orders@yourcompany.com" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-0 transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Support Alerts</label>
                    <input type="email" wire:model="support_notification_email" placeholder="support-alerts@yourcompany.com" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-sky-500 focus:ring-0 transition-all">
                </div>
                <p class="text-[10px] font-medium text-slate-400 leading-relaxed italic">Internal system alerts will be dispatched to these addresses immediately upon triggering.</p>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6 rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
            <p class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">SMTP Gateway Authentication</p>
            <div class="grid gap-6 md:grid-cols-3">
                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Hostname</label>
                        <input type="text" wire:model="mail_smtp_host" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-indigo-500 focus:ring-0 transition-all">
                    </div>
                    <div class="grid gap-4 grid-cols-2">
                        <div class="space-y-1.5">
                            <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Port</label>
                            <input type="text" wire:model="mail_smtp_port" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-indigo-500 focus:ring-0 transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Security</label>
                            <input type="text" wire:model="mail_smtp_encryption" placeholder="tls" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-indigo-500 focus:ring-0 transition-all">
                        </div>
                    </div>
                </div>
                <div class="space-y-4 md:col-span-2">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Username / API Key</label>
                            <input type="text" wire:model="mail_smtp_username" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-indigo-500 focus:ring-0 transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Password / Secret</label>
                            <div class="relative" x-data="{ show: false }">
                                <input :type="show ? 'text' : 'password'" wire:model="mail_smtp_password" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 pr-12 text-sm font-bold shadow-inner focus:bg-white focus:border-indigo-500 focus:ring-0 transition-all">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-4 text-slate-400 hover:text-indigo-600 transition-colors">
                                    <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl bg-slate-900/5 p-4 border border-slate-100">
                         <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Security Advisory</p>
                         <p class="text-[10px] leading-relaxed text-slate-400">Ensure your SMTP credentials have restricted scopes. For SES or Mailgun, use IAM policies that only allow 'SendRawEmail' actions.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-[2.5rem] bg-indigo-600 p-8 text-white shadow-2xl shadow-indigo-100 overflow-hidden relative group">
        <div class="absolute right-0 top-0 -mr-12 -mt-12 h-48 w-48 rounded-full bg-white/10 transition-transform group-hover:scale-150"></div>
        <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="space-y-1">
                <h4 class="text-xl font-black">Live Connectivity Test</h4>
                <p class="text-xs font-medium text-indigo-100">Validate your bridge configuration with a real-time dispatch test.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <input type="email" wire:model="test_email_recipient" placeholder="Enter recipient email..." class="min-w-[280px] rounded-2xl border-transparent bg-white/10 px-6 py-4 text-sm font-bold text-white placeholder-white/40 shadow-inner backdrop-blur-md focus:bg-white focus:text-indigo-900 focus:ring-0 transition-all">
                <button wire:click="sendTestEmail" type="button" class="group flex items-center justify-center gap-3 rounded-2xl bg-white px-8 py-4 text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] shadow-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <span wire:loading.remove wire:target="sendTestEmail">
                        <i class="fas fa-paper-plane text-xs opacity-50 group-hover:opacity-100 transition-opacity"></i>
                        Initiate Test
                    </span>
                    <span wire:loading wire:target="sendTestEmail">
                        <i class="fas fa-spinner fa-spin text-xs"></i>
                        Dispatching...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
