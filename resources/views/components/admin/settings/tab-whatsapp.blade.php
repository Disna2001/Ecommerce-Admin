@props(['app_public_url' => '', 'whatsapp_enabled' => false, 'whatsapp_provider' => 'meta_cloud', 'whatsapp_chat_enabled' => false])
<div class="space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 shadow-inner"><i class="fas fa-comment-dots text-lg"></i></div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">WhatsApp Meta Integration</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Automated Business Messaging</p>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <a href="https://developers.facebook.com/docs/whatsapp/cloud-api/get-started" target="_blank" rel="noopener" class="group flex items-center gap-4 rounded-[2rem] border border-slate-200 bg-white p-6 transition-all hover:border-emerald-400 hover:shadow-xl hover:shadow-emerald-50">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-500 group-hover:text-white transition-colors"><i class="fab fa-whatsapp text-xl"></i></div>
            <div>
                <p class="text-sm font-black text-slate-900">Developer Portal</p>
                <p class="text-[10px] font-medium text-slate-400 leading-relaxed">Configure Meta App, Token, and Webhooks.</p>
            </div>
        </a>
        <a href="https://business.facebook.com/wa/manage/" target="_blank" rel="noopener" class="group flex items-center gap-4 rounded-[2rem] border border-slate-200 bg-white p-6 transition-all hover:border-sky-400 hover:shadow-xl hover:shadow-sky-50">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 group-hover:bg-sky-500 group-hover:text-white transition-colors"><i class="fas fa-briefcase text-lg"></i></div>
            <div>
                <p class="text-sm font-black text-slate-900">WhatsApp Manager</p>
                <p class="text-[10px] font-medium text-slate-400 leading-relaxed">Manage Business ID and Phone Profiles.</p>
            </div>
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6 rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
            <div class="flex items-center justify-between px-2">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Direct Connect: WhatsApp Messaging</p>
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full {{ $whatsapp_enabled ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></span>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $whatsapp_enabled ? 'INTEGRATION ACTIVE' : 'SYSTEM OFFLINE' }}</span>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach([
                    'meta_cloud' => ['Meta Cloud API', 'fab fa-whatsapp', 'emerald'],
                    'twilio' => ['Twilio Gateway', 'fas fa-phone-alt', 'rose'],
                    'custom' => ['Custom Proxy', 'fas fa-terminal', 'slate'],
                ] as $key => [$name, $icon, $color])
                    <button type="button" wire:click="$set('whatsapp_provider', '{{ $key }}')" 
                        class="group relative flex flex-col gap-4 rounded-3xl border-2 p-5 transition-all {{ $whatsapp_provider === $key ? 'border-'.$color.'-500 bg-'.$color.'-50/50 ring-4 ring-'.$color.'-500/10' : 'border-slate-100 bg-slate-50/50 hover:border-slate-200 hover:bg-white' }}">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $whatsapp_provider === $key ? 'bg-'.$color.'-500 text-white' : 'bg-white text-slate-400 group-hover:text-'.$color.'-500' }} transition-colors shadow-sm">
                            <i class="{{ $icon }} text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black {{ $whatsapp_provider === $key ? 'text-slate-900' : 'text-slate-500' }}">{{ $name }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter mt-0.5">{{ $whatsapp_provider === $key ? 'SELECTED' : 'AVAILABLE' }}</p>
                        </div>
                        @if($whatsapp_provider === $key)
                            <div class="absolute right-4 top-4 flex h-5 w-5 items-center justify-center rounded-full bg-{{ $color }}-500 text-white text-[8px]"><i class="fas fa-check"></i></div>
                        @endif
                    </button>
                @endforeach
            </div>

            <div class="grid gap-6 mt-8 sm:grid-cols-2">
                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Business Phone ID</label>
                    <input type="text" wire:model="whatsapp_phone_number" placeholder="e.g. 10482937401" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-0 transition-all">
                </div>

                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Base API Endpoint</label>
                    <input type="text" wire:model="whatsapp_api_url" placeholder="https://graph.facebook.com/v17.0/..." class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-0 transition-all">
                </div>
            </div>
        </div>

        <div class="space-y-6 rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
            <p class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Security & Hooks</p>
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Permanent Access Token</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" wire:model="whatsapp_api_key" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 pr-12 text-sm font-bold shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-0 transition-all">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-4 text-slate-400 hover:text-emerald-500 transition-colors">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Webhook Verify Token</label>
                    <input type="text" wire:model="whatsapp_webhook_verify_token" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-0 transition-all">
                </div>

                <div class="rounded-2xl bg-slate-900 p-6 text-white overflow-hidden relative">
                    <div class="absolute right-0 top-0 -mr-6 -mt-6 h-24 w-24 rounded-full bg-emerald-500/20"></div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-400">Live Endpoint URL</p>
                    <p class="mt-2 text-xs font-mono font-bold break-all opacity-80">{{ rtrim($app_public_url ?? config('app.url'), '/') }}/whatsapp/webhook</p>
                    <p class="mt-4 text-[10px] font-medium text-white/40 leading-relaxed italic">Subscribe to 'message_status' events in Meta Configuration using this URL and your Verify Token.</p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3 space-y-6 rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
            <p class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Automation Blueprints</p>
            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Order Receipt Template</label>
                    <textarea wire:model="whatsapp_order_template" rows="4" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-6 py-4 text-sm font-bold shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-0 transition-all resize-none"></textarea>
                </div>
                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Payment Settlement Template</label>
                    <textarea wire:model="whatsapp_payment_template" rows="4" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-6 py-4 text-sm font-bold shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-0 transition-all resize-none"></textarea>
                </div>
            </div>
        </div>

        <!-- Storefront Chat Button Configurations -->
        <div class="lg:col-span-3 space-y-6 rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
            <div class="flex items-center justify-between px-2">
                <div>
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-wider">Storefront WhatsApp Floating Button</h4>
                    <p class="text-[10px] font-medium text-slate-400 leading-relaxed mt-1">Configure the WhatsApp quick chat button visible to storefront visitors.</p>
                </div>
                <button type="button" wire:click="$set('whatsapp_chat_enabled', {{ !$whatsapp_chat_enabled ? 'true' : 'false' }})" 
                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $whatsapp_chat_enabled ? 'bg-emerald-600' : 'bg-slate-200' }}">
                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $whatsapp_chat_enabled ? 'translate-x-5' : 'translate-x-0' }}"></span>
                </button>
            </div>

            <div class="grid gap-6 md:grid-cols-2 mt-4">
                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">WhatsApp Number (with Country Code, no spaces/special chars)</label>
                    <input type="text" wire:model="whatsapp_chat_number" placeholder="e.g. 94702615076" 
                        class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-0 transition-all"
                        {{ !$whatsapp_chat_enabled ? 'disabled' : '' }}>
                </div>
                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Default Message</label>
                    <textarea wire:model="whatsapp_chat_message" rows="2" placeholder="e.g. Hello, I need assistance with display screens."
                        class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-0 transition-all resize-none"
                        {{ !$whatsapp_chat_enabled ? 'disabled' : '' }}></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
