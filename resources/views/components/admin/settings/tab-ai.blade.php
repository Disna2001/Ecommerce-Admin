@props(['ai_model' => ''])
<div class="space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 shadow-inner"><i class="fas fa-brain text-lg"></i></div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Intelligence Protocols</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Cognitive Services & AI Routing</p>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        @foreach([
            ['title' => 'Efficiency Fleet', 'model' => 'gpt-4o-mini', 'desc' => 'High-velocity summaries and standard admin support.', 'icon' => 'fa-bolt'],
            ['title' => 'Balanced Logic', 'model' => 'gpt-4.1-mini', 'desc' => 'Deep analysis for daily commerce operations.', 'icon' => 'fa-scale-balanced'],
            ['title' => 'Flagship Neural', 'model' => 'gpt-5', 'desc' => 'Maximum reasoning for strategic decision making.', 'icon' => 'fa-crown']
        ] as $preset)
            <button type="button" wire:click="$set('ai_model', '{{ $preset['model'] }}')" 
                class="group relative overflow-hidden rounded-[2rem] border p-6 text-left transition-all {{ ($ai_model ?? '') === $preset['model'] ? 'border-slate-900 bg-slate-900 text-white shadow-2xl shadow-slate-200' : 'border-slate-200 bg-white text-slate-600 hover:border-rose-400 hover:shadow-xl hover:shadow-rose-50' }}">
                <div class="flex items-center justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ ($ai_model ?? '') === $preset['model'] ? 'bg-white/20' : 'bg-slate-50 text-slate-400' }}"><i class="fas {{ $preset['icon'] }} text-xs"></i></div>
                    @if(($ai_model ?? '') === $preset['model'])
                        <span class="h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_10px_rgba(52,211,153,0.8)] animate-pulse"></span>
                    @endif
                </div>
                <p class="mt-4 text-sm font-black">{{ $preset['title'] }}</p>
                <p class="mt-1 text-[10px] font-bold font-mono {{ ($ai_model ?? '') === $preset['model'] ? 'text-white/60' : 'text-rose-500' }}">{{ $preset['model'] }}</p>
                <p class="mt-4 text-xs leading-relaxed {{ ($ai_model ?? '') === $preset['model'] ? 'text-white/70' : 'text-slate-400' }}">{{ $preset['desc'] }}</p>
            </button>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="space-y-6 rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
            <p class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Core Intelligence Config</p>
            <div class="space-y-4">
                <label class="group flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-4 transition-all hover:bg-white hover:border-rose-500">
                    <input type="checkbox" wire:model="ai_enabled" class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-0">
                    <span class="text-xs font-bold text-slate-600 group-hover:text-slate-900">Authorize AI Assistance on the Bridge</span>
                </label>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Neural Provider</label>
                        <select wire:model="ai_provider" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-rose-500 focus:ring-0 transition-all">
                            <option value="openai">OpenAI (Direct)</option>
                            <option value="custom">Custom Proxy</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Active Model ID</label>
                        <input type="text" wire:model="ai_model" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold shadow-inner focus:bg-white focus:border-rose-500 focus:ring-0 transition-all">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Master API Credential</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" wire:model="ai_api_key" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3 pr-12 text-sm font-bold shadow-inner focus:bg-white focus:border-rose-500 focus:ring-0 transition-all">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-4 text-slate-400 hover:text-rose-500 transition-colors">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6 rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
            <p class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Operational Toggles</p>
            <div class="space-y-3">
                @foreach([
                    'ai_sales_tracking_enabled' => ['Sales Intelligence', 'Forecast and identify transaction patterns.'],
                    'ai_inventory_guidance_enabled' => ['Inventory Prediction', 'Smart restock and stock health monitoring.'],
                    'ai_management_guidance_enabled' => ['Management Bridge', 'Strategic recommendations for store operations.']
                ] as $wire => [$title, $desc])
                    <label class="group flex cursor-pointer items-start gap-4 rounded-2xl border border-slate-50 bg-slate-50/50 p-4 transition-all hover:bg-white hover:border-emerald-400">
                        <input type="checkbox" wire:model="{{ $wire }}" class="mt-1 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-0">
                        <div>
                            <span class="block text-xs font-black text-slate-900 uppercase tracking-tight">{{ $title }}</span>
                            <span class="mt-1 block text-[10px] font-medium text-slate-400 leading-relaxed">{{ $desc }}</span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6 rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
            <p class="px-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Cognitive Programming</p>
            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">System Goal (Mission)</label>
                    <input type="text" wire:model="ai_goal_text" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-6 py-4 text-sm font-bold shadow-inner focus:bg-white focus:border-rose-500 focus:ring-0 transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="px-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Behavioral Context Prompt</label>
                    <textarea wire:model="ai_prompt_context" rows="4" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-6 py-4 text-sm font-bold shadow-inner focus:bg-white focus:border-rose-500 focus:ring-0 transition-all resize-none"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
