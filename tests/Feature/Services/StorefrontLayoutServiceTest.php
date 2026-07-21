<?php

namespace Tests\Feature\Services;

use App\Models\StorefrontPage;
use App\Models\StorefrontSection;
use App\Services\Storefront\SectionRegistry;
use App\Services\Storefront\StorefrontLayoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontLayoutServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StorefrontLayoutService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(StorefrontLayoutService::class);
    }

    public function test_can_add_section_to_page(): void
    {
        $section = $this->service->addSection('home', 'hero');

        $this->assertInstanceOf(StorefrontSection::class, $section);
        $this->assertEquals('hero', $section->type);
        $this->assertEquals('before', $section->slot);
        $this->assertDatabaseHas('storefront_sections', [
            'id' => $section->id,
            'type' => 'hero',
        ]);
    }

    public function test_can_update_section_config(): void
    {
        $section = $this->service->addSection('home', 'hero');

        $updated = $this->service->updateSection($section->id, [
            'heading' => 'Updated Heading Test',
        ]);

        $this->assertEquals('Updated Heading Test', $updated->config['heading']);
    }

    public function test_can_reorder_sections(): void
    {
        $s1 = $this->service->addSection('home', 'hero');
        $s2 = $this->service->addSection('home', 'banner-rail');

        $this->service->reorderSections('home', [$s2->id, $s1->id]);

        $this->assertEquals(0, $s2->fresh()->order);
        $this->assertEquals(1, $s1->fresh()->order);
    }

    public function test_can_toggle_section_active_status(): void
    {
        $section = $this->service->addSection('home', 'hero');

        $toggled = $this->service->toggleSection($section->id, false);

        $this->assertFalse($toggled->is_active);
    }

    public function test_can_delete_section(): void
    {
        $section = $this->service->addSection('home', 'hero');

        $this->service->deleteSection($section->id);

        $this->assertDatabaseMissing('storefront_sections', [
            'id' => $section->id,
        ]);
    }
}
