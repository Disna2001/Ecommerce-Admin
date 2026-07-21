<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppSession;
use App\Services\AuditLogService;
use App\Services\Billing\BillCustomizationService;
use App\Services\Operations\SystemDataService;
use App\Services\WhatsAppBotService;
use App\Services\WhatsAppBridgeService;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Permission;

class SystemSettingsManager extends Component
{
    use WithFileUploads;

    public string $activeTab = 'communications';

    public $backupFile;

    public bool $saved = false;

    // Email
    public string $mail_mailer = 'smtp';

    public string $mail_from_name = '';

    public string $mail_from_address = '';

    public string $mail_smtp_host = '';

    public string $mail_smtp_port = '587';

    public string $mail_smtp_username = '';

    public string $mail_smtp_password = '';

    public string $mail_smtp_encryption = 'tls';

    public string $mail_api_key = '';

    public string $mail_api_secret = '';

    public string $order_notification_email = '';

    public string $support_notification_email = '';

    public string $test_email_recipient = '';

    // Hosting / storefront identity
    public string $app_public_url = '';

    public bool $force_https = false;

    public string $app_timezone = 'Asia/Colombo';

    public string $app_locale = 'en';

    public string $support_email = '';

    public string $support_phone = '';

    public string $company_address = '';

    public string $company_tax_id = '';

    public string $currency_code = 'LKR';

    public string $currency_symbol = 'Rs';

    public string $asset_cdn_url = '';

    public array $billing_profiles = [];

    public array $billing_default_profiles = [];

    public array $printer_catalog = [];

    public string $billing_preview_invoice_status = 'paid';

    public string $billing_preview_invoice_customer_name = 'Dishna Chamuditha';

    public string $billing_preview_invoice_customer_email = 'customer@example.com';

    public string $billing_preview_invoice_customer_phone = '0702615076';

    public string $billing_preview_invoice_customer_address = 'Weliwita, Ratnapura, Sri Lanka';

    public string $billing_preview_invoice_item_one_name = 'Netflix Premium Plan';

    public string $billing_preview_invoice_item_one_description = '1 month digital access';

    public string $billing_preview_invoice_item_two_name = 'Spotify Family';

    public string $billing_preview_invoice_item_two_description = '30-day activation';

    public string $billing_preview_receipt_status = 'paid';

    public string $billing_preview_receipt_customer_name = 'Walk-in customer';

    public string $billing_preview_receipt_customer_phone = '0712345678';

    public string $billing_preview_receipt_item_one_name = 'Gift Card Top-up';

    public string $billing_preview_receipt_item_two_name = 'Service Fee';

    // WhatsApp Integration
    public bool $whatsapp_enabled = false;

    public string $whatsapp_provider = 'meta_cloud';

    public string $whatsapp_phone_number = '';

    public string $whatsapp_api_url = '';

    public string $whatsapp_api_key = '';

    public string $whatsapp_webhook_verify_token = '';

    public string $whatsapp_order_template = 'Your order {order_number} has been placed successfully.';

    public string $whatsapp_payment_template = 'Payment update for order {order_number}: {payment_status}.';

    public bool $whatsapp_chat_enabled = false;

    public string $whatsapp_chat_number = '';

    public string $whatsapp_chat_message = '';

    // WhatsApp AI Bot
    public bool $whatsapp_bot_enabled = false;

    public string $whatsapp_bot_persona_prompt = 'You are the official WhatsApp customer service assistant for Display Lanka, Sri Lanka\'s leading display screen and electronics store.';

    public bool $whatsapp_bot_inherit_ai_persona = true;

    public string $whatsapp_bot_business_hours_start = '08:30';

    public string $whatsapp_bot_business_hours_end = '17:30';

    public string $whatsapp_bot_outside_hours_message = 'Our office is currently closed. We will reply to your message during standard business hours.';

    public string $whatsapp_bot_escalation_keywords = 'human, agent, person, support, help, representative, manager';

    public string $whatsapp_bot_fallback_message = 'Thank you for reaching out. One of our support representatives will be with you shortly.';

    public int $whatsapp_bot_max_auto_replies = 3;

    // Interactive Test/Preview Chat Widget State
    public array $testBotMessages = [];

    public string $testBotInput = '';

    public string $testBotPhone = '+94702615076';

    // AI Configuration
    public bool $ai_enabled = true;

    public string $ai_provider = 'openai';

