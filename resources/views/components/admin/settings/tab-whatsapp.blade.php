@props([
    'app_public_url' => '',
    'whatsapp_enabled' => false,
    'whatsapp_provider' => 'meta_cloud',
    'whatsapp_chat_enabled' => false,
    'whatsapp_bot_enabled' => false,
    'testBotMessages' => [],
    'testBotInput' => '',
    'bridgeState' => 'disconnected',
    'bridgePhone' => '',
    'bridgeQrImage' => '',
    'bridgePairing' => false,
    'showDisconnectConfirm' => false,
])

<div class="space-y-6">
    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
            <i class="fab fa-whatsapp text-base"></i>
        </div>
        <div>
            <h3 class="text-base font-bold text-slate-900">WhatsApp</h3>
            <p class="text-xs text-slate-500">Configure Meta Cloud API credentials, link a WhatsApp account directly, manage AI Bot, and set up templates.</p>
        </div>
    </div>

    <!-- Quick Link Developer Cards -->
    <div class="grid gap-4 sm:grid-cols-2 text-xs font-semibold">
        <a href="https://developers.facebook.com/docs/whatsapp/cloud-api/get-started" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 hover:border-emerald-500 hover:shadow-xs transition-all">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600"><i class="fab fa-whatsapp text-sm"></i></div>
            <div>
                <p class="font-bold text-slate-900">Meta Developer Portal</p>
                <p class="text-[10px] font-normal text-slate-500">Configure Meta App, Tokens & Webhooks.</p>
            </div>
        </a>
        <a href="https://business.facebook.com/wa/manage/" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 hover:border-sky-500 hover:shadow-xs transition-all">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50 text-sky-600"><i class="fas fa-briefcase text-sm"></i></div>
            <div>
                <p class="font-bold text-slate-900">WhatsApp Manager</p>
                <p class="text-[10px] font-normal text-slate-500">Manage Business ID and Phone Profiles.</p>
            </div>
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3 text-xs font-semibold">
        <!-- Connection -->
        <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h4 class="font-bold text-slate-900 text-sm">Connection</h4>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full {{ $whatsapp_enabled ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $whatsapp_enabled ? 'Active' : 'Disabled' }}</span>
                    </div>
                    <button type="button" wire:click="$set('whatsapp_enabled', {{ !$whatsapp_enabled ? 'true' : 'false' }})" 
                        class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $whatsapp_enabled ? 'bg-emerald-600' : 'bg-slate-200' }}">
                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $whatsapp_enabled ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-4">
                @foreach([
                    'meta_cloud' => ['Meta Cloud API', 'fab fa-whatsapp', 'emerald'],
                    'twilio'     => ['Twilio Gateway', 'fas fa-phone-alt', 'sky'],
                    'custom'     => ['Custom Proxy', 'fas fa-terminal', 'slate'],
                    'baileys'    => ['Direct Account', 'fas fa-mobile-alt', 'violet'],
                ] as $key => [$name, $icon, $color])
                    <button type="button" wire:click="$set('whatsapp_provider', '{{ $key }}')" 
                        class="group relative flex flex-col gap-2 rounded-lg border-2 p-3 text-left transition-all {{ $whatsapp_provider === $key ? 'border-emerald-600 bg-emerald-50/50' : 'border-slate-100 bg-slate-50/50 hover:border-slate-200 hover:bg-white' }}">
                        <div class="flex h-7 w-7 items-center justify-center rounded-md {{ $whatsapp_provider === $key ? 'bg-emerald-600 text-white' : 'bg-white text-slate-400' }} shadow-xs">
                            <i class="{{ $icon }} text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold {{ $whatsapp_provider === $key ? 'text-slate-900' : 'text-slate-600' }}">{{ $name }}</p>
                        </div>
                    </button>
                @endforeach
            </div>

            <div class="grid gap-4 mt-4 sm:grid-cols-2">
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Business Phone ID</label>
                    <input type="text" wire:model="whatsapp_phone_number" placeholder="e.g. 10482937401" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Base API Endpoint</label>
                    <input type="text" wire:model="whatsapp_api_url" placeholder="https://graph.facebook.com/v17.0/..." class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                </div>
            </div>
        </div>

        <!-- API Credentials & Security -->
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <h4 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">API Credentials</h4>
            <div class="space-y-3">
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Access Token</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" wire:model="whatsapp_api_key" class="w-full rounded-lg border-slate-200 px-3 py-2 pr-9 font-semibold text-slate-900 focus:ring-0">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-600">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Webhook Verify Token</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" wire:model="whatsapp_webhook_verify_token" class="w-full rounded-lg border-slate-200 px-3 py-2 pr-9 font-semibold text-slate-900 focus:ring-0">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-600">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <div class="rounded-lg bg-slate-900 p-4 text-white space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Webhook Endpoint URL</p>
                    <p class="text-xs font-mono font-bold break-all">{{ rtrim($app_public_url ?? config('app.url'), '/') }}/whatsapp/webhook</p>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════════
             DIRECT ACCOUNT PAIRING (WhatsApp Web QR Login via Baileys Bridge)
             ══════════════════════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-3 rounded-xl border border-violet-200 bg-white p-6 shadow-xs space-y-4"
            x-data="{
                state:    '{{ $bridgeState }}',
                phone:    '{{ $bridgePhone }}',
                qrImage:  '{{ $bridgeQrImage }}',
                pairing:  {{ $bridgePairing ? 'true' : 'false' }},
                confirmDisconnect: {{ $showDisconnectConfirm ? 'true' : 'false' }},
                pollTimer: null,
                qrTimer:   null,

                init() {
                    // Initialise from server state on mount
                    this.$wire.initBridgeState();

                    // Listen for Livewire property changes
                    this.$watch('$wire.bridgeState',   v => { this.state = v; if (v === 'connected') { clearInterval(this.pollTimer); clearTimeout(this.qrTimer); } });
                    this.$watch('$wire.bridgePhone',   v => { this.phone = v; });
                    this.$watch('$wire.bridgeQrImage', v => { this.qrImage = v; });
                    this.$watch('$wire.bridgePairing', v => { this.pairing = v; if (v) this.startPolling(); else this.stopPolling(); });
                },

                startPairing() {
                    this.$wire.startPairing();
                    this.startPolling();
                    // Auto-refresh QR every 18s (Baileys QR expires ~20s)
                    this.qrTimer = setInterval(() => {
                        if (this.pairing && this.state !== 'connected') {
                            this.$wire.refreshQr();
                        } else {
                            clearInterval(this.qrTimer);
                        }
                    }, 18000);
                },

                startPolling() {
                    clearInterval(this.pollTimer);
                    this.pollTimer = setInterval(() => {
                        if (this.state === 'connected') { clearInterval(this.pollTimer); return; }
                        this.$wire.refreshBridgeStatus();
                    }, 2000);
                },

                stopPolling() {
                    clearInterval(this.pollTimer);
                    clearInterval(this.qrTimer);
                },

                stateLabel() {
                    return { connected: 'Connected', connecting: 'Connecting…', disconnected: 'Not Linked', unreachable: 'Bridge Offline' }[this.state] ?? this.state;
                },

                stateColor() {
                    return { connected: 'emerald', connecting: 'amber', disconnected: 'slate', unreachable: 'red' }[this.state] ?? 'slate';
                }
            }"
            x-init="init()"
        >
            <!-- Header -->
            <div class="flex items-center justify-between pb-3 border-b border-violet-100">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                        <i class="fas fa-mobile-alt text-sm"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Direct Account Pairing</h4>
                        <p class="text-xs text-slate-500 font-normal mt-0.5">Link a real WhatsApp account via QR code — no Meta Business API approval needed.</p>
                    </div>
                </div>

                <!-- Status pill -->
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full transition-colors"
                        :class="{
                            'bg-emerald-500 animate-pulse': state === 'connected',
                            'bg-amber-400 animate-pulse':  state === 'connecting',
                            'bg-slate-300':                state === 'disconnected',
                            'bg-red-400':                  state === 'unreachable'
                        }">
                    </span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500" x-text="stateLabel()"></span>
                    <template x-if="phone">
                        <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-700 font-mono" x-text="phone"></span>
                    </template>
                </div>
            </div>

            <!-- Connected State -->
            <template x-if="state === 'connected'">
                <div class="space-y-3">
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-4 flex items-start gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-600 text-white flex-shrink-0">
                            <i class="fas fa-check text-sm"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 text-sm">WhatsApp account linked</p>
                            <p class="text-xs text-slate-600 font-normal mt-0.5">
                                Order notifications and AI bot replies are now sent through 
                                <span class="font-mono font-bold" x-text="phone"></span>.
                                This account takes priority over the Meta Cloud API / Custom Proxy settings above.
                            </p>
                        </div>
                    </div>

                    <!-- Disconnect -->
                    <template x-if="!confirmDisconnect">
                        <button type="button" wire:click="confirmDisconnect"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3.5 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100 transition-colors">
                            <i class="fas fa-unlink text-xs"></i> Disconnect Account
                        </button>
                    </template>

                    <template x-if="confirmDisconnect">
                        <div class="rounded-xl border border-red-200 bg-red-50/60 p-4 space-y-3">
                            <p class="text-xs font-bold text-red-800">Are you sure? Disconnecting will stop all order update sending and AI bot replies through this account until re-linked.</p>
                            <div class="flex gap-2">
                                <button type="button" wire:click="disconnectBridge"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-red-700 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-red-800 transition-colors">
                                    <i class="fas fa-unlink text-xs"></i> Yes, Disconnect
                                </button>
                                <button type="button" wire:click="cancelDisconnect"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Pairing / QR State -->
            <template x-if="state !== 'connected'">
                <div class="space-y-4">
                    <template x-if="!pairing">
                        <div class="space-y-3">
                            <p class="text-xs text-slate-500 font-normal leading-relaxed">
                                The <span class="font-bold text-slate-700">whatsapp-bridge</span> Node.js service must be running before connecting.
                                Start it with: <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-[11px] text-slate-800">cd whatsapp-bridge && node index.js</code>
                            </p>

                            <template x-if="state === 'unreachable'">
                                <div class="rounded-lg border border-red-200 bg-red-50/60 p-3 text-xs text-red-700 font-medium flex items-center gap-2">
                                    <i class="fas fa-exclamation-circle"></i>
                                    Bridge service is offline or unreachable at the configured URL.
                                </div>
                            </template>

                            <button type="button" @click="startPairing()"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 rounded-lg bg-violet-600 px-4 py-2 text-xs font-semibold text-white hover:bg-violet-700 transition-colors shadow-xs">
                                <i class="fas fa-qrcode text-xs"></i>
                                Connect WhatsApp Account
                            </button>
                        </div>
                    </template>

                    <template x-if="pairing">
                        <div class="space-y-4">
                            <div class="rounded-xl border border-violet-200 bg-violet-50/40 p-4 space-y-3">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-qrcode text-violet-600 text-sm"></i>
                                    <span class="text-xs font-bold text-slate-800">Scan with WhatsApp</span>
                                    <span class="ml-auto text-[10px] font-bold text-amber-600 animate-pulse">
                                        <i class="fas fa-sync fa-spin text-[9px]"></i> Auto-refreshing QR…
                                    </span>
                                </div>

                                <div class="flex items-center gap-5">
                                    <template x-if="qrImage">
                                        <div class="flex-shrink-0 bg-white rounded-xl p-2.5 border border-slate-200 shadow-xs">
                                            <img :src="qrImage" alt="WhatsApp QR Code" class="w-40 h-40 rounded-lg">
                                        </div>
                                    </template>
                                    <template x-if="!qrImage">
                                        <div class="flex-shrink-0 bg-slate-100 rounded-xl w-40 h-40 flex items-center justify-center">
                                            <i class="fas fa-spinner fa-spin text-slate-400 text-2xl"></i>
                                        </div>
                                    </template>

                                    <ol class="list-decimal list-inside space-y-2 text-xs text-slate-600 font-normal">
                                        <li>Open <strong class="text-slate-800">WhatsApp</strong> on your phone</li>
                                        <li>Tap <strong class="text-slate-800">⋮ Menu → Linked Devices</strong></li>
                                        <li>Tap <strong class="text-slate-800">Link a Device</strong></li>
                                        <li>Scan the QR code with your camera</li>
                                        <li>Status will update automatically when linked ✓</li>
                                    </ol>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" @click="pairing = false; stopPolling()"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                                    <i class="fas fa-times text-xs"></i> Cancel
                                </button>
                                <button type="button" wire:click="refreshBridgeStatus"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                                    <i class="fas fa-sync text-xs"></i> Check Status
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Info bar: bridge webhook URL -->
            <div class="border-t border-violet-100 pt-3">
                <div class="rounded-lg bg-slate-900 p-3 text-white space-y-1 text-[10px]">
                    <p class="font-bold uppercase tracking-wider text-violet-400">Bridge Webhook URL (register in Node .env)</p>
                    <p class="font-mono break-all">{{ rtrim($app_public_url ?? config('app.url'), '/') }}/whatsapp/bridge-webhook</p>
                </div>
            </div>
        </div>

        <!-- WhatsApp AI Bot Configuration Card -->
        <div class="lg:col-span-3 rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                        <i class="fas fa-robot text-sm"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">AI Bot Assistant</h4>
                        <p class="text-xs text-slate-500 font-normal mt-0.5">Automate customer conversations using AI with read-only order/stock tools.</p>
                    </div>
                </div>
                <button type="button" wire:click="$set('whatsapp_bot_enabled', {{ !$whatsapp_bot_enabled ? 'true' : 'false' }})" 
                    class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $whatsapp_bot_enabled ? 'bg-rose-600' : 'bg-slate-200' }}">
                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $whatsapp_bot_enabled ? 'translate-x-4' : 'translate-x-0' }}"></span>
                </button>
            </div>

            <div class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Bot Persona Prompt</label>
                        <textarea wire:model="whatsapp_bot_persona_prompt" rows="3" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0 resize-none"></textarea>
                    </div>
                    <div class="space-y-2 pt-1">
                        <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-100 bg-slate-50 p-3 hover:bg-white transition-colors mt-5">
                            <input type="checkbox" wire:model="whatsapp_bot_inherit_ai_persona" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-0">
                            <div>
                                <span class="block font-bold text-slate-800">Inherit AI Assistant Persona</span>
                                <span class="block text-[11px] font-normal text-slate-500">Combines with primary store instructions set in AI Assistant settings.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Business Hours Start</label>
                        <input type="text" wire:model="whatsapp_bot_business_hours_start" placeholder="08:30" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                    </div>
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Business Hours End</label>
                        <input type="text" wire:model="whatsapp_bot_business_hours_end" placeholder="17:30" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                    </div>
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Max Auto-Replies Before Handoff Offer</label>
                        <input type="number" min="1" max="10" wire:model="whatsapp_bot_max_auto_replies" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Outside Hours Auto-Message</label>
                        <textarea wire:model="whatsapp_bot_outside_hours_message" rows="2" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0 resize-none"></textarea>
                    </div>
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Fallback Message</label>
                        <textarea wire:model="whatsapp_bot_fallback_message" rows="2" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0 resize-none"></textarea>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Escalation Keywords (comma-separated)</label>
                    <input type="text" wire:model="whatsapp_bot_escalation_keywords" placeholder="human, agent, person, support, help" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                    <p class="text-[11px] font-normal text-slate-500">Matching any keyword automatically pauses AI and alerts a human team member.</p>
                </div>

                <!-- Test Mode Preview Chat Widget -->
                <div class="rounded-xl border border-rose-200 bg-rose-50/40 p-5 space-y-4 shadow-xs mt-6">
                    <div class="flex items-center justify-between pb-2 border-b border-rose-200">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-md bg-rose-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white">
                                <i class="fas fa-flask text-[9px]"></i> Test Mode
                            </span>
                            <span class="text-xs font-bold text-slate-800">Inline Bot Preview Simulator</span>
                        </div>
                        <button type="button" wire:click="clearTestBotMessages" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">Clear Chat</button>
                    </div>
                    
                    <p class="text-[11px] font-medium text-slate-500">Simulates real AI replies and tool calls in real-time. Nothing is sent to actual WhatsApp.</p>

                    <!-- Chat Feed -->
                    <div class="rounded-lg bg-white border border-slate-200 p-4 min-h-[160px] max-h-[300px] overflow-y-auto space-y-3 font-sans">
                        @forelse($testBotMessages as $msg)
                            @if($msg['role'] === 'user')
                                <div class="flex justify-end">
                                    <div class="max-w-[80%] rounded-xl bg-slate-900 px-3.5 py-2 text-white text-xs">
                                        <p class="font-normal leading-relaxed">{{ $msg['content'] }}</p>
                                        <span class="block text-[9px] text-slate-400 text-right mt-1">{{ $msg['timestamp'] }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="flex justify-start">
                                    <div class="max-w-[85%] rounded-xl bg-slate-100 border border-slate-200 px-3.5 py-2 text-slate-900 text-xs space-y-1.5">
                                        <div class="flex items-center gap-1.5 text-rose-600 text-[10px] font-bold">
                                            <i class="fas fa-robot"></i> AI Bot Reply
                                        </div>
                                        <p class="font-normal leading-relaxed whitespace-pre-line">{{ $msg['content'] }}</p>
                                        
                                        @if(!empty($msg['tool_calls']))
                                            <div class="mt-2 rounded bg-slate-200/80 p-2 text-[10px] font-mono text-slate-700 space-y-1">
                                                <span class="font-bold text-slate-900 uppercase">Tool Calls Executed:</span>
                                                @foreach($msg['tool_calls'] as $tc)
                                                    <div class="border-t border-slate-300 pt-1">
                                                        <span class="text-indigo-700 font-bold">{{ $tc['name'] }}</span>({{ json_encode($tc['arguments']) }})
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        <span class="block text-[9px] text-slate-400 mt-1">{{ $msg['timestamp'] }}</span>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="py-8 text-center text-slate-400 font-medium">Type a test question below (e.g., "Where is my order ORD-1001?" or "Do you have iPhone screens in stock?")</div>
                        @endforelse
                    </div>

                    <!-- Input Box -->
                    <form wire:submit.prevent="sendTestBotMessage" class="flex gap-2">
                        <input type="text" wire:model="testBotInput" placeholder="Type a customer query to test..." class="flex-1 rounded-lg border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-900 focus:ring-0">
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition-colors shadow-xs">
                            <span wire:loading.remove wire:target="sendTestBotMessage"><i class="fas fa-paper-plane text-xs"></i> Test</span>
                            <span wire:loading wire:target="sendTestBotMessage"><i class="fas fa-spinner fa-spin text-xs"></i></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Message Templates -->
        <div class="lg:col-span-3 rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <h4 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">Message Templates</h4>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Order Receipt Template</label>
                    <textarea wire:model="whatsapp_order_template" rows="3" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0 resize-none"></textarea>
                </div>
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Payment Update Template</label>
                    <textarea wire:model="whatsapp_payment_template" rows="3" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0 resize-none"></textarea>
                </div>
            </div>
            <p class="text-[11px] font-normal text-slate-500">Available variables: <code class="font-mono text-slate-700">{order_number}</code>, <code class="font-mono text-slate-700">{order_status}</code>, <code class="font-mono text-slate-700">{payment_status}</code>, <code class="font-mono text-slate-700">{customer_name}</code></p>
        </div>

        <!-- Live Chat Widget -->
        <div class="lg:col-span-3 rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Live Chat Widget</h4>
                    <p class="text-xs text-slate-500 font-normal mt-0.5">Floating WhatsApp button for storefront visitors.</p>
                </div>
                <button type="button" wire:click="$set('whatsapp_chat_enabled', {{ !$whatsapp_chat_enabled ? 'true' : 'false' }})" 
                    class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $whatsapp_chat_enabled ? 'bg-emerald-600' : 'bg-slate-200' }}">
                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $whatsapp_chat_enabled ? 'translate-x-4' : 'translate-x-0' }}"></span>
                </button>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">WhatsApp Phone Number</label>
                    <input type="text" wire:model="whatsapp_chat_number" placeholder="e.g. 94702615076" 
                        class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0"
                        {{ !$whatsapp_chat_enabled ? 'disabled' : '' }}>
                </div>
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Default Welcome Message</label>
                    <textarea wire:model="whatsapp_chat_message" rows="2" placeholder="e.g. Hello, I need assistance."
                        class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0 resize-none"
                        {{ !$whatsapp_chat_enabled ? 'disabled' : '' }}></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
