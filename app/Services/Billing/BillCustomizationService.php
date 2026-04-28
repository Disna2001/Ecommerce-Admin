<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

class BillCustomizationService
{
    public function defaultProfiles(): array
    {
        return [
            [
                'id' => 'invoice-a4-default',
                'name' => 'Invoice PDF - A4',
                'bill_type' => 'invoice_pdf',
                'enabled' => true,
                'output_mode' => 'pdf',
                'paper_size' => 'a4',
                'orientation' => 'portrait',
                'device_match' => 'desktop',
                'input_match' => 'any',
                'printer_match' => 'Office A4',
                'auto_print' => false,
                'copies' => 1,
                'font_scale' => '1.00',
                'header_note' => '',
                'footer_note' => 'Thank you for your business.',
                'show_company_phone' => true,
                'show_tax_id' => true,
                'show_customer_address' => true,
                'show_customer_email' => true,
                'show_customer_phone' => true,
                'show_payment_method' => true,
                'show_notes' => true,
                'show_terms' => true,
            ],
            [
                'id' => 'pos-thermal-default',
                'name' => 'POS Receipt - Thermal 80mm',
                'bill_type' => 'pos_receipt',
                'enabled' => true,
                'output_mode' => 'browser_print',
                'paper_size' => 'thermal_80',
                'orientation' => 'portrait',
                'device_match' => 'desktop',
                'input_match' => 'keyboard_scanner',
                'printer_match' => 'Counter Thermal',
                'auto_print' => true,
                'copies' => 1,
                'font_scale' => '0.92',
                'header_note' => 'Counter sale receipt',
                'footer_note' => 'Items can be exchanged only with this receipt.',
                'show_company_phone' => true,
                'show_tax_id' => false,
                'show_customer_address' => false,
                'show_customer_email' => false,
                'show_customer_phone' => true,
                'show_payment_method' => true,
                'show_notes' => true,
                'show_terms' => false,
            ],
        ];
    }

    public function defaultAssignments(): array
    {
        return [
            'invoice_pdf' => 'invoice-a4-default',
            'pos_receipt' => 'pos-thermal-default',
        ];
    }

    public function configuredProfiles(): array
    {
        $stored = SiteSetting::get('billing_profiles', []);

        if (! is_array($stored) || $stored === []) {
            return $this->defaultProfiles();
        }

        return array_values(array_map(
            fn (array $profile) => $this->normalizeProfile($profile),
            array_filter($stored, 'is_array')
        ));
    }

    public function configuredAssignments(): array
    {
        $stored = SiteSetting::get('billing_default_profiles', []);

        if (! is_array($stored)) {
            return $this->defaultAssignments();
        }

        return array_merge($this->defaultAssignments(), $stored);
    }

    public function resolveProfile(string $billType, array $context = []): array
    {
        $profiles = collect($this->configuredProfiles())
            ->filter(fn (array $profile) => $profile['enabled'])
            ->filter(fn (array $profile) => in_array($profile['bill_type'], [$billType, 'any'], true))
            ->values();

        if ($profiles->isEmpty()) {
            return $this->fallbackProfile($billType);
        }

        $assignments = $this->configuredAssignments();
        $defaultId = $assignments[$billType] ?? null;
        $context = array_merge([
            'device_type' => 'desktop',
            'input_mode' => 'any',
            'printer_hint' => '',
        ], $context);

        $scored = $profiles->map(function (array $profile) use ($billType, $defaultId, $context) {
            $score = 0;

            if ($profile['id'] === $defaultId) {
                $score += 50;
            }

            if ($profile['bill_type'] === $billType) {
                $score += 30;
            }

            $score += $this->matchScore($profile['device_match'], $context['device_type'], 18);
            $score += $this->matchScore($profile['input_match'], $context['input_mode'], 12);
            $score += $this->printerScore($profile['printer_match'], $context['printer_hint']);

            return [
                'profile' => $profile,
                'score' => $score,
            ];
        })->sortByDesc('score')->values();

        return $scored->first()['profile'] ?? $this->fallbackProfile($billType);
    }

    public function fallbackProfile(string $billType): array
    {
        return collect($this->defaultProfiles())
            ->first(fn (array $profile) => in_array($profile['bill_type'], [$billType, 'any'], true))
            ?? $this->normalizeProfile([]);
    }