    public string $ai_model = 'gpt-4o-mini';

    public string $ai_api_key = '';

    public string $custom_integrations_api_key = '';

    public bool $ai_sales_tracking_enabled = true;

    public bool $ai_inventory_guidance_enabled = true;

    public bool $ai_management_guidance_enabled = true;

    public string $ai_prompt_context = 'You are a helpful business assistant specializing in retail, sales tracking, and inventory management.';

    public string $ai_goal_text = 'Help the team manage sales, stock levels, and operational decisions quickly.';

    // OneSignal Push Notification settings
    public bool $onesignal_enabled = false;

    public string $onesignal_app_id = '';

    public string $onesignal_rest_api_key = '';

    public bool $maintenance_mode = false;

    public string $maintenance_secret = 'admin-bypass';

    // ── WhatsApp Bridge (Baileys QR pairing) ─────────────────────────────────
    public string $bridgeState      = 'disconnected'; // connecting | connected | disconnected | unreachable
    public string $bridgePhone      = '';
    public string $bridgeQrImage    = '';   // base64 PNG data URL
    public bool   $bridgePairing    = false; // true while QR is being displayed
    public bool   $showDisconnectConfirm = false;


    public function mount(BillCustomizationService $billCustomizationService): void
    {
        $keys = [
            'mail_mailer', 'mail_from_name', 'mail_from_address', 'mail_smtp_host', 'mail_smtp_port',
            'mail_smtp_username', 'mail_smtp_password', 'mail_smtp_encryption', 'mail_api_key', 'mail_api_secret',
            'order_notification_email', 'support_notification_email',
            'app_public_url', 'force_https', 'app_timezone', 'app_locale', 'support_email', 'support_phone',
            'company_address', 'company_tax_id', 'currency_code', 'currency_symbol', 'asset_cdn_url',
            'whatsapp_enabled', 'whatsapp_provider', 'whatsapp_phone_number', 'whatsapp_api_url',
            'whatsapp_api_key', 'whatsapp_webhook_verify_token', 'whatsapp_order_template', 'whatsapp_payment_template',
            'whatsapp_chat_enabled', 'whatsapp_chat_number', 'whatsapp_chat_message',
            'whatsapp_bot_enabled', 'whatsapp_bot_persona_prompt', 'whatsapp_bot_inherit_ai_persona',
            'whatsapp_bot_business_hours_start', 'whatsapp_bot_business_hours_end',
            'whatsapp_bot_outside_hours_message', 'whatsapp_bot_escalation_keywords',
            'whatsapp_bot_fallback_message', 'whatsapp_bot_max_auto_replies',
            'ai_enabled', 'ai_provider', 'ai_model', 'ai_api_key',
            'ai_sales_tracking_enabled', 'ai_inventory_guidance_enabled', 'ai_management_guidance_enabled',
            'ai_prompt_context', 'ai_goal_text', 'custom_integrations_api_key',
            'onesignal_enabled', 'onesignal_app_id', 'onesignal_rest_api_key',
        ];

        foreach ($keys as $key) {
            $value = SiteSetting::get($key);
            if (! is_null($value)) {
                if (is_bool($this->$key)) {
                    $this->$key = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                } elseif (is_int($this->$key)) {
                    $this->$key = (int) $value;
                } else {
                    $this->$key = (string) $value;
                }
            }
        }

        $this->billing_profiles = $billCustomizationService->configuredProfiles();
        $this->billing_default_profiles = $billCustomizationService->configuredAssignments();
        $this->printer_catalog = $billCustomizationService->configuredPrinterCatalog();
        $this->test_email_recipient = $this->support_notification_email ?: $this->mail_from_address;
        $this->maintenance_mode = app()->isDownForMaintenance();
    }

    public function sendTestBotMessage(WhatsAppBotService $botService): void
    {
        if (! filled(trim($this->testBotInput))) {
            return;
        }

        $userText = trim($this->testBotInput);
        $this->testBotInput = '';

        $this->testBotMessages[] = [
            'role' => 'user',
            'content' => $userText,
            'timestamp' => now()->format('H:i'),
        ];

        $dummyConversation = new WhatsAppConversation([
            'phone_number' => $this->testBotPhone ?: '+94702615076',
            'customer_name' => 'Admin Preview User',
            'status' => 'bot_active',
        ]);

        $aiResult = $botService->generateReply($dummyConversation, $userText);

        $this->testBotMessages[] = [
            'role' => 'bot',
            'content' => $aiResult['reply'] ?? 'No response generated.',
            'tool_calls' => $aiResult['tool_calls'] ?? null,
            'timestamp' => now()->format('H:i'),
        ];
    }

