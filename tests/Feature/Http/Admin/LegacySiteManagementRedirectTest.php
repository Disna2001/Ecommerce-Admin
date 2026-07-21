<?php

namespace Tests\Feature\Http\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class LegacySiteManagementRedirectTest extends TestCase
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

    public function test_index_renders_studio(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.site-management.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.site-management.studio');
    }

    public function test_legacy_appearance_redirects_to_studio_theme_tab(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.site-management.appearance'));

        $response->assertRedirect(route('admin.site-management.index', ['tab' => 'theme']))
            ->assertSessionHas('warning');
    }

    public function test_legacy_banners_redirects_to_studio_banners_tab(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.site-management.banners'));

        $response->assertRedirect(route('admin.site-management.index', ['tab' => 'banners']))
            ->assertSessionHas('warning');
    }

    public function test_legacy_discounts_redirects_to_studio_discounts_tab(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.site-management.discounts'));

        $response->assertRedirect(route('admin.site-management.index', ['tab' => 'discounts']))
            ->assertSessionHas('warning');
    }

    public function test_legacy_reviews_redirects_to_studio_reviews_tab(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.site-management.reviews'));

        $response->assertRedirect(route('admin.site-management.index', ['tab' => 'reviews']))
            ->assertSessionHas('warning');
    }
}
