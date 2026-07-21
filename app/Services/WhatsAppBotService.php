<?php

namespace App\Services;

use App\Models\NotificationOutbox;
use App\Models\Order;
use App\Models\SiteSetting;
use App\Models\Stock;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\Notifications\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use OpenAI;

class WhatsAppBotService
{
    protected ?OpenAI\Client $client = null;

    public function __construct(protected WhatsAppNotificationService $notificationService)
    {
        $apiKey = SiteSetting::get('ai_api_key') ?: env('OPENAI_API_KEY');
        if (filled($apiKey)) {
            $this->client = OpenAI::client($apiKey);
        }
    }

    public function isWithinBusinessHours(): bool
    {
        $timezone = SiteSetting::get('app_timezone', 'Asia/Colombo');
        $now = Carbon::now($timezone);

        $startStr = SiteSetting::get('whatsapp_bot_business_hours_start', '08:30');
        $endStr = SiteSetting::get('whatsapp_bot_business_hours_end', '17:30');

        try {
            $start = Carbon::createFromTimeString($startStr, $timezone);
            $end = Carbon::createFromTimeString($endStr, $timezone);

            return $now->between($start, $end);
        } catch (\Throwable $e) {
            return true; // Default to open if invalid format
        }
    }

    public function containsEscalationKeyword(string $text): bool
    {
        $keywords = SiteSetting::get('whatsapp_bot_escalation_keywords', [
            'human', 'agent', 'person', 'representative', 'support', 'manager', 'speak to someone'
        ]);

        if (is_string($keywords)) {
            $decoded = json_decode($keywords, true);
            $keywords = is_array($decoded) ? $decoded : array_map('trim', explode(',', $keywords));
        }

        $lowerText = strtolower($text);
        foreach ($keywords as $kw) {
            $kw = strtolower(trim($kw));
            if (filled($kw) && str_contains($lowerText, $kw)) {
                return true;
            }
        }

        return false;
    }

    public function notifyAdminOfEscalation(WhatsAppConversation $conversation, WhatsAppMessage $message): void
    {
        NotificationOutbox::create([
            'channel' => 'system_alert',
            'recipient' => SiteSetting::get('support_notification_email', 'support@displaylanka.lk'),
            'subject' => "WhatsApp Escalation: Customer {$conversation->phone_number} requested human agent",
            'payload' => [
                'conversation_id' => $conversation->id,
                'phone_number' => $conversation->phone_number,
                'customer_name' => $conversation->customer_name,
                'last_message' => $message->content,
            ],
            'status' => 'pending',
        ]);
    }

    public function generateReply(WhatsAppConversation $conversation, string $userMessageText): array
    {
        $recordedToolCalls = [];

        if (!$this->client) {
            return [
                'reply' => SiteSetting::get(
                    'whatsapp_bot_fallback_message',
                    'Thank you for reaching out. How else can I assist you today?'
                ),
                'tool_calls' => null,
            ];
        }

        $messages = $this->buildMessageHistory($conversation, $userMessageText);
        $tools = $this->getToolsDefinition();
        $model = SiteSetting::get('ai_model', 'gpt-4o-mini');

        try {
            // First Call to OpenAI with Tools
            $response = $this->client->chat()->create([
                'model' => $model,
                'messages' => $messages,
                'tools' => $tools,
                'temperature' => 0.3,
                'max_tokens' => 400,
            ]);

            $firstChoice = $response->choices[0]->message;
            $toolCalls = $firstChoice->toolCalls ?? [];

            // If AI requested Tool Execution
            if (!empty($toolCalls)) {
                $messages[] = [
                    'role' => 'assistant',
                    'content' => $firstChoice->content ?: null,
                    'tool_calls' => array_map(fn($tc) => [
                        'id' => $tc->id,
                        'type' => 'function',
                        'function' => [
                            'name' => $tc->function->name,
                            'arguments' => $tc->function->arguments,
                        ]
                    ], $toolCalls),
                ];

                foreach ($toolCalls as $toolCall) {
                    $fnName = $toolCall->function->name;
                    $fnArgs = json_decode($toolCall->function->arguments, true) ?? [];
                    
                    $toolResult = $this->executeToolCall($conversation, $fnName, $fnArgs);

                    $recordedToolCalls[] = [
                        'name' => $fnName,
                        'arguments' => $fnArgs,
                        'result' => $toolResult,
                    ];

                    $messages[] = [
                        'role' => 'tool',
                        'tool_call_id' => $toolCall->id,
                        'content' => json_encode($toolResult),
                    ];
                }

                // Second Call to OpenAI after feeding tool results
                $secondResponse = $this->client->chat()->create([
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.3,
                    'max_tokens' => 400,
                ]);

                $replyText = $secondResponse->choices[0]->message->content;
            } else {
                $replyText = $firstChoice->content;
            }

            return [
                'reply' => trim($replyText),
                'tool_calls' => !empty($recordedToolCalls) ? $recordedToolCalls : null,
            ];

        } catch (\Throwable $e) {
            Log::error('WhatsApp Bot AI Reply Error: ' . $e->getMessage());
            return [
                'reply' => SiteSetting::get(
                    'whatsapp_bot_fallback_message',
                    'Thank you for contacting Display Lanka. One of our support representatives will be with you shortly.'
                ),
                'tool_calls' => null,
            ];
        }
    }