    public function clearTestBotMessages(): void
    {
        $this->testBotMessages = [];
    }

    // ── WhatsApp Bridge Pairing ───────────────────────────────────────────────

    public function initBridgeState(WhatsAppBridgeService $bridge): void
    {
        $status = $bridge->getStatus();
        $this->bridgeState = $status['state'] ?? 'disconnected';
        $this->bridgePhone = $status['phone_number'] ?? '';
    }

    public function startPairing(WhatsAppBridgeService $bridge): void
    {
        $qr = $bridge->getQr();

        if ($qr && isset($qr['image'])) {
            $this->bridgeQrImage = $qr['image'];
            $this->bridgePairing = true;
            $this->bridgeState   = 'connecting';
        } else {
            // Bridge already connected — just refresh status
            $this->refreshBridgeStatus($bridge);
        }
    }

    public function refreshBridgeStatus(WhatsAppBridgeService $bridge): void
    {
        $status = $bridge->getStatus();
        $this->bridgeState = $status['state'] ?? 'disconnected';
        $this->bridgePhone = $status['phone_number'] ?? '';

        if ($this->bridgeState === 'connected') {
            $this->bridgePairing  = false;
            $this->bridgeQrImage  = '';
        }
    }

    public function refreshQr(WhatsAppBridgeService $bridge): void
    {
        $qr = $bridge->getQr();
        if ($qr && isset($qr['image'])) {
            $this->bridgeQrImage = $qr['image'];
        }
    }

    public function confirmDisconnect(): void
    {
        $this->showDisconnectConfirm = true;
    }

    public function cancelDisconnect(): void
    {
        $this->showDisconnectConfirm = false;
    }

    public function disconnectBridge(WhatsAppBridgeService $bridge): void
    {
        $bridge->logout();
        $this->bridgeState          = 'disconnected';
        $this->bridgePhone          = '';
        $this->bridgePairing        = false;
        $this->bridgeQrImage        = '';
        $this->showDisconnectConfirm = false;
        $this->dispatch('notify', type: 'info', message: 'WhatsApp account disconnected.');
    }


    public function save(AuditLogService $auditLogService, BillCustomizationService $billCustomizationService): void
    {
        $this->validate($this->rules());

        foreach ($this->textKeys() as $key) {
            SiteSetting::set($key, (string) $this->$key, 'text', $this->groupFor($key));
        }

        foreach ($this->booleanKeys() as $key) {
            SiteSetting::set($key, $this->$key ? '1' : '0', 'boolean', $this->groupFor($key));
        }

        SiteSetting::set('whatsapp_bot_max_auto_replies', (string) $this->whatsapp_bot_max_auto_replies, 'text', 'whatsapp');

        $normalizedProfiles = array_values(array_map(
            fn (array $profile) => $billCustomizationService->normalizeProfile($profile),
            array_filter($this->billing_profiles, 'is_array')
        ));

        if ($normalizedProfiles === []) {
            $normalizedProfiles = $billCustomizationService->defaultProfiles();
        }

        $this->billing_profiles = $normalizedProfiles;
        $this->billing_default_profiles = array_merge(
            $billCustomizationService->defaultAssignments(),
            $this->billing_default_profiles
        );
        $this->printer_catalog = array_values(array_map(
            fn (array $printer) => $billCustomizationService->normalizePrinter($printer),
            array_filter($this->printer_catalog, 'is_array')
        ));

        if ($this->printer_catalog === []) {
            $this->printer_catalog = $billCustomizationService->defaultPrinterCatalog();
        }

        SiteSetting::set('billing_profiles', $this->billing_profiles, 'json', 'billing', 'Bill customization profiles');
        SiteSetting::set('billing_default_profiles', $this->billing_default_profiles, 'json', 'billing', 'Bill default profile assignments');
        SiteSetting::set('printer_catalog', $this->printer_catalog, 'json', 'billing', 'Managed printer catalog');

        $auditLogService->log(
            'settings.updated',
            null,
            'System settings updated from admin panel.',
            [
                'mail_mailer' => $this->mail_mailer,
                'app_public_url' => $this->app_public_url,
                'force_https' => $this->force_https,
                'whatsapp_enabled' => $this->whatsapp_enabled,
                'whatsapp_bot_enabled' => $this->whatsapp_bot_enabled,
                'whatsapp_webhook_ready' => filled($this->whatsapp_webhook_verify_token),
                'ai_enabled' => $this->ai_enabled,
                'ai_model' => $this->ai_model,
                'billing_profile_count' => count($this->billing_profiles),
                'printer_catalog_count' => count($this->printer_catalog),
            ],
            auth()->id()
        );

        $this->saved = true;
        $this->dispatch('notify', type: 'success', message: 'System settings updated successfully.');
    }

