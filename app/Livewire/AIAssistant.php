<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Order;
use App\Models\SiteSetting;
use App\Models\Stock;
use App\Services\OpenAIService;
use Livewire\Component;

class AIAssistant extends Component
{
    public bool $isOpen = false;
    public string $prompt = '';
    public bool $isLoading = false;
    public string $context = 'general';
    public array $messages = [];
    public array $lastInsight = [];
    public array $metrics = [];

    protected OpenAIService $openAIService;

    public function boot(OpenAIService $openAIService): void
    {
        $this->openAIService = $openAIService;
    }

    public function mount(): void
    {
        $this->refreshMetrics();
    }

    public function render()
    {
        return view('livewire.a-i-assistant', [
            'quickPrompts' => $this->quickPrompts(),
            'toolCards' => $this->toolCards(),
            'enabledTools' => $this->enabledTools(),
        ]);
    }

    public function openAssistant(): void
    {
        $this->isOpen = true;
        $this->refreshMetrics();

        if (empty($this->messages)) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Ask for sales guidance, stock insights, pricing help, or an admin summary. I can use your current store data to answer more usefully.',
            ];
        }
    }

    public function closeAssistant(): void
    {
        $this->isOpen = false;
    }

    public function clearConversation(): void
    {
        $this->messages = [[
            'role' => 'assistant',
            'content' => 'Conversation cleared. Start with a new question or use one of the quick actions below.',
        ]];
        $this->prompt = '';
        $this->lastInsight = [];
    }

    public function useQuickPrompt(string $prompt, string $context = 'general'): void
    {
        $this->context = $context;
        $this->prompt = $prompt;
        $this->askQuestion();
    }

    public function runTool(string $tool): void
    {
        $map = [
            'daily_brief' => [
                'context' => 'management',
                'prompt' => 'Give me a short admin daily brief covering priority orders, payments waiting for review, low stock risks, and the next best actions.',
                'title' => 'Daily Brief',
            ],
            'inventory_watch' => [
                'context' => 'inventory',
                'prompt' => 'Review current low stock and reorder risks. Tell me which items need attention first and what action to take.',
                'title' => 'Inventory Watch',
            ],
            'sales_watch' => [
                'context' => 'sales',
                'prompt' => 'Summarize current sales signals using available order and product data. Highlight likely opportunities and risks.',
                'title' => 'Sales Watch',
            ],
            'payment_watch' => [
                'context' => 'management',
                'prompt' => 'Review payment verification workload and tell me the operational priorities for pending payments and order confirmations.',
                'title' => 'Payment Watch',
            ],
        ];

        if (!isset($map[$tool])) {
            return;
        }

        $this->context = $map[$tool]['context'];
        $this->prompt = $map[$tool]['prompt'];
        $this->askQuestion($map[$tool]['title']);
    }

    public function askQuestion(?string $forcedTitle = null): void
    {
        $cleanPrompt = trim($this->prompt);

        if ($cleanPrompt === '') {
            return;
        }

        $this->isLoading = true;
        $this->refreshMetrics();

        $this->messages[] = [
            'role' => 'user',
            'content' => $cleanPrompt,
        ];

        try {
            $response = $this->openAIService->chat(
                $this->buildMessages($cleanPrompt),
                null,
                0.5,
                700
            );

            $assistantReply = trim($response->choices[0]->message->content ?? 'No response returned.');

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $assistantReply,
            ];

            $this->lastInsight = [
                'title' => $forcedTitle ?: $this->contextLabel($this->context),
                'content' => $assistantReply,
            ];

            $this->prompt = '';
        } catch (\Throwable $e) {
            $fallback = $this->buildFallbackReply($e->getMessage());

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $fallback,
            ];

            $this->lastInsight = [
                'title' => 'Fallback Guidance',
                'content' => $fallback,
            ];
        }

        $this->isLoading = false;
    }

    private function buildMessages(string $latestPrompt): array
    {
        $history = collect($this->messages)
            ->filter(fn (array $message) => in_array($message['role'], ['user', 'assistant'], true))
            ->take(-8)
            ->map(fn (array $message) => [
                'role' => $message['role'],
                'content' => $message['content'],
            ])
            ->values()
            ->all();

        return array_merge([
            ['role' => 'system', 'content' => $this->systemPrompt()],
            ['role' => 'system', 'content' => $this->businessContextBlock()],
        ], $history ?: [['role' => 'user', 'content' => $latestPrompt]]);
    }

    private function systemPrompt(): string
    {
        $basePrompt = SiteSetting::get(
            'ai_prompt_context',
            'You are a helpful business assistant specializing in retail, sales tracking, and inventory management.'
        );

        $goal = SiteSetting::get(
            'ai_goal_text',
            'Help the team manage sales, stock levels, and operational decisions quickly.'
        );

        $specialty = match ($this->context) {
            'inventory' => 'Focus on inventory levels, reorder urgency, product availability, and stocking decisions.',
            'sales' => 'Focus on sales signals, product performance, order flow, and revenue opportunities.',
            'pricing' => 'Focus on pricing decisions, margin awareness, and competitiveness.',
            'management' => 'Focus on admin operations, team priorities, workflow issues, and what to do next.',
            default => 'Answer clearly, practically, and in a way that helps an admin take action fast.',
        };

        return trim($basePrompt . ' Mission: ' . $goal . ' ' . $specialty . ' Keep responses concise, practical, and action-oriented.');
    }

    private function businessContextBlock(): string
    {
        return "Current business snapshot:\n"
            . "- Pending orders: {$this->metrics['pending_orders']}\n"
            . "- Payment reviews pending: {$this->metrics['pending_payments']}\n"
            . "- Low stock items: {$this->metrics['low_stock']}\n"
            . "- Active products: {$this->metrics['active_products']}\n"
            . "- Categories: {$this->metrics['categories']}\n"
            . "- Revenue this month: Rs {$this->metrics['month_revenue']}\n"
            . "- AI sales guidance enabled: " . ($this->enabledTools()['sales'] ? 'yes' : 'no') . "\n"
            . "- AI inventory guidance enabled: " . ($this->enabledTools()['inventory'] ? 'yes' : 'no') . "\n"
            . "- AI management guidance enabled: " . ($this->enabledTools()['management'] ? 'yes' : 'no');
    }

    private function refreshMetrics(): void
    {
        $this->metrics = [
            'pending_orders' => Order::whereIn('status', ['pending', 'confirmed'])->count(),
            'pending_payments' => Order::where('payment_review_status', 'pending_review')->count(),
            'low_stock' => Stock::whereColumn('quantity', '<=', 'reorder_level')->count(),
            'active_products' => Stock::where('status', 'active')->count(),
            'categories' => Category::count(),
            'month_revenue' => number_format(
                Order::whereIn('status', ['completed', 'delivered'])
                    ->whereMonth('created_at', now()->month)
                    ->sum('total'),
                0
            ),
        ];
    }

    private function quickPrompts(): array
    {
        return [
            ['label' => 'Today priorities', 'prompt' => 'What should I focus on first today as admin?', 'context' => 'management'],
            ['label' => 'Low stock risks', 'prompt' => 'Which current low stock items are the biggest risk?', 'context' => 'inventory'],
            ['label' => 'Sales opportunities', 'prompt' => 'Where are the best current sales opportunities?', 'context' => 'sales'],
            ['label' => 'Pricing advice', 'prompt' => 'Give me pricing guidance for improving margin without hurting conversions.', 'context' => 'pricing'],
        ];
    }

    private function toolCards(): array
    {
        return [
            [
                'key' => 'daily_brief',
                'title' => 'Daily Brief',
                'description' => 'A fast summary of what needs admin attention first.',
                'icon' => 'fa-newspaper',
                'enabled' => true,
            ],
            [
                'key' => 'inventory_watch',
                'title' => 'Inventory Watch',
                'description' => 'Low stock review with reorder-oriented guidance.',
                'icon' => 'fa-box-open',
                'enabled' => $this->enabledTools()['inventory'],
            ],
            [
                'key' => 'sales_watch',
                'title' => 'Sales Watch',
                'description' => 'Sales trend guidance based on live store signals.',
                'icon' => 'fa-chart-line',
                'enabled' => $this->enabledTools()['sales'],
            ],
            [
                'key' => 'payment_watch',
                'title' => 'Payment Watch',
                'description' => 'Highlights payment review and order confirmation workload.',
                'icon' => 'fa-money-check-dollar',
                'enabled' => $this->enabledTools()['management'],
            ],
        ];
    }

    private function enabledTools(): array
    {
        return [
            'sales' => filter_var(SiteSetting::get('ai_sales_tracking_enabled', true), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            'inventory' => filter_var(SiteSetting::get('ai_inventory_guidance_enabled', true), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            'management' => filter_var(SiteSetting::get('ai_management_guidance_enabled', true), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
        ];
    }

    private function contextLabel(string $context): string
    {
        return match ($context) {
            'inventory' => 'Inventory Insight',
            'sales' => 'Sales Insight',
            'pricing' => 'Pricing Guidance',
            'management' => 'Management Guidance',
            default => 'AI Guidance',
        };
    }

    private function buildFallbackReply(string $errorMessage): string
    {
        $intro = "The live AI reply is temporarily unavailable.\nReason: {$errorMessage}\n\n";

        return $intro . match ($this->context) {
            'inventory' => "Quick inventory fallback:\n"
                . "- Low stock items currently tracked: {$this->metrics['low_stock']}\n"
                . "- Active products: {$this->metrics['active_products']}\n"
                . "- Best next step: review low-stock items first, confirm supplier lead times, and restock the fastest-moving items before slower products.",
            'sales' => "Quick sales fallback:\n"
                . "- Pending orders: {$this->metrics['pending_orders']}\n"
                . "- Revenue this month: Rs {$this->metrics['month_revenue']}\n"
                . "- Best next step: clear pending orders quickly, verify payments faster, and promote items with healthy stock so conversions are not blocked.",
            'pricing' => "Quick pricing fallback:\n"
                . "- Use high-demand items with healthy stock for margin improvements first.\n"
                . "- Avoid price increases on products already at risk of low stock until replenishment is stable.\n"
                . "- Compare your strongest categories first before changing store-wide pricing.",
            'management' => "Quick admin fallback:\n"
                . "- Pending orders: {$this->metrics['pending_orders']}\n"
                . "- Payment reviews pending: {$this->metrics['pending_payments']}\n"
                . "- Low stock items: {$this->metrics['low_stock']}\n"
                . "- Best next step: handle payment reviews first, clear pending orders second, and then review low stock risks.",
            default => "Quick fallback summary:\n"
                . "- Pending orders: {$this->metrics['pending_orders']}\n"
                . "- Payment reviews pending: {$this->metrics['pending_payments']}\n"
                . "- Low stock items: {$this->metrics['low_stock']}\n"
                . "- Revenue this month: Rs {$this->metrics['month_revenue']}\n"
                . "- Try the request again in a minute, or use the quick tools for a simpler AI call.",
        };
    }
}
