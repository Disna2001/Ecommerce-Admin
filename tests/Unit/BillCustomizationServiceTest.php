<?php

namespace Tests\Unit;

use App\Models\SiteSetting;
use App\Services\Billing\BillCustomizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillCustomizationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_default_profiles_when_no_settings_exist(): void
    {
        $service = app(BillCustomizationService::class);
        $profiles = $service->configuredProfiles();

        $this->assertCount(2, $profiles);
        $this->assertSame('invoice-a4-default', $profiles[0]['id']);
        $this->assertSame('pos-thermal-default', $profiles[1]['id']);
    }

    public function test_it_prefers_matching_printer_device_and_input_profile(): void
    {
        SiteSetting::set('billing_profiles', [
            [
                'id' => 'office-a4',
                'name' => 'Office A4',
                'bill_type' => 'invoice_pdf',
                'enabled' => true,
                'output_mode' => 'pdf',
                'paper_size' => 'a4',
                'orientation' => 'portrait',
                'device_match' => 'desktop',
                'input_match' => 'manual',
                'printer_match' => 'Office A4',
            ],
            [
                'id' => 'counter-thermal',
                'name' => 'Counter Thermal',
                'bill_type' => 'pos_receipt',
                'enabled' => true,
                'output_mode' => 'browser_print',
                'paper_size' => 'thermal_80',
                'orientation' => 'portrait',
                'device_match' => 'desktop',
                'input_match' => 'keyboard_scanner',
                'printer_match' => 'Counter Thermal',
            ],
        ], 'json', 'billing');

        SiteSetting::set('billing_default_profiles', [
            'invoice_pdf' => 'office-a4',
            'pos_receipt' => 'counter-thermal',
        ], 'json', 'billing');

        $service = app(BillCustomizationService::class);
        $profile = $service->resolveProfile('pos_receipt', [
            'device_type' => 'desktop',
            'input_mode' => 'keyboard_scanner',
            'printer_hint' => 'Front Counter Thermal Printer',
        ]);

        $this->assertSame('counter-thermal', $profile['id']);
        $this->assertSame('thermal_80', $profile['paper_size']);
    }
}
