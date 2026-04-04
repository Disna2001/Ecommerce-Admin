<x-admin.ui.panel padding="p-6">
    <x-slot:header>
        <div class="mb-6">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">API credentials vault</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Keep core platform keys in one place so integrations are easier to identify and rotate.</p>
        </div>
    </x-slot:header>

    <div class="grid gap-4 lg:grid-cols-2">
        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Mail API Key</label><input type="password" wire:model="mail_api_key" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Mail API Secret</label><input type="password" wire:model="mail_api_secret" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">WhatsApp API Key / Token</label><input type="password" wire:model="whatsapp_api_key" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">AI API Key</label><input type="password" wire:model="ai_api_key" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div class="lg:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Custom Integration API Key</label><input type="password" wire:model="custom_integrations_api_key" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
    </div>

    <p class="mt-4 text-xs leading-6 text-slate-500 dark:text-slate-400">Secret fields are stored encrypted in site settings. Use this tab for tokens and keys, and use `Hosting & Identity` for public URLs and customer-facing contact data.</p>
</x-admin.ui.panel>