    public function sendTestEmail(): void
    {
        $this->validate([
            'test_email_recipient' => 'required|email',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'nullable|string|max:120',
            'mail_mailer' => 'required|string|max:50',
        ]);

        $this->applyRuntimeMailConfiguration();

        try {
            Mail::raw(
                "This is a live test email from {$this->mail_from_name}.\n\n".
                'App URL: '.($this->app_public_url ?: config('app.url'))."\n".
                'Support Email: '.($this->support_email ?: $this->support_notification_email ?: 'not set')."\n".
                'Time: '.now()->toDateTimeString(),
                function ($message) {
                    $message
                        ->to($this->test_email_recipient)
                        ->subject('System test email - '.($this->mail_from_name ?: config('app.name')));
                }
            );

            $this->dispatch('notify', type: 'success', message: 'Test email sent successfully.');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('notify', type: 'error', message: 'Test email could not be sent: '.$e->getMessage());
        }
    }

    protected function applyRuntimeMailConfiguration(): void
    {
        if ($this->mail_mailer === 'brevo') {
            config([
                'mail.default' => 'smtp',
                'mail.from.address' => $this->mail_from_address,
                'mail.from.name' => $this->mail_from_name ?: config('app.name'),
                'mail.mailers.smtp.host' => 'smtp-relay.brevo.com',
                'mail.mailers.smtp.port' => '2525',
                'mail.mailers.smtp.username' => $this->mail_smtp_username ?: $this->mail_from_address,
                'mail.mailers.smtp.password' => $this->mail_api_key,
                'mail.mailers.smtp.encryption' => 'tls',
            ]);
        } else {
            config([
                'mail.default' => $this->mail_mailer,
                'mail.from.address' => $this->mail_from_address,
                'mail.from.name' => $this->mail_from_name ?: config('app.name'),
                'mail.mailers.smtp.host' => $this->mail_smtp_host,
                'mail.mailers.smtp.port' => $this->mail_smtp_port,
                'mail.mailers.smtp.username' => $this->mail_smtp_username,
                'mail.mailers.smtp.password' => $this->mail_smtp_password,
                'mail.mailers.smtp.encryption' => $this->mail_smtp_encryption,
            ]);
        }
    }

