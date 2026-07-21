<div class="space-y-6">
    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-900 text-white">
            <i class="fas fa-key text-sm"></i>
        </div>
        <div>
            <h3 class="text-base font-bold text-slate-900">API Credentials</h3>
            <p class="text-xs text-slate-500">Manage secret keys for mailers, WhatsApp, AI, and external APIs.</p>
        </div>
    </div>

    <!-- External Developer Portals -->
    <div class="grid gap-3 sm:grid-cols-3 text-xs font-semibold">
        @foreach([
            ['label' => 'OpenAI Platform', 'url' => 'https://platform.openai.com/api-keys', 'icon' => 'fa-robot'],
            ['label' => 'Resend Mail', 'url' => 'https://app.resend.com/api-keys', 'icon' => 'fa-paper-plane'],
            ['label' => 'Mailgun Domains', 'url' => 'https://app.mailgun.com/app/sending/domains', 'icon' => 'fa-envelope']
        ] as $link)
            <a href="{{ $link['url'] }}" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-3.5 hover:border-slate-400 hover:shadow-xs transition-all">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-600"><i class="fas {{ $link['icon'] }} text-xs"></i></div>
                <div>
                    <p class="font-bold text-slate-900">{{ $link['label'] }}</p>
                    <p class="text-[10px] font-normal text-slate-400">External Console</p>
                </div>
            </a>
        @endforeach
    </div>

    <!-- API Credentials Secret Inputs -->
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4 text-xs font-semibold">
        <h4 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">API Credentials</h4>
        
        <div class="grid gap-4 md:grid-cols-2">
            @foreach([
                'mail_api_key' => ['Mail Gateway Key', 'Primary transport token for email dispatch.'],
                'mail_api_secret' => ['Mail Gateway Secret', 'Secondary secret token for mail driver.'],
                'whatsapp_api_key' => ['WhatsApp Access Token', 'Permanent token for automated messaging.'],
                'ai_api_key' => ['AI Secret Key', 'Access credential for AI services.'],
            ] as $wire => [$label, $desc])
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">{{ $label }}</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" wire:model="{{ $wire }}" class="w-full rounded-lg border-slate-200 px-3 py-2 pr-9 font-semibold text-slate-900 focus:ring-0">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-600">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    <p class="text-[10px] font-normal text-slate-400">{{ $desc }}</p>
                </div>
            @endforeach

            <div class="md:col-span-2 space-y-1 pt-4 border-t border-slate-100">
                <label class="block font-bold text-slate-700">Custom Integration Key</label>
                <div class="relative" x-data="{ show: false }">
                    <input :type="show ? 'text' : 'password'" wire:model="custom_integrations_api_key" placeholder="Bearer Token..." class="w-full rounded-lg border-slate-200 px-3 py-2 pr-9 font-semibold text-slate-900 focus:ring-0">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-600">
                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