    protected function executeToolCall(WhatsAppConversation $conversation, string $fnName, array $args): array
    {
        return match ($fnName) {
            'lookup_order_status' => $this->lookupOrderStatusTool($conversation, $args['order_number'] ?? ''),
            'check_stock' => $this->checkStockTool($args['query'] ?? ''),
            'get_price' => $this->getPriceTool($args['query'] ?? ''),
            default => ['error' => 'Unknown tool'],
        };
    }

    /**
     * PRIVACY CRITICAL TOOL:
     * Scoped strictly to matching customer's phone number!
     */
    public function lookupOrderStatusTool(WhatsAppConversation $conversation, string $orderRef): array
    {
        $orderRef = trim($orderRef);
        if (!filled($orderRef)) {
            return ['found' => false, 'message' => 'Order reference is required.'];
        }

        // Clean order reference (remove ORD- if needed or match exact order_number or id)
        $cleanRef = preg_replace('/[^a-zA-Z0-9\-]/', '', $orderRef);

        $order = Order::query()
            ->where(function ($q) use ($cleanRef) {
                $q->where('order_number', 'like', '%' . $cleanRef . '%')
                  ->orWhere('id', $cleanRef);
            })
            ->first();

        if (!$order) {
            return ['found' => false, 'message' => 'Order not found.'];
        }

        // PRIVACY ENFORCEMENT: Match customer_phone on order with conversation's phone_number
        $conversationPhoneDigits = preg_replace('/[^\d]/', '', $conversation->phone_number);
        $orderPhoneDigits = preg_replace('/[^\d]/', '', (string) $order->customer_phone);

        // Right-most 9 digits matching for phone comparison (e.g. 771234567)
        $conversationSuffix = strlen($conversationPhoneDigits) >= 9 ? substr($conversationPhoneDigits, -9) : $conversationPhoneDigits;
        $orderSuffix = strlen($orderPhoneDigits) >= 9 ? substr($orderPhoneDigits, -9) : $orderPhoneDigits;

        if (!filled($orderSuffix) || $conversationSuffix !== $orderSuffix) {
            // DO NOT LEAK ORDER DATA TO ANOTHER PHONE NUMBER!
            return ['found' => false, 'message' => 'Order not found under your phone number.'];
        }

        $itemsSummary = [];
        foreach ($order->items as $item) {
            $itemsSummary[] = $item->quantity . 'x ' . ($item->stock->model_name ?? $item->item_name ?? 'Item');
        }

        return [
            'found' => true,
            'order_number' => $order->order_number,
            'status' => $order->status_label,
            'payment_status' => $order->payment_status,
            'total_amount' => SiteSetting::get('currency_symbol', 'Rs') . ' ' . number_format($order->total_amount, 2),
            'tracking_number' => $order->tracking_number ?: 'Not assigned yet',
            'items' => implode(', ', $itemsSummary),
            'created_at' => $order->created_at->format('Y-m-d H:i'),
        ];
    }

    public function checkStockTool(string $query): array
    {
        $query = trim($query);
        if (!filled($query)) {
            return ['found' => false, 'message' => 'Product query is required.'];
        }

        $stocks = Stock::query()
            ->where(function ($q) use ($query) {
                $q->where('model_name', 'like', '%' . $query . '%')
                  ->orWhere('item_code', 'like', '%' . $query . '%')
                  ->orWhereHas('category', fn ($c) => $c->where('name', 'like', '%' . $query . '%'))
                  ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', '%' . $query . '%'));
            })
            ->limit(3)
            ->get();

        if ($stocks->isEmpty()) {
            return ['found' => false, 'message' => "No items matching '{$query}' found in inventory."];
        }

        $results = [];
        foreach ($stocks as $stock) {
            $status = $stock->quantity <= 0 ? 'Out of Stock' : ($stock->quantity <= ($stock->reorder_level ?? 5) ? 'Low Stock' : 'In Stock');
            $results[] = [
                'model_name' => $stock->model_name,
                'item_code' => $stock->item_code,
                'stock_status' => $status,
                'quantity_available' => $stock->quantity,
                'price' => SiteSetting::get('currency_symbol', 'Rs') . ' ' . number_format($stock->selling_price ?? 0, 2),
            ];
        }

        return [
            'found' => true,
            'matches' => $results,
        ];
    }