    public function companyPayload(): array
    {
        $logoPath = SiteSetting::get('logo_path', '');
        $logoFullPath = '';
        $logoDataUri = '';

        if (filled($logoPath)) {
            try {
                $candidate = Storage::disk('public')->path($logoPath);
                if (is_string($candidate) && is_file($candidate)) {
                    $logoFullPath = $candidate;
                    $mimeType = mime_content_type($candidate) ?: 'image/png';
                    $logoDataUri = 'data:'.$mimeType.';base64,'.base64_encode((string) file_get_contents($candidate));
                }
            } catch (\Throwable) {
                $logoFullPath = '';
                $logoDataUri = '';
            }
        }

        return [
            'name' => SiteSetting::get('site_name', config('app.name', 'Display Lanka')),
            'email' => SiteSetting::get('support_email', config('mail.from.address', 'company@example.com')),
            'phone' => SiteSetting::get('support_phone', '+94 11 234 5678'),
            'address' => SiteSetting::get('company_address', 'Sri Lanka'),
            'tax_id' => SiteSetting::get('company_tax_id', 'N/A'),
            'currency' => SiteSetting::get('currency_code', 'LKR'),
            'currency_symbol' => SiteSetting::get('currency_symbol', 'Rs'),
            'logo_path' => $logoPath,
            'logo_full_path' => $logoFullPath,
            'logo_data_uri' => $logoDataUri,
        ];
    }

    public function invoiceViewData(Invoice $invoice, array $context = []): array
    {
        return [
            'invoice' => $invoice,
            'company' => $this->companyPayload(),
            'billProfile' => $this->resolveProfile('invoice_pdf', $context),
        ];
    }

    public function paperConfig(array $profile): array|string
    {
        return match ($profile['paper_size']) {
            'letter' => 'letter',
            'thermal_58' => [0, 0, $this->mmToPoints(58), $this->mmToPoints(420)],
            'thermal_80' => [0, 0, $this->mmToPoints(80), $this->mmToPoints(420)],
            default => 'a4',
        };
    }

    public function paperOrientation(array $profile): string
    {
        return in_array($profile['orientation'], ['portrait', 'landscape'], true)
            ? $profile['orientation']
            : 'portrait';
    }

    public function normalizeProfile(array $profile): array
    {
        $defaults = [
            'id' => 'profile-'.substr(md5(json_encode($profile)), 0, 8),
            'name' => 'Billing Profile',
            'bill_type' => 'invoice_pdf',
            'enabled' => true,
            'output_mode' => 'pdf',
            'paper_size' => 'a4',
            'orientation' => 'portrait',
            'device_match' => 'any',
            'input_match' => 'any',
            'printer_match' => '',
            'auto_print' => false,
            'copies' => 1,
            'font_scale' => '1.00',
            'header_note' => '',
            'footer_note' => '',
            'show_company_phone' => true,
            'show_tax_id' => true,
            'show_customer_address' => true,
            'show_customer_email' => true,
            'show_customer_phone' => true,
            'show_payment_method' => true,
            'show_notes' => true,
            'show_terms' => true,
        ];

        $normalized = array_merge($defaults, $profile);
        $normalized['enabled'] = (bool) $normalized['enabled'];
        $normalized['auto_print'] = (bool) $normalized['auto_print'];
        $normalized['copies'] = max(1, (int) $normalized['copies']);
        $normalized['font_scale'] = number_format(max(0.7, min(1.4, (float) $normalized['font_scale'])), 2, '.', '');

        foreach ([
            'show_company_phone',
            'show_tax_id',
            'show_customer_address',
            'show_customer_email',
            'show_customer_phone',
            'show_payment_method',
            'show_notes',
            'show_terms',
        ] as $key) {
            $normalized[$key] = (bool) $normalized[$key];
        }

        return $normalized;
    }

    protected function matchScore(string $expected, string $actual, int $exactScore): int
    {
        if ($expected === 'any') {
            return 4;
        }

        return $expected === $actual ? $exactScore : -20;
    }

    protected function printerScore(string $expected, string $actual): int
    {
        $expected = trim(mb_strtolower($expected));
        $actual = trim(mb_strtolower($actual));

        if ($expected === '') {
            return 3;
        }

        if ($actual !== '' && str_contains($actual, $expected)) {
            return 22;
        }

        return -10;
    }

    protected function mmToPoints(float $millimeters): float
    {
        return $millimeters * 2.8346456693;
    }
}
