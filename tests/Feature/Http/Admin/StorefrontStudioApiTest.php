<?php

namespace Tests\Feature\Http\Admin;

use App\Models\User;
use App\Models\StorefrontPage;
use App\Models\StorefrontSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class StorefrontStudioApiTest extends TestCase
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

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $response = $this->getJson('/admin/api/storefront-studio/registry');

        $this->assertTrue(in_array($response->status(), [401, 302]));
    }

    public function test_can_fetch_registry(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->getJson('/admin/api/storefront-studio/registry');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'hero' => ['key', 'label', 'schema', 'defaults'],
                'banner-rail',
                'featured-products',
                'footer',
            ]);
    }

    public function test_can_fetch_page_layout(): void
    {
        $page = StorefrontPage::create(['key' => 'home', 'label' => 'Home']);
        StorefrontSection::create([
            'page_id' => $page->id,
            'type' => 'hero',
            'order' => 0,
            'is_active' => true,
            'config' => ['heading' => 'Test'],
            'slot' => 'before',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/admin/api/storefront-studio/layout/home');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_add_section(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson('/admin/api/storefront-studio/sections', [
                'page_key' => 'home',
                'type' => 'hero',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'hero');
    }

    public function test_can_update_section(): void
    {
        $page = StorefrontPage::create(['key' => 'home', 'label' => 'Home']);
        $section = StorefrontSection::create([
            'page_id' => $page->id,
            'type' => 'hero',
            'order' => 0,
            'is_active' => true,
            'config' => [],
            'slot' => 'before',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->patchJson("/admin/api/storefront-studio/sections/{$section->id}", [
                'config' => ['heading' => 'New Heading'],
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.config.heading', 'New Heading');
    }

    public function test_can_fetch_theme_settings(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->getJson('/admin/api/storefront-studio/theme');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'site_name',
                'site_tagline',
                'logo_path',
                'favicon_path',
                'primary_color',
                'secondary_color',
                'accent_color',
                'text_color',
                'bg_color',
                'nav_bg_color',
                'heading_font',
                'body_font',
            ]);
    }

    public function test_can_update_theme_settings(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->patchJson('/admin/api/storefront-studio/theme', [
                'site_name' => 'Display Lanka',
                'primary_color' => '#6d28d9',
                'heading_font' => 'Plus Jakarta Sans',
                'body_font' => 'Figtree',
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Theme settings updated']);

        $this->assertDatabaseHas('site_settings', [
            'key' => 'site_name',
            'value' => 'Display Lanka',
            'group' => 'branding',
        ]);

        $this->assertDatabaseHas('site_settings', [
            'key' => 'primary_color',
            'value' => '#6d28d9',
            'group' => 'appearance',
        ]);

        $this->assertDatabaseHas('site_settings', [
            'key' => 'heading_font',
            'value' => 'Plus Jakarta Sans',
            'group' => 'appearance',
        ]);
    }
}
