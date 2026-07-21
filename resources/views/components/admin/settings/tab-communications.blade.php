@props(['mail_mailer' => 'smtp', 'mail_from_name' => '', 'mail_from_address' => '', 'order_notification_email' => '', 'support_notification_email' => '', 'mail_smtp_host' => '', 'mail_smtp_port' => '', 'mail_smtp_encryption' => '', 'mail_smtp_username' => '', 'mail_smtp_password' => '', 'test_email_recipient' => '', 'onesignal_enabled' => false, 'onesignal_app_id' => '', 'onesignal_rest_api_key' => ''])

@php
    $selectedClasses = [
        'resend' => 'border-indigo-500 bg-indigo-50/50 ring-2 ring-indigo-500/20',
        'smtp' => 'border-slate-800 bg-slate-50/50 ring-2 ring-slate-800/20',
        'sendgrid' => 'border-sky-500 bg-sky-50/50 ring-2 ring-sky-500/20',
        'mailgun' => 'border-rose-500 bg-rose-50/50 ring-2 ring-rose-500/20',
        'brevo' => 'border-amber-500 bg-amber-50/50 ring-2 ring-amber-500/20',
    ];
@endphp

<div class="space-y-6">
    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
            <i class="fas fa-envelope-open-text text-sm"></i>
        </div>
        <div>
            <h3 class="text-base font-bold text-slate-900">Communications</h3>
            <p class="text-xs text-slate-500">Configure email dispatch, SMTP settings, notification recipients, and push alerts.</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3 text-xs font-semibold">
        <!-- Sender Details -->
        <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h4 class="font-bold text-slate-900 text-sm">Sender Details</h4>
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full {{ $mail_mailer !== 'log' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $mail_mailer === 'log' ? 'Offline' : 'Active' }}</span>
                </div>
            </div>
            
            <div class="grid gap-3 grid-cols-2 sm:grid-cols-3 xl:grid-cols-5">
                @foreach([
                    'resend' => ['Resend', 'fa-bolt'],
                    'smtp' => ['Standard SMTP', 'fa-server'],
                    'sendgrid' => ['SendGrid', 'fa-paper-plane'],
                    'mailgun' => ['Mailgun API', 'fa-fire'],
                    'brevo' => ['Brevo SMTP', 'fa-envelope'],
                ] as $key => [$name, $icon])
                    <button type="button" wire:click="$set('mail_mailer', '{{ $key }}')" 
                        class="group relative flex flex-col gap-2 rounded-lg border-2 p-3 transition-all text-left {{ $mail_mailer === $key ? $selectedClasses[$key] : 'border-slate-100 bg-slate-50/50 hover:border-slate-200 hover:bg-white' }}">
                        <div class="flex h-7 w-7 items-center justify-center rounded-md {{ $mail_mailer === $key ? 'bg-slate-900 text-white' : 'bg-white text-slate-400' }} shadow-xs">
                            <i class="fas {{ $icon }} text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold {{ $mail_mailer === $key ? 'text-slate-900' : 'text-slate-600' }}">{{ $name }}</p>
                        </div>
                    </button>
                @endforeach
            </div>

            <div class="grid gap-4 mt-4 {{ ($mail_mailer !== 'smtp' && $mail_mailer !== 'log') ? 'grid-cols-1 sm:grid-cols-3' : 'grid-cols-1 sm:grid-cols-2' }}">
                @if($mail_mailer !== 'smtp' && $mail_mailer !== 'log')
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">API Key / Secret</label>
                        <div class="relative" x-data="{ show: false }">
                            <input :type="show ? 'text' : 'password'" wire:model="mail_api_key" placeholder="Enter API Key" class="w-full rounded-lg border-slate-200 px-3 py-2 pr-9 font-semibold text-slate-900 focus:ring-0">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-600">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                @endif

                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Sender Display Name</label>
                    <input type="text" wire:model="mail_from_name" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Sender Address</label>
                    <input type="email" wire:model="mail_from_address" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                </div>
            </div>
        </div>

        <!-- Notification Emails -->
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <h4 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">Notification Emails</h4>
            <div class="space-y-3">
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Order Notifications</label>
                    <input type="email" wire:model="order_notification_email" placeholder="orders@yourcompany.com" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                </div>
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Support Alerts</label>
                    <input type="email" wire:model="support_notification_email" placeholder="support@yourcompany.com" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                </div>
                <p class="text-[11px] font-medium text-slate-500 leading-relaxed">Internal system alerts and order notifications will be sent to these addresses.</p>
            </div>
        </div>

        <!-- Push Notifications -->
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h4 class="font-bold text-slate-900 text-sm">Push Notifications</h4>
                <button type="button" wire:click="$set('onesignal_enabled', {{ !$onesignal_enabled ? 'true' : 'false' }})" 
                    class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $onesignal_enabled ? 'bg-indigo-600' : 'bg-slate-200' }}">
                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $onesignal_enabled ? 'translate-x-4' : 'translate-x-0' }}"></span>
                </button>
            </div>

            <div class="space-y-3">
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">OneSignal App ID</label>
                    <input type="text" wire:model="onesignal_app_id" placeholder="App ID..." 
                        class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0"
                        {{ !$onesignal_enabled ? 'disabled' : '' }}>
                </div>
                
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">REST API Key</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" wire:model="onesignal_rest_api_key" placeholder="API Key..." 
                            class="w-full rounded-lg border-slate-200 px-3 py-2 pr-9 font-semibold text-slate-900 focus:ring-0"
                            {{ !$onesignal_enabled ? 'disabled' : '' }}>
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-600"
                            {{ !$onesignal_enabled ? 'disabled' : '' }}>
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <p class="text-[11px] font-medium text-slate-500 leading-relaxed">Sends real-time push notifications to registered devices for order updates.</p>
            </div>
        </div>

        @if($mail_mailer === 'smtp')
        <!-- SMTP Settings -->
        <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <h4 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">SMTP Settings</h4>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="space-y-3">
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Hostname</label>
                        <input type="text" wire:model="mail_smtp_host" placeholder="smtp.mailtrap.io" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                    </div>
                    <div class="grid gap-2 grid-cols-2">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">Port</label>
                            <input type="text" wire:model="mail_smtp_port" placeholder="587" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                        </div>
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">Encryption</label>
                            <input type="text" wire:model="mail_smtp_encryption" placeholder="tls" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                        </div>
                    </div>
                </div>
                <div class="space-y-3 sm:col-span-2">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">Username</label>
                            <input type="text" wire:model="mail_smtp_username" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                        </div>
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">Password</label>
                            <div class="relative" x-data="{ show: false }">
                                <input :type="show ? 'text' : 'password'" wire:model="mail_smtp_password" class="w-full rounded-lg border-slate-200 px-3 py-2 pr-9 font-semibold text-slate-900 focus:ring-0">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-600">
                                    <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3 border border-slate-100 text-slate-600 text-[11px] font-medium leading-relaxed">
                        Ensure your SMTP credentials have standard sending privileges. For AWS SES or Mailgun, use dedicated IAM API keys.
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Live Test Email -->
    <div class="rounded-xl bg-slate-900 p-6 text-white shadow-xs space-y-3 text-xs">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h4 class="text-sm font-bold">Send Test Email</h4>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Send a test email to verify your mail server configuration.</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <input type="email" wire:model="test_email_recipient" placeholder="Recipient email..." class="rounded-lg border-transparent bg-white/10 px-3 py-2 text-xs font-semibold text-white placeholder-white/40 focus:ring-0">
                <button wire:click="sendTestEmail" type="button" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors shadow-xs">
                    <span wire:loading.remove wire:target="sendTestEmail">
                        <i class="fas fa-paper-plane text-xs"></i>
                        Send Test Email
                    </span>
                    <span wire:loading wire:target="sendTestEmail">
                        <i class="fas fa-spinner fa-spin text-xs"></i>
                        Sending...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
