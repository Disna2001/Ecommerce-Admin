<x-admin.ui.panel padding="p-6">
    <x-slot:header>
        <div class="mb-6">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">AI operations</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Control the admin AI assistant, select the model, and define which business areas it should help manage.</p>
        </div>
    </x-slot:header>

    <div class="mb-6 grid gap-4 lg:grid-cols-3">
        @foreach([['title' => 'Fast Everyday', 'model' => 'gpt-4o-mini', 'desc' => 'Quick summaries and routine admin help.'], ['title' => 'Balanced', 'model' => 'gpt-4.1-mini', 'desc' => 'Stronger day-to-day analysis.'], ['title' => 'Advanced', 'model' => 'gpt-5', 'desc' => 'Use if your account supports a newer flagship model.']] as $preset)
            <button type="button" wire:click="$set('ai_model', '{{ $preset['model'] }}')" class="rounded-2xl border px-4 py-4 text-left transition {{ $ai_model === $preset['model'] ? 'border-slate-900 bg-slate-900 text-white shadow-lg shadow-slate-900/10 dark:border-white dark:bg-white dark:text-slate-900' : 'border-slate-200 bg-slate-50 text-slate-700 hover:border-slate-300 hover:bg-white dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                <p class="text-sm font-semibold">{{ $preset['title'] }}</p><p class="mt-1 text-xs font-mono {{ $ai_model === $preset['model'] ? 'text-white/70 dark:text-slate-500' : 'text-violet-600' }}">{{ $preset['model'] }}</p><p class="mt-3 text-sm leading-6 {{ $ai_model === $preset['model'] ? 'text-white/75 dark:text-slate-500' : 'text-slate-500 dark:text-slate-400' }}">{{ $preset['desc'] }}</p>
            </button>
        @endforeach
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 lg:col-span-2 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"><input type="checkbox" wire:model="ai_enabled" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Enable AI assistant inside admin</label>
        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">AI Provider</label><select wire:model="ai_provider" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"><option value="openai">OpenAI</option><option value="custom">Custom API</option></select></div>
        <div><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Model</label><input type="text" wire:model="ai_model" list="ai-model-options" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"><datalist id="ai-model-options"><option value="gpt-4o-mini"></option><option value="gpt-4.1-mini"></option><option value="gpt-4.1"></option><option value="gpt-4o"></option><option value="gpt-5"></option><option value="gpt-5-mini"></option></datalist></div>
        <div class="lg:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">API Key</label><input type="password" wire:model="ai_api_key" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"><input type="checkbox" wire:model="ai_sales_tracking_enabled" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Enable AI sales tracking guidance</label>
        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"><input type="checkbox" wire:model="ai_inventory_guidance_enabled" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Enable AI inventory guidance</label>
        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 lg:col-span-2 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"><input type="checkbox" wire:model="ai_management_guidance_enabled" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Enable AI management and operations recommendations</label>
        <div class="lg:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">AI Mission</label><input type="text" wire:model="ai_goal_text" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div class="lg:col-span-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Assistant Context Prompt</label><textarea wire:model="ai_prompt_context" rows="5" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></textarea></div>
    </div>
</x-admin.ui.panel>
