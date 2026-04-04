<div>
    <button wire:click="openAssistant"
            class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-3 rounded-full bg-gradient-to-r from-slate-900 via-violet-700 to-indigo-600 px-5 py-4 text-white shadow-2xl transition hover:-translate-y-0.5 hover:shadow-violet-500/30">
        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-white/12">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.847-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.847a4.5 4.5 0 003.09 3.09L15.75 12l-2.847.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715 18 9.75l-.259-1.035a2.25 2.25 0 00-1.456-1.456L15.25 7l1.035-.259a2.25 2.25 0 001.456-1.456L18 4.25l.259 1.035a2.25 2.25 0 001.456 1.456L20.75 7l-1.035.259a2.25 2.25 0 00-1.456 1.456z"></path>
            </svg>
        </span>
        <span class="hidden text-left md:block">
            <span class="block text-sm font-semibold">AI Assistant</span>
            <span class="block text-xs text-white/70">Sales, stock, and admin help</span>
        </span>
    </button>

    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div class="fixed inset-0 bg-slate-950/65 backdrop-blur-sm" wire:click="closeAssistant"></div>

                <div class="relative z-10 w-full max-w-6xl overflow-hidden rounded-[2rem] border border-white/10 bg-white shadow-2xl shadow-slate-900/30">
                    <div class="grid lg:grid-cols-[340px_minmax(0,1fr)]">
                        <aside class="border-b border-slate-200 bg-slate-950 text-white lg:border-b-0 lg:border-r lg:border-slate-800">
                            <div class="border-b border-white/10 px-6 py-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/50">AI Operations</p>
                                        <h3 class="mt-2 text-2xl font-black">Admin Assistant</h3>
                                        <p class="mt-2 text-sm leading-6 text-white/70">Use live business context for faster decisions on sales, stock, payments, and operations.</p>
                                    </div>
                                    <button wire:click="closeAssistant" class="rounded-full border border-white/10 p-2 text-white/70 transition hover:bg-white/10 hover:text-white">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18 18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-6 px-6 py-5">
                                <div class="flex flex-wrap gap-2">
                                    <span class="rounded-full border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white/70">{{ ucfirst($context) }} mode</span>
                                    <span class="rounded-full border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white/70">{{ count($messages) }} messages</span>
                                    <span class="rounded-full border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white/70">{{ count(array_filter($enabledTools)) }} tools enabled</span>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    @foreach([
                                        ['label' => 'Pending Orders', 'value' => $metrics['pending_orders'] ?? 0],
                                        ['label' => 'Payment Reviews', 'value' => $metrics['pending_payments'] ?? 0],
                                        ['label' => 'Low Stock', 'value' => $metrics['low_stock'] ?? 0],
                                        ['label' => 'Monthly Revenue', 'value' => 'Rs ' . ($metrics['month_revenue'] ?? '0')],
                                    ] as $metric)
                                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">{{ $metric['label'] }}</p>
                                            <p class="mt-2 text-lg font-black">{{ $metric['value'] }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/45">Quick Tools</p>
                                    <div class="mt-3 grid gap-3">
                                        @foreach($toolCards as $tool)
                                            <button wire:click="runTool('{{ $tool['key'] }}')"
                                                    @disabled(!$tool['enabled'] || $isLoading)
                                                    class="rounded-2xl border border-white/10 px-4 py-4 text-left transition {{ $tool['enabled'] ? 'bg-white/5 hover:bg-white/10' : 'cursor-not-allowed bg-white/5 opacity-45' }}">
                                                <div class="flex items-start gap-3">
                                                    <span class="mt-0.5 inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white/10 text-white">
                                                        <i class="fas {{ $tool['icon'] }}"></i>
                                                    </span>
                                                    <div>
                                                        <p class="text-sm font-semibold">{{ $tool['title'] }}</p>
                                                        <p class="mt-1 text-xs leading-5 text-white/60">{{ $tool['description'] }}</p>
                                                    </div>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                @if(!empty($lastInsight))
                                    <div class="rounded-[1.5rem] border border-violet-400/20 bg-gradient-to-br from-violet-500/15 to-cyan-400/10 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-violet-200/70">{{ $lastInsight['title'] ?? 'Latest Insight' }}</p>
                                        <p class="mt-3 text-sm leading-6 text-white/80">{{ \Illuminate\Support\Str::limit($lastInsight['content'] ?? '', 180) }}</p>
                                    </div>
                                @endif

                                <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/45">Best Uses</p>
                                    <ul class="mt-3 space-y-2 text-sm leading-6 text-white/65">
                                        <li>Ask for the next admin priorities before starting the day.</li>
                                        <li>Use inventory mode before reordering or discounting products.</li>
                                        <li>Use payment watch when the review queue starts piling up.</li>
                                    </ul>
                                </div>
                            </div>
                        </aside>

                        <section class="flex min-h-[760px] flex-col bg-slate-50">
                            <div class="border-b border-slate-200 bg-white px-6 py-5">
                                <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Assistant Mode</p>
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            @foreach([
                                                'general' => 'General',
                                                'management' => 'Management',
                                                'sales' => 'Sales',
                                                'inventory' => 'Inventory',
                                                'pricing' => 'Pricing',
                                            ] as $value => $label)
                                                <button type="button"
                                                        wire:click="$set('context', '{{ $value }}')"
                                                        class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $context === $value ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                                    {{ $label }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        @foreach($quickPrompts as $quickPrompt)
                                            <button wire:click="useQuickPrompt('{{ addslashes($quickPrompt['prompt']) }}', '{{ $quickPrompt['context'] }}')"
                                                    class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                                                {{ $quickPrompt['label'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="flex-1 overflow-y-auto px-6 py-5">
                                <div class="space-y-4">
                                    @foreach($messages as $message)
                                        <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                                            <div class="max-w-3xl rounded-[1.6rem] px-5 py-4 shadow-sm {{ $message['role'] === 'user' ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white text-slate-700' }}">
                                                <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] {{ $message['role'] === 'user' ? 'text-white/60' : 'text-slate-400' }}">
                                                    @if($message['role'] === 'user')
                                                        <i class="fas fa-user"></i>
                                                        <span>You</span>
                                                    @else
                                                        <i class="fas fa-sparkles text-violet-500"></i>
                                                        <span>Assistant</span>
                                                    @endif
                                                </div>
                                                <div class="whitespace-pre-line text-sm leading-7">{{ $message['content'] }}</div>
                                            </div>
                                        </div>
                                    @endforeach

                                    @if($isLoading)
                                        <div class="flex justify-start">
                                            <div class="rounded-[1.6rem] border border-slate-200 bg-white px-5 py-4 shadow-sm">
                                                <div class="flex items-center gap-3 text-sm text-slate-500">
                                                    <span class="inline-flex h-3 w-3 animate-pulse rounded-full bg-violet-500"></span>
                                                    Thinking through your store data...
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="border-t border-slate-200 bg-white px-6 py-5">
                                <div class="mb-3 flex items-center justify-between gap-3">
                                    <p class="text-sm text-slate-500">Ask about orders, stock, pricing, staff workflow, or admin priorities.</p>
                                    <button wire:click="clearConversation"
                                            type="button"
                                            class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                                        Clear
                                    </button>
                                </div>

                                <form wire:submit.prevent="askQuestion">
                                    <div class="rounded-[1.6rem] border border-slate-200 bg-slate-50 p-3">
                                        <textarea wire:model="prompt"
                                                  rows="3"
                                                  placeholder="Example: What should I prioritize today based on pending orders, payment reviews, and low stock?"
                                                  class="w-full resize-none border-0 bg-transparent text-sm text-slate-700 shadow-none focus:ring-0"></textarea>
                                        <div class="mb-3 flex flex-wrap gap-2">
                                            @foreach([
                                                "Summarize today's risks",
                                                'Which products need restock first?',
                                                'What should I review before closing today?',
                                            ] as $hint)
                                                <button type="button"
                                                        wire:click="$set('prompt', '{{ $hint }}')"
                                                        class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-500 transition hover:border-slate-300 hover:text-slate-900">
                                                    {{ $hint }}
                                                </button>
                                            @endforeach
                                        </div>
                                        <div class="mt-3 flex items-center justify-between gap-3">
                                            <p class="text-xs text-slate-400">The assistant uses your configured AI model and current admin data context.</p>
                                            <button type="submit"
                                                    wire:loading.attr="disabled"
                                                    class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-violet-600 to-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:from-violet-700 hover:to-indigo-700 disabled:opacity-50">
                                                <i class="fas fa-paper-plane"></i>
                                                <span>Send</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
