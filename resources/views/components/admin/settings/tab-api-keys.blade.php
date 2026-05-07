<div class="space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-inner"><i class="fas fa-key text-lg"></i></div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">API Credentials Vault</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Secure Secret Storage & Rotation</p>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        @foreach([
            ['label' => 'OpenAI Platform', 'url' => 'https://platform.openai.com/api-keys', 'icon' => 'fa-robot', 'color' => 'indigo'],
            ['label' => 'Resend Mail', 'url' => 'https://app.resend.com/api-keys', 'icon' => 'fa-paper-plane', 'color' => 'sky'],
            ['label' => 'Mailgun Domains', 'url' => 'https://app.mailgun.com/app/sending/domains', 'icon' => 'fa-envelope', 'color' => 'rose']
        ] as $link)
            <a href="{{ $link['url'] }}" target="_blank" rel="noopener" class="group flex items-center gap-4 rounded-[2rem] border border-slate-200 bg-white p-6 transition-all hover:border-{{ $link['color'] }}-400 hover:shadow-xl hover:shadow-{{ $link['color'] }}-50">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-400 group-hover:bg-{{ $link['color'] }}-500 group-hover:text-white transition-colors"><i class="fas {{ $link['icon'] }} text-xs"></i></div>
                <div>
                    <p class="text-xs font-black text-slate-900">{{ $link['label'] }}</p>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">External Dashboard</p>
                </div>
            </a>
        @endforeach
    </div>

    <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
        <p class="px-2 mb-8 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Secure Integration Secrets</p>
        
        <div class="grid gap-6 md:grid-cols-2">
            @foreach([
                'mail_api_key' => ['Mail Gateway Key', 'Primary transport token for communications.'],
                'mail_api_secret' => ['Mail Gateway Secret', 'Secondary authentication secret for mailers.'],
                'whatsapp_api_key' => ['WhatsApp Meta Token', 'System-wide token for automated messaging.'],
                'ai_api_key' => ['Master Intelligence Key', 'Direct access credential for neural services.'],
            ] as $wire => [$label, $desc])
                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $label }}</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" wire:model="{{ $wire }}" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 pr-12 text-sm font-bold shadow-inner focus:bg-white focus:border-slate-900 focus:ring-0 transition-all">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-4 text-slate-400 hover:text-slate-900 transition-colors">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    <p class="px-1 text-[9px] font-medium text-slate-400 italic">{{ $desc }}</p>
                </div>
            @endforeach

            <div class="md:col-span-2 space-y-1.5 mt-4 pt-6 border-t border-slate-100">
                <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Global Integration Secret</label>
                <div class="relative" x-data="{ show: false }">
                    <input :type="show ? 'text' : 'password'" wire:model="custom_integrations_api_key" placeholder="Third-party bridge token..." class="w-full rounded-2xl border-slate-100 bg-slate-50 px-6 py-4 pr-12 text-sm font-bold shadow-inner focus:bg-white focus:border-slate-900 focus:ring-0 transition-all">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-4 text-slate-400 hover:text-slate-900 transition-colors">
                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-slate-900 p-6 flex gap-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white"><i class="fas fa-shield-halved text-xs"></i></div>
        <div>
            <p class="text-xs font-bold text-white leading-relaxed">Cryptographic Policy</p>
            <p class="mt-1 text-[10px] font-medium text-white/50 leading-relaxed">Secret fields are stored with AES-256 encryption within the site settings registry. Access to these values is restricted to high-privilege administrative sessions only.</p>
        </div>
    </div>
</div>
