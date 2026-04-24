<x-admin.ui.panel padding="p-6">
    <x-slot:header>
        <div class="mb-6">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">API credentials vault</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Keep core platform keys in one place so integrations are easier to identify and rotate.</p>
        </div>
    </x-slot:header>

    <div class="grid gap-4 lg:grid-cols-2">
        <div class="lg:col-span-2 grid gap-3 md:grid-cols-3">
            <a href="https://platform.openai.com/api-keys" target="_blank" rel="noopener" class="rounded-2xl border border-violet-200 bg-violet-50 px-4 py-4 text-sm font-semibold text-violet-800 transition hover:border-violet-300 hover:bg-violet-100 dark:border-violet-500/20 dark:bg-violet-500/10 dark:text-violet-200">
                <i class="fas fa-key mr-2"></i>OpenAI API keys
                <span class="mt-1 block text-xs font-normal leading-5">Create a key for the admin AI assistant.</span>
            </a>
            <a href="https://app.resend.com/api-keys" target="_blank" rel="noopener" class="rounded-2xl border border-slate-200 bg-white px-4 py-4 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                <i class="fas fa-envelope mr-2"></i>Resend mail keys
                <span class="mt-1 block text-xs font-normal leading-5">Use if the client sends mail through Resend.</span>
            </a>
            <a href="https://app.mailgun.com/app/sending/domains" target="_blank" rel="noopener" class="rounded-2xl border border-slate-200 bg-white px-4 py-4 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                <i class="fas fa-paper-plane mr-2"></i>Mailgun domains
                <span class="mt-1 block text-xs font-normal leading-5">Get mail API credentials and verify sending domains.</span>
            </a>
        </div>

        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Mail API Key</label><input type="password" wire:model="mail_api_key" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Mail API Secret</label><input type="password" wire:model="mail_api_secret" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">WhatsApp API Key / Token</label><input type="password" wire:model="whatsapp_api_key" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">AI API Key</label><input type="password" wire:model="ai_api_key" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div class="lg:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Custom Integration API Key</label><input type="password" wire:model="custom_integrations_api_key" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
    </div>

    <p class="mt-4 text-xs leading-6 text-slate-500 dark:text-slate-400">Secret fields are stored encrypted in site settings. Use this tab for tokens and keys, and use `Hosting & Identity` for public URLs and customer-facing contact data.</p>
</x-admin.ui.panel>
