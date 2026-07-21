<?php

namespace Tests\Feature\Http\Admin;

use App\Models\StorefrontPage;
use App\Models\StorefrontSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SectionFragmentApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $permission = Permission::firstOrCreate(['name' => 'view site management', 'guard_name' => 'web']);
        $this->adminUser = User::factory()->create();
        $this->adminUser->givePermissionTo($permission);
    }

    public function test_can_fetch_section_fragment(): void
    {
        $page = StorefrontPage::create(['key' => 'home', 'label' => 'Home']);
        $section = StorefrontSection::create([
            'page_id' => $page->id,
            'type' => 'hero',
            'order' => 0,
            'is_active' => true,
            'config' => ['heading' => 'Fragment Test Heading'],
            'slot' => 'before',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson("/admin/api/storefront-studio/sections/{$section->id}/fragment");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/html; charset=UTF-8');

        $this->assertStringContainsString('no-cache', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));

        $this->assertStringContainsString('Fragment Test Heading', $response->getContent());
    }
}