    protected function rules(): array
    {
        return [
            'mail_mailer' => 'required|string|max:50',
            'mail_from_name' => 'nullable|string|max:120',
            'mail_from_address' => 'nullable|email',
            'mail_smtp_host' => 'nullable|string|max:255',
            'mail_smtp_port' => 'nullable|string|max:10',
            'mail_smtp_username' => 'nullable|string|max:255',
            'mail_smtp_password' => 'nullable|string|max:255',
            'mail_smtp_encryption' => 'nullable|string|max:50',
            'mail_api_key' => 'nullable|string|max:255',
            'mail_api_secret' => 'nullable|string|max:255',
            'order_notification_email' => 'nullable|email',
            'support_notification_email' => 'nullable|email',
            'test_email_recipient' => 'nullable|email',

            'app_public_url' => 'nullable|url|max:255',
            'app_timezone' => 'nullable|string|max:120',
            'app_locale' => 'nullable|string|max:10',
            'support_email' => 'nullable|email',
            'support_phone' => 'nullable|string|max:50',
            'company_address' => 'nullable|string|max:500',
            'company_tax_id' => 'nullable|string|max:120',
            'currency_code' => 'nullable|string|max:12',
            'currency_symbol' => 'nullable|string|max:12',
            'asset_cdn_url' => 'nullable|url|max:255',

            'whatsapp_provider' => 'nullable|string|max:50',
            'whatsapp_phone_number' => 'nullable|string|max:50',
            'whatsapp_api_url' => 'nullable|string|max:255',
            'whatsapp_api_key' => 'nullable|string|max:255',
            'whatsapp_webhook_verify_token' => 'nullable|string|max:255',
            'whatsapp_order_template' => 'nullable|string|max:1000',
            'whatsapp_payment_template' => 'nullable|string|max:1000',
            'whatsapp_chat_enabled' => 'nullable|boolean',
            'whatsapp_chat_number' => 'nullable|string|max:50',
            'whatsapp_chat_message' => 'nullable|string|max:1000',

            'whatsapp_bot_enabled' => 'nullable|boolean',
            'whatsapp_bot_persona_prompt' => 'nullable|string|max:4000',
            'whatsapp_bot_inherit_ai_persona' => 'nullable|boolean',
            'whatsapp_bot_business_hours_start' => 'nullable|string|max:20',
            'whatsapp_bot_business_hours_end' => 'nullable|string|max:20',
            'whatsapp_bot_outside_hours_message' => 'nullable|string|max:1000',
            'whatsapp_bot_escalation_keywords' => 'nullable|string|max:1000',
            'whatsapp_bot_fallback_message' => 'nullable|string|max:1000',
            'whatsapp_bot_max_auto_replies' => 'nullable|integer|min:1|max:10',

            'ai_provider' => 'nullable|string|max:50',
            'ai_model' => 'nullable|string|max:100',
            'ai_api_key' => 'nullable|string|max:255',
            'custom_integrations_api_key' => 'nullable|string|max:255',
            'ai_prompt_context' => 'nullable|string|max:4000',
            'ai_goal_text' => 'nullable|string|max:1000',
            'onesignal_enabled' => 'nullable|boolean',
            'onesignal_app_id' => 'nullable|string|max:100',
            'onesignal_rest_api_key' => 'nullable|string|max:100',
        ];
    }

    protected function textKeys(): array
    {
        return [
            'mail_mailer', 'mail_from_name', 'mail_from_address', 'mail_smtp_host', 'mail_smtp_port',
            'mail_smtp_username', 'mail_smtp_password', 'mail_smtp_encryption', 'mail_api_key', 'mail_api_secret',
            'order_notification_email', 'support_notification_email',
            'app_public_url', 'app_timezone', 'app_locale', 'support_email', 'support_phone',
            'company_address', 'company_tax_id', 'currency_code', 'currency_symbol', 'asset_cdn_url',
            'whatsapp_provider', 'whatsapp_phone_number', 'whatsapp_api_url', 'whatsapp_api_key', 'whatsapp_webhook_verify_token',
            'whatsapp_order_template', 'whatsapp_payment_template',
            'whatsapp_chat_number', 'whatsapp_chat_message',
            'whatsapp_bot_persona_prompt', 'whatsapp_bot_business_hours_start', 'whatsapp_bot_business_hours_end',
            'whatsapp_bot_outside_hours_message', 'whatsapp_bot_escalation_keywords', 'whatsapp_bot_fallback_message',
            'ai_provider', 'ai_model', 'ai_api_key', 'ai_prompt_context', 'ai_goal_text', 'custom_integrations_api_key',
            'onesignal_app_id', 'onesignal_rest_api_key',
        ];
    }

    protected function booleanKeys(): array
    {
        return [
            'force_https',
            'whatsapp_enabled',
            'whatsapp_chat_enabled',
            'whatsapp_bot_enabled',
            'whatsapp_bot_inherit_ai_persona',
            'ai_enabled',
            'ai_sales_tracking_enabled',
            'ai_inventory_guidance_enabled',
            'ai_management_guidance_enabled',
            'onesignal_enabled',
        ];
    }

    private function groupFor(string $key): string
    {
        if (str_starts_with($key, 'mail_') || str_contains($key, 'notification_email') || $key === 'test_email_recipient' || str_starts_with($key, 'onesignal_')) {
            return 'communications';
        }

        if (in_array($key, ['app_public_url', 'force_https', 'app_timezone', 'app_locale', 'support_email', 'support_phone', 'company_address', 'company_tax_id', 'currency_code', 'currency_symbol', 'asset_cdn_url'], true)) {
            return 'hosting';
        }

        if (str_starts_with($key, 'billing_')) {
            return 'billing';
        }

        if (str_starts_with($key, 'whatsapp_')) {
            return 'whatsapp';
        }

        if (str_starts_with($key, 'ai_')) {
            return 'ai';
        }

        return 'system';
    }

    public function render()
    {
        return view('livewire.admin.system-settings-manager');
    }
}
