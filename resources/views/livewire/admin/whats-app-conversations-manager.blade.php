<div class="space-y-6">
    <!-- Header Block & KPI Badges -->
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Support Inbox</p>
                <h1 class="mt-1 text-xl font-bold text-slate-900">WhatsApp Conversations</h1>
                <p class="mt-1 text-xs text-slate-500">Monitor live customer chats, AI bot tool calls, and human agent handoffs.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                <span class="inline-flex items-center gap-1.5 rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-amber-700">
                    <span class="h-2 w-2 rounded-full bg-amber-500 {{ $awaitingHumanCount > 0 ? 'animate-pulse' : '' }}"></span>
                    <span>Awaiting Human: <strong>{{ $awaitingHumanCount }}</strong></span>
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-emerald-700">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    <span>Bot Active: <strong>{{ $botActiveCount }}</strong></span>
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-indigo-700">
                    <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                    <span>Agent Active: <strong>{{ $humanActiveCount }}</strong></span>
                </span>
            </div>
        </div>
    </div>

    <!-- Main 2-Column Inbox Workspace -->
    <div class="grid min-w-0 gap-6 lg:grid-cols-[340px_minmax(0,1fr)]">
        <!-- Left Panel: Conversation List -->
        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs space-y-3">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-3 text-xs text-slate-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search phone or name..." class="w-full rounded-lg border-slate-200 pl-9 pr-3 py-2 text-xs font-semibold text-slate-900 focus:ring-0">
                </div>

                <div class="flex flex-wrap gap-1 text-[11px] font-semibold">
                    @foreach([
                        'all' => 'All',
                        'awaiting_human' => 'Awaiting',
                        'bot_active' => 'Bot',
                        'human_active' => 'Agent',
                        'closed' => 'Closed',
                    ] as $key => $label)
                        <button type="button" wire:click="$set('statusFilter', '{{ $key }}')" 
                            class="rounded-md px-2.5 py-1 transition-colors {{ $statusFilter === $key ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- List -->
            <div class="space-y-2 max-h-[600px] overflow-y-auto pr-1">
                @forelse($conversations as $conv)
                    <button type="button" wire:click="selectConversation({{ $conv->id }})" 
                        class="w-full text-left rounded-xl border p-4 transition-all {{ $selectedConversationId === $conv->id ? 'border-slate-900 bg-slate-900 text-white shadow-xs' : 'border-slate-200 bg-white hover:border-slate-400' }}">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs truncate max-w-[180px] {{ $selectedConversationId === $conv->id ? 'text-white' : 'text-slate-900' }}">
                                {{ $conv->customer_name ?: $conv->phone_number }}
                            </span>
                            
                            <!-- Status Badge -->
                            @if($conv->status === 'awaiting_human')
                                <span class="rounded-full bg-amber-500 text-white text-[9px] font-bold px-2 py-0.5 animate-pulse">Awaiting</span>
                            @elseif($conv->status === 'bot_active')
                                <span class="rounded-full {{ $selectedConversationId === $conv->id ? 'bg-emerald-400/20 text-emerald-300 border border-emerald-400/30' : 'bg-emerald-50 text-emerald-600 border border-emerald-200' }} text-[9px] font-bold px-2 py-0.5">Bot Active</span>
                            @elseif($conv->status === 'human_active')
                                <span class="rounded-full {{ $selectedConversationId === $conv->id ? 'bg-indigo-400/20 text-indigo-300 border border-indigo-400/30' : 'bg-indigo-50 text-indigo-600 border border-indigo-200' }} text-[9px] font-bold px-2 py-0.5">Agent</span>
                            @else
                                <span class="rounded-full bg-slate-200 text-slate-600 text-[9px] font-bold px-2 py-0.5">Closed</span>
                            @endif
                        </div>

                        <p class="text-[11px] font-mono mt-1 opacity-70 truncate">{{ $conv->phone_number }}</p>
                        
                        @if($lastMsg = $conv->messages->last())
                            <p class="text-[11px] font-normal mt-2 line-clamp-1 {{ $selectedConversationId === $conv->id ? 'text-slate-300' : 'text-slate-500' }}">
                                {{ $lastMsg->content }}
                            </p>
                        @endif

                        <div class="mt-2 text-[9px] font-medium text-right opacity-60">
                            {{ $conv->last_message_at ? $conv->last_message_at->diffForHumans() : $conv->created_at->diffForHumans() }}
                        </div>
                    </button>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-200 p-8 text-center text-xs text-slate-400 font-medium">
                        No conversations matching filters.
                    </div>
                @endforelse

                <div class="pt-2">
                    {{ $conversations->links() }}
                </div>
            </div>
        </div>

        <!-- Right Panel: Active Thread View -->
        <div class="min-w-0">
            @if($selectedConversation)
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs flex flex-col min-h-[600px] justify-between space-y-6">
                    <!-- Thread Header -->
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                <i class="fab fa-whatsapp text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">{{ $selectedConversation->customer_name ?: 'Customer' }}</h3>
                                <p class="text-xs font-mono font-semibold text-slate-500">{{ $selectedConversation->phone_number }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Status Selector Dropdown -->
                            <select wire:change="updateStatus($event.target.value)" class="rounded-lg border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-900 focus:ring-0">
                                <option value="bot_active" {{ $selectedConversation->status === 'bot_active' ? 'selected' : '' }}>Bot Active</option>
                                <option value="awaiting_human" {{ $selectedConversation->status === 'awaiting_human' ? 'selected' : '' }}>Awaiting Human</option>
                                <option value="human_active" {{ $selectedConversation->status === 'human_active' ? 'selected' : '' }}>Human Active</option>
                                <option value="closed" {{ $selectedConversation->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>

                            <!-- Return to Bot Action Button -->
                            @if($selectedConversation->status !== 'bot_active')
                                <button type="button" wire:click="returnToBot" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-xs">
                                    <i class="fas fa-robot text-xs text-rose-500"></i>
                                    <span>Return to Bot</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Chat Feed -->
                    <div class="flex-1 overflow-y-auto pr-2 space-y-4 max-h-[420px] font-sans text-xs">
                        @foreach($selectedConversation->messages as $msg)
                            @if($msg->sender_type === 'customer')
                                <!-- Customer Message -->
                                <div class="flex justify-start">
                                    <div class="max-w-[80%] rounded-xl bg-slate-100 border border-slate-200 p-3.5 text-slate-900 space-y-1">
                                        <div class="flex items-center gap-2 text-[10px] font-bold text-slate-500">
                                            <i class="fas fa-user"></i> {{ $selectedConversation->customer_name ?: 'Customer' }}
                                        </div>
                                        <p class="font-normal leading-relaxed whitespace-pre-line text-xs">{{ $msg->content }}</p>
                                        <span class="block text-[9px] text-slate-400 text-right mt-1">{{ $msg->created_at->format('H:i') }}</span>
                                    </div>
                                </div>

                            @elseif($msg->sender_type === 'bot')
                                <!-- AI Bot Message -->
                                <div class="flex justify-start">
                                    <div class="max-w-[85%] rounded-xl bg-emerald-50/60 border border-emerald-200 p-3.5 text-slate-900 space-y-2">
                                        <div class="flex items-center gap-1.5 text-[10px] font-bold text-emerald-700">
                                            <i class="fas fa-robot"></i> AI Assistant Reply
                                        </div>
                                        <p class="font-normal leading-relaxed whitespace-pre-line text-xs">{{ $msg->content }}</p>

                                        <!-- Expandable Tool Calls Record -->
                                        @if(!empty($msg->tool_calls))
                                            <div x-data="{ open: false }" class="mt-2 border-t border-emerald-200/80 pt-2 text-[10px]">
                                                <button type="button" @click="open = !open" class="flex items-center gap-1 text-emerald-800 font-bold hover:underline">
                                                    <i class="fas fa-wrench text-[9px]"></i>
                                                    <span x-text="open ? 'Hide Tool Calls' : 'Show Executed Tool Calls ({{ count($msg->tool_calls) }})'"></span>
                                                    <i class="fas fa-chevron-down text-[8px] transition-transform" :class="open ? 'rotate-180' : ''"></i>
                                                </button>
                                                <div x-show="open" class="mt-2 rounded-lg bg-white border border-emerald-200 p-2.5 space-y-2 font-mono text-slate-700">
                                                    @foreach($msg->tool_calls as $tc)
                                                        <div class="space-y-0.5">
                                                            <div class="text-indigo-700 font-bold">Function: {{ $tc['name'] ?? '' }}</div>
                                                            <div class="text-[9px] text-slate-500">Args: {{ json_encode($tc['arguments'] ?? []) }}</div>
                                                            <div class="text-[9px] text-slate-700 font-semibold bg-slate-50 p-1.5 rounded border border-slate-100 mt-1">Result: {{ json_encode($tc['result'] ?? []) }}</div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        <span class="block text-[9px] text-slate-400 text-right mt-1">{{ $msg->created_at->format('H:i') }}</span>
                                    </div>
                                </div>

                            @elseif($msg->sender_type === 'agent')
                                <!-- Human Agent Message -->
                                <div class="flex justify-end">
                                    <div class="max-w-[80%] rounded-xl bg-slate-900 text-white p-3.5 space-y-1 shadow-xs">
                                        <div class="flex items-center gap-1.5 text-[10px] font-bold text-indigo-300">
                                            <i class="fas fa-headset"></i> Support Agent
                                        </div>
                                        <p class="font-normal leading-relaxed whitespace-pre-line text-xs">{{ $msg->content }}</p>
                                        <span class="block text-[9px] text-slate-400 text-right mt-1">{{ $msg->created_at->format('H:i') }}</span>
                                    </div>
                                </div>

                            @elseif($msg->sender_type === 'system')
                                <!-- System Note -->
                                <div class="flex justify-center my-2">
                                    <span class="rounded-full bg-slate-100 border border-slate-200 px-3 py-1 text-[10px] font-semibold text-slate-500">
                                        <i class="fas fa-info-circle mr-1"></i> {{ $msg->content }}
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Agent Reply Console -->
                    <form wire:submit.prevent="sendAgentReply" class="space-y-2 pt-3 border-t border-slate-100">
                        <div class="flex gap-2">
                            <textarea wire:model="agentReplyText" rows="2" placeholder="Type a manual reply to customer (switches conversation to Agent Active)..." class="flex-1 rounded-lg border-slate-200 px-3 py-2 text-xs font-semibold text-slate-900 focus:ring-0 resize-none"></textarea>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-900 px-5 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition-colors shadow-xs">
                                <span wire:loading.remove wire:target="sendAgentReply"><i class="fas fa-paper-plane text-xs"></i> Send</span>
                                <span wire:loading wire:target="sendAgentReply"><i class="fas fa-spinner fa-spin text-xs"></i></span>
                            </button>
                        </div>
                        <p class="text-[10px] text-slate-400 font-normal">Sending a manual message automatically sets conversation status to <strong>Human Active</strong>.</p>
                    </form>
                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-200 bg-white p-12 text-center text-xs text-slate-400 font-medium">
                    Select a conversation from the left inbox panel to view message history and reply.
                </div>
            @endif
        </div>
    </div>
</div>