    public function getPriceTool(string $query): array
    {
        $query = trim($query);
        if (!filled($query)) {
            return ['found' => false, 'message' => 'Product query is required.'];
        }

        $stocks = Stock::query()
            ->where(function ($q) use ($query) {
                $q->where('model_name', 'like', '%' . $query . '%')
                  ->orWhere('item_code', 'like', '%' . $query . '%')
                  ->orWhereHas('category', fn ($c) => $c->where('name', 'like', '%' . $query . '%'));
            })
            ->limit(3)
            ->get();

        if ($stocks->isEmpty()) {
            return ['found' => false, 'message' => "No price info matching '{$query}' found."];
        }

        $results = [];
        $currency = SiteSetting::get('currency_symbol', 'Rs');
        foreach ($stocks as $stock) {
            $results[] = [
                'model_name' => $stock->model_name,
                'item_code' => $stock->item_code,
                'selling_price' => $currency . ' ' . number_format($stock->selling_price ?? 0, 2),
                'wholesale_price' => $stock->wholesale_price ? ($currency . ' ' . number_format($stock->wholesale_price, 2)) : null,
                'warranty' => $stock->warranty->name ?? 'Standard Warranty',
            ];
        }

        return [
            'found' => true,
            'prices' => $results,
        ];
    }

    protected function buildMessageHistory(WhatsAppConversation $conversation, string $currentMessageText): array
    {
        $systemPrompt = SiteSetting::get(
            'whatsapp_bot_persona_prompt',
            'You are the official WhatsApp customer service assistant for Display Lanka, Sri Lanka\'s leading display screen and electronics store.'
        );

        if (SiteSetting::get('whatsapp_bot_inherit_ai_persona', true)) {
            $globalPersona = SiteSetting::get('ai_prompt_context', '');
            if (filled($globalPersona)) {
                $systemPrompt = $globalPersona . "\n\n" . $systemPrompt;
            }
        }

        $systemPrompt .= "\n\nCRITICAL INSTRUCTIONS:
- You must ONLY state specific order status, tracking numbers, stock counts, or prices if they come directly from a tool call in this turn. Never fabricate or guess order status or prices.
- If looking up order status, call `lookup_order_status`. If it returns 'not found under your phone number', tell the customer you couldn't find an order matching that number on their account.
- Be concise, polite, professional, and helpful. Use clear business Sri Lankan English.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Retrieve last ~15 messages for history context
        $historyMessages = $conversation->messages()
            ->latest()
            ->take(15)
            ->get()
            ->reverse();

        foreach ($historyMessages as $msg) {
            if ($msg->sender_type === 'customer') {
                $messages[] = ['role' => 'user', 'content' => $msg->content];
            } elseif ($msg->sender_type === 'bot' || $msg->sender_type === 'agent') {
                $messages[] = ['role' => 'assistant', 'content' => $msg->content];
            }
        }

        // Add current user message
        $messages[] = ['role' => 'user', 'content' => $currentMessageText];

        return $messages;
    }

    protected function getToolsDefinition(): array
    {
        return [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'lookup_order_status',
                    'description' => 'Look up real order status, tracking number, and payment state by order number.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'order_number' => [
                                'type' => 'string',
                                'description' => 'The order number or reference code (e.g. ORD-1001 or 1001)',
                            ],
                        ],
                        'required' => ['order_number'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'check_stock',
                    'description' => 'Check real-time stock quantity and availability for a display screen or product item.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'query' => [
                                'type' => 'string',
                                'description' => 'Product name, model name, SKU, or item code (e.g. iPhone 11 screen, Samsung A12)',
                            ],
                        ],
                        'required' => ['query'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_price',
                    'description' => 'Get real-time selling price and warranty details for a product.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'query' => [
                                'type' => 'string',
                                'description' => 'Product name, model name, SKU, or item code',
                            ],
                        ],
                        'required' => ['query'],
                    ],
                ],
            ],
        ];
    }
}
