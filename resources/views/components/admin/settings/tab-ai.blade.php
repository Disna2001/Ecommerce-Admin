@props(['ai_model' => ''])
<div class="space-y-6">
    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
            <i class="fas fa-brain text-sm"></i>
        </div>
        <div>
            <h3 class="text-base font-bold text-slate-900">AI Assistant</h3>
            <p class="text-xs text-slate-500">Configure AI provider, models, and operational guidance features.</p>
        </div>
    </div>

    <!-- AI Presets -->
    <div class="grid gap-3 lg:grid-cols-3 text-xs font-semibold">
        @foreach([
            ['title' => 'Fast Assistant', 'model' => 'gpt-4o-mini', 'desc' => 'Quick summaries and standard admin support.', 'icon' => 'fa-bolt'],
            ['title' => 'Balanced Logic', 'model' => 'gpt-4.1-mini', 'desc' => 'Deep analysis for daily commerce operations.', 'icon' => 'fa-scale-balanced'],
            ['title' => 'High Reasoning', 'model' => 'gpt-5', 'desc' => 'Maximum reasoning for strategic decision making.', 'icon' => 'fa-crown']
        ] as $preset)
            <button type="button" wire:click="$set('ai_model', '{{ $preset['model'] }}')" 
                class="group relative overflow-hidden rounded-xl border p-4 text-left transition-all {{ ($ai_model ?? '') === $preset['model'] ? 'border-slate-900 bg-slate-900 text-white shadow-xs' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-400' }}">
                <div class="flex items-center justify-between">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg {{ ($ai_model ?? '') === $preset['model'] ? 'bg-white/20' : 'bg-slate-100 text-slate-500' }}"><i class="fas {{ $preset['icon'] }} text-xs"></i></div>
                    @if(($ai_model ?? '') === $preset['model'])
                        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    @endif
                </div>
                <p class="mt-3 text-xs font-bold">{{ $preset['title'] }}</p>
                <p class="mt-0.5 text-[10px] font-mono font-semibold {{ ($ai_model ?? '') === $preset['model'] ? 'text-slate-300' : 'text-slate-500' }}">{{ $preset['model'] }}</p>
                <p class="mt-2 text-[11px] font-normal leading-relaxed {{ ($ai_model ?? '') === $preset['model'] ? 'text-slate-300' : 'text-slate-500' }}">{{ $preset['desc'] }}</p>
            </button>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-2 text-xs font-semibold">
        <!-- AI Provider -->
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <h4 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">AI Provider</h4>
            <div class="space-y-3">
                <label class="flex cursor-pointer items-center gap-2.5 rounded-lg border border-slate-100 bg-slate-50 p-3 hover:bg-white hover:border-slate-200 transition-colors">
                    <input type="checkbox" wire:model="ai_enabled" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-0">
                    <span class="font-bold text-slate-800">Enable AI Assistant Services</span>
                </label>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Provider</label>
                        <select wire:model="ai_provider" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                            <option value="openai">OpenAI (Direct)</option>
                            <option value="custom">Custom Proxy</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Model ID</label>
                        <input type="text" wire:model="ai_model" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">API Secret Key</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" wire:model="ai_api_key" class="w-full rounded-lg border-slate-200 px-3 py-2 pr-9 font-semibold text-slate-900 focus:ring-0">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-600">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Features -->
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <h4 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">AI Features</h4>
            <div class="space-y-2">
                @foreach([
                    'ai_sales_tracking_enabled' => ['Sales Insights', 'Analyze transaction patterns and demand trends.'],
                    'ai_inventory_guidance_enabled' => ['Inventory Recommendations', 'Smart restock levels and pricing suggestions.'],
                    'ai_management_guidance_enabled' => ['Operations Assistant', 'Actionable recommendations for daily operations.']
                ] as $wire => [$title, $desc])
                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-100 bg-slate-50 p-3 hover:bg-white transition-colors">
                        <input type="checkbox" wire:model="{{ $wire }}" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-0">
                        <div>
                            <span class="block font-bold text-slate-900">{{ $title }}</span>
                            <span class="block text-[11px] font-normal text-slate-500 mt-0.5">{{ $desc }}</span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- AI Instructions -->
        <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <h4 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">AI Instructions</h4>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">System Goal</label>
                    <input type="text" wire:model="ai_goal_text" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                </div>
                <div class="space-y-1">
                    <label class="block font-bold text-slate-700">Context Prompt</label>
                    <textarea wire:model="ai_prompt_context" rows="3" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0 resize-none"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
