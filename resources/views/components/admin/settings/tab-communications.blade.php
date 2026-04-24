<x-admin.ui.panel padding="p-6">
    <x-slot:header>
        <div class="mb-6">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Email sending and alert routing</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Choose the email identity and transport the system should use for order, support, and operational notifications.</p>
        </div>
    </x-slot:header>

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
</x-admin.ui.panel>
