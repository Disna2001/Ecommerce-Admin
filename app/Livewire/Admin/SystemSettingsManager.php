<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting;
use App\Services\AuditLogService;
use App\Services\Billing\BillCustomizationService;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

class SystemSettingsManager extends Component
{
    public string $activeTab = 'communications';

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

    // WhatsApp
    public bool $whatsapp_enabled = false;

    public string $whatsapp_provider = 'meta_cloud';

    public string $whatsapp_phone_number = '';

    public string $whatsapp_api_url = '';

    public string $whatsapp_api_key = '';

    public string $whatsapp_webhook_verify_token = '';

    public string $whatsapp_order_template = 'Your order {order_number} has been placed successfully.';

    public string $whatsapp_payment_template = 'Payment update for order {order_number}: {payment_status}.';

    // AI
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
            'ai_enabled', 'ai_provider', 'ai_model', 'ai_api_key',
            'ai_sales_tracking_enabled', 'ai_inventory_guidance_enabled', 'ai_management_guidance_enabled',
            'ai_prompt_context', 'ai_goal_text', 'custom_integrations_api_key',
        ];

        foreach ($keys as $key) {
            $value = SiteSetting::get($key);
            if (! is_null($value)) {
                $this->$key = $value;
            }
        }

        $this->billing_profiles = $billCustomizationService->configuredProfiles();
        $this->billing_default_profiles = $billCustomizationService->configuredAssignments();
        $this->test_email_recipient = $this->support_notification_email ?: $this->mail_from_address;
    }

    public function save(AuditLogService $auditLogService, BillCustomizationService $billCustomizationService): void
    {
        $this->validate($this->rules());

        foreach ($this->textKeys() as $key) {
            SiteSetting::set($key, $this->$key, 'text', $this->groupFor($key));
        }

        foreach ($this->booleanKeys() as $key) {
            SiteSetting::set($key, $this->$key ? '1' : '0', 'boolean', $this->groupFor($key));
        }

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

        SiteSetting::set('billing_profiles', $this->billing_profiles, 'json', 'billing', 'Bill customization profiles');
        SiteSetting::set('billing_default_profiles', $this->billing_default_profiles, 'json', 'billing', 'Bill default profile assignments');

        $auditLogService->log(
            'settings.updated',
            null,
            'System settings updated from admin panel.',
            [
                'mail_mailer' => $this->mail_mailer,
                'app_public_url' => $this->app_public_url,
                'force_https' => $this->force_https,
                'whatsapp_enabled' => $this->whatsapp_enabled,
                'whatsapp_webhook_ready' => filled($this->whatsapp_webhook_verify_token),
                'ai_enabled' => $this->ai_enabled,
                'ai_model' => $this->ai_model,
                'billing_profile_count' => count($this->billing_profiles),
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

            'ai_provider' => 'nullable|string|max:50',
            'ai_model' => 'nullable|string|max:100',
            'ai_api_key' => 'nullable|string|max:255',
            'custom_integrations_api_key' => 'nullable|string|max:255',
            'ai_prompt_context' => 'nullable|string|max:4000',
            'ai_goal_text' => 'nullable|string|max:1000',

            'billing_profiles' => 'nullable|array',
            'billing_profiles.*.id' => 'required|string|max:100',
            'billing_profiles.*.name' => 'required|string|max:120',
            'billing_profiles.*.bill_type' => 'required|in:invoice_pdf,pos_receipt,any',
            'billing_profiles.*.output_mode' => 'required|in:pdf,browser_print,either',
            'billing_profiles.*.paper_size' => 'required|in:a4,letter,thermal_80,thermal_58',
            'billing_profiles.*.orientation' => 'required|in:portrait,landscape',
            'billing_profiles.*.device_match' => 'required|in:any,desktop,tablet,mobile',
            'billing_profiles.*.input_match' => 'required|in:any,keyboard_scanner,touch,manual',
            'billing_profiles.*.printer_match' => 'nullable|string|max:120',
            'billing_profiles.*.copies' => 'nullable|integer|min:1|max:5',
            'billing_profiles.*.font_scale' => 'nullable|numeric|min:0.7|max:1.4',
            'billing_profiles.*.header_note' => 'nullable|string|max:255',
            'billing_profiles.*.footer_note' => 'nullable|string|max:255',
            'billing_default_profiles.invoice_pdf' => 'nullable|string|max:100',
            'billing_default_profiles.pos_receipt' => 'nullable|string|max:100',
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
            'ai_provider', 'ai_model', 'ai_api_key', 'ai_prompt_context', 'ai_goal_text', 'custom_integrations_api_key',
        ];
    }

    protected function booleanKeys(): array
    {
        return [
            'force_https',
            'whatsapp_enabled',
            'ai_enabled',
            'ai_sales_tracking_enabled',
            'ai_inventory_guidance_enabled',
            'ai_management_guidance_enabled',
        ];
    }

    private function groupFor(string $key): string
    {
        if (str_starts_with($key, 'mail_') || str_contains($key, 'notification_email') || $key === 'test_email_recipient') {
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
        $statusCards = [
            'email_ready' => filled($this->mail_from_address) && filled($this->mail_mailer) && ($this->mail_mailer !== 'smtp' || filled($this->mail_smtp_host)),
            'whatsapp_enabled' => $this->whatsapp_enabled,
            'whatsapp_ready' => $this->whatsapp_enabled && filled($this->whatsapp_api_url) && filled($this->whatsapp_api_key) && filled($this->whatsapp_webhook_verify_token),
            'ai_enabled' => $this->ai_enabled,
            'ai_ready' => $this->ai_enabled && filled($this->ai_provider) && filled($this->ai_model) && filled($this->ai_api_key),
            'ops_contacts_ready' => filled($this->order_notification_email) && filled($this->support_notification_email),
            'hosting_ready' => filled($this->app_public_url) && filled($this->app_timezone) && filled($this->app_locale),
            'business_ready' => filled($this->support_email) && filled($this->support_phone) && filled($this->company_address),
            'billing_ready' => count($this->billing_profiles) > 0 && filled($this->billing_default_profiles['invoice_pdf'] ?? null) && filled($this->billing_default_profiles['pos_receipt'] ?? null),
        ];

        $checklist = [
            ['label' => 'Public URL and HTTPS', 'ready' => filled($this->app_public_url) && $this->force_https],
            ['label' => 'Storefront support contacts', 'ready' => filled($this->support_email) && filled($this->support_phone)],
            ['label' => 'Mail sender and ops alerts', 'ready' => $statusCards['email_ready'] && $statusCards['ops_contacts_ready']],
            ['label' => 'Timezone and locale', 'ready' => filled($this->app_timezone) && filled($this->app_locale)],
            ['label' => 'Currency display', 'ready' => filled($this->currency_code) && filled($this->currency_symbol)],
            ['label' => 'Bill profiles and printer routing', 'ready' => $statusCards['billing_ready']],
        ];

        $billingPreviewCompany = [
            'name' => $this->app_public_url !== '' ? parse_url($this->app_public_url, PHP_URL_HOST) ?: config('app.name') : config('app.name'),
            'display_name' => SiteSetting::get('site_name', config('app.name', 'Display Lanka')),
            'email' => $this->support_email ?: $this->mail_from_address ?: 'support@example.com',
            'phone' => $this->support_phone ?: '+94 70 000 0000',
            'address' => $this->company_address ?: 'Colombo, Sri Lanka',
            'tax_id' => $this->company_tax_id ?: 'Pending',
            'currency_symbol' => $this->currency_symbol ?: 'Rs',
        ];

        $billingPreviewDocuments = [
            'invoice' => [
                'number' => 'INV-2026-0042',
                'date' => now()->format('M d, Y'),
                'due_date' => now()->addDays(2)->format('M d, Y'),
                'status' => 'paid',
                'payment_method' => 'PayHere',
                'customer_name' => 'Dishna Chamuditha',
                'customer_email' => 'customer@example.com',
                'customer_phone' => '0702615076',
                'customer_address' => 'Weliwita, Ratnapura, Sri Lanka',
                'items' => [
                    ['name' => 'Netflix Premium Plan', 'description' => '1 month digital access', 'quantity' => 1, 'price' => 2490.00, 'discount' => 0, 'tax' => 0, 'total' => 2490.00],
                    ['name' => 'Spotify Family', 'description' => '30-day activation', 'quantity' => 1, 'price' => 1290.00, 'discount' => 0, 'tax' => 0, 'total' => 1290.00],
                ],
                'subtotal' => 3780.00,
                'tax_amount' => 0.00,
                'discount_amount' => 0.00,
                'total' => 3780.00,
                'amount_paid' => 3780.00,
                'balance_due' => 0.00,
                'notes' => 'Digital delivery completed to the customer email.',
                'terms' => 'Valid for the purchased digital product only. Keep this invoice for support requests.',
            ],
            'receipt' => [
                'number' => 'POS-0138',
                'status' => 'paid',
                'customer_name' => 'Walk-in customer',
                'customer_phone' => '0712345678',
                'payment_method' => 'Cash',
                'items' => [
                    ['name' => 'Gift Card Top-up', 'quantity' => 1, 'price' => 3500.00, 'total' => 3500.00],
                    ['name' => 'Service Fee', 'quantity' => 1, 'price' => 150.00, 'total' => 150.00],
                ],
                'total' => 3650.00,
                'notes' => 'Thank you for shopping with us.',
            ],
        ];

        return view('livewire.admin.system-settings-manager', [
            'permissionCount' => Permission::count(),
            'statusCards' => $statusCards,
            'billingPreviewCompany' => $billingPreviewCompany,
            'billingPreviewDocuments' => $billingPreviewDocuments,
            'integrationSummary' => [
                'enabled_channels' => collect([
                    $this->whatsapp_enabled ? 'WhatsApp' : null,
                    filled($this->mail_from_address) ? 'Email' : null,
                    $this->ai_enabled ? 'AI' : null,
                ])->filter()->values()->all(),
                'configured_secrets' => collect([
                    $this->mail_smtp_password,
                    $this->mail_api_key,
                    $this->mail_api_secret,
                    $this->whatsapp_api_key,
                    $this->ai_api_key,
                    $this->custom_integrations_api_key,
                ])->filter(fn ($value) => filled($value))->count(),
            ],
            'checklist' => $checklist,
        ]);
    }

    public function addBillingProfile(BillCustomizationService $billCustomizationService, string $billType = 'invoice_pdf'): void
    {
        $this->billing_profiles[] = $billCustomizationService->normalizeProfile([
            'id' => 'profile-'.str()->lower(str()->random(8)),
            'name' => $billType === 'pos_receipt' ? 'New POS Receipt Profile' : 'New Invoice PDF Profile',
            'bill_type' => $billType,
            'output_mode' => $billType === 'pos_receipt' ? 'browser_print' : 'pdf',
            'paper_size' => $billType === 'pos_receipt' ? 'thermal_80' : 'a4',
            'printer_match' => $billType === 'pos_receipt' ? 'Counter Thermal' : 'Office A4',
        ]);
    }

    public function removeBillingProfile(int $index): void
    {
        if (! isset($this->billing_profiles[$index])) {
            return;
        }

        $removedId = $this->billing_profiles[$index]['id'] ?? null;
        unset($this->billing_profiles[$index]);
        $this->billing_profiles = array_values($this->billing_profiles);

        foreach (['invoice_pdf', 'pos_receipt'] as $billType) {
            if (($this->billing_default_profiles[$billType] ?? null) === $removedId) {
                $this->billing_default_profiles[$billType] = $this->billing_profiles[0]['id'] ?? '';
            }
        }
    }

    public function resetBillingProfiles(BillCustomizationService $billCustomizationService): void
    {
        $this->billing_profiles = $billCustomizationService->defaultProfiles();
        $this->billing_default_profiles = $billCustomizationService->defaultAssignments();
    }
}
