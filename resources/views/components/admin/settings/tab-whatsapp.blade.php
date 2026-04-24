<x-admin.ui.panel padding="p-6">
    <x-slot:header>
        <div class="mb-6">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">WhatsApp automation</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Store provider details and message templates for automated order and payment updates.</p>
        </div>
    </x-slot:header>

    <div class="grid gap-4 lg:grid-cols-2">
        <div class="lg:col-span-2 grid gap-3 md:grid-cols-2">
            <a href="https://developers.facebook.com/docs/whatsapp/cloud-api/get-started" target="_blank" rel="noopener" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm font-semibold text-emerald-800 transition hover:border-emerald-300 hover:bg-emerald-100 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-200">
                <i class="fab fa-whatsapp mr-2"></i>WhatsApp Cloud API guide
                <span class="mt-1 block text-xs font-normal leading-5">Create the app, phone number, token, and webhook subscription.</span>
            </a>
            <a href="https://business.facebook.com/wa/manage/" target="_blank" rel="noopener" class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-4 text-sm font-semibold text-sky-800 transition hover:border-sky-300 hover:bg-sky-100 dark:border-sky-500/20 dark:bg-sky-500/10 dark:text-sky-200">
                <i class="fas fa-briefcase mr-2"></i>WhatsApp Manager
                <span class="mt-1 block text-xs font-normal leading-5">Manage business phone numbers and message templates.</span>
            </a>
        </div>

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
</x-admin.ui.panel>
