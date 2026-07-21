<?php

namespace Tests\Feature\Http\Admin;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class BannerApiTest extends TestCase
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
        $response = $this->getJson('/admin/api/storefront-studio/banners');
        $this->assertTrue(in_array($response->status(), [401, 302]));
    }

    public function test_can_fetch_banners_list(): void
    {
        Banner::create([
            'title' => 'Test Banner 1',
            'position' => 'hero',
            'bg_color' => '#123456',
            'text_color' => '#ffffff',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/admin/api/storefront-studio/banners');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Test Banner 1');
    }

    public function test_can_create_banner_with_valid_data(): void
    {
        $payload = [
            'title' => 'Launch Promo Banner',
            'subtitle' => 'Hero spotlight',
            'caption' => 'Special launch discount',
            'button_text' => 'Shop Now',
            'button_link' => '/products',
            'position' => 'hero',
            'bg_color' => '#312e81',
            'text_color' => '#ffffff',
            'is_active' => true,
            'sort_order' => 1,
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson('/admin/api/storefront-studio/banners', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Launch Promo Banner')
            ->assertJsonPath('data.position', 'hero');

        $this->assertDatabaseHas('banners', [
            'title' => 'Launch Promo Banner',
            'position' => 'hero',
            'bg_color' => '#312e81',
        ]);
    }

    public function test_title_is_required(): void
    {
        $payload = [
            'position' => 'hero',
            'bg_color' => '#ffffff',
            'text_color' => '#000000',
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson('/admin/api/storefront-studio/banners', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_title_max_length_rule(): void
    {
        $payload = [
            'title' => str_repeat('a', 201),
            'position' => 'hero',
            'bg_color' => '#ffffff',
            'text_color' => '#000000',
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson('/admin/api/storefront-studio/banners', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_position_must_be_valid_enum(): void
    {
        $payload = [
            'title' => 'Valid Title',
            'position' => 'invalid_position',
            'bg_color' => '#ffffff',
            'text_color' => '#000000',
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson('/admin/api/storefront-studio/banners', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['position']);
    }

    public function test_ends_at_must_be_after_or_equal_starts_at(): void
    {
        $payload = [
            'title' => 'Scheduled Banner',
            'position' => 'promo',
            'bg_color' => '#ffffff',
            'text_color' => '#000000',
            'starts_at' => '2026-08-10 10:00:00',
            'ends_at' => '2026-08-01 10:00:00',
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson('/admin/api/storefront-studio/banners', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ends_at']);
    }

    public function test_can_update_banner(): void
    {
        $banner = Banner::create([
            'title' => 'Original Banner',
            'position' => 'sidebar',
            'bg_color' => '#ffffff',
            'text_color' => '#000000',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->putJson("/admin/api/storefront-studio/banners/{$banner->id}", [
                'title' => 'Updated Banner Title',
                'position' => 'top_bar',
                'bg_color' => '#111111',
                'text_color' => '#ffffff',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Banner Title')
            ->assertJsonPath('data.position', 'top_bar');

        $this->assertDatabaseHas('banners', [
            'id' => $banner->id,
            'title' => 'Updated Banner Title',
            'position' => 'top_bar',
        ]);
    }

    public function test_can_toggle_banner_active(): void
    {
        $banner = Banner::create([
            'title' => 'Toggle Test',
            'position' => 'hero',
            'bg_color' => '#ffffff',
            'text_color' => '#000000',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->patchJson("/admin/api/storefront-studio/banners/{$banner->id}/toggle");

        $response->assertStatus(200)
            ->assertJsonPath('data.is_active', false);

        $this->assertDatabaseHas('banners', [
            'id' => $banner->id,
            'is_active' => false,
        ]);
    }

    public function test_can_reorder_banners(): void
    {
        $b1 = Banner::create(['title' => 'B1', 'position' => 'hero', 'bg_color' => '#000', 'text_color' => '#fff', 'sort_order' => 0]);
        $b2 = Banner::create(['title' => 'B2', 'position' => 'hero', 'bg_color' => '#000', 'text_color' => '#fff', 'sort_order' => 1]);

        $response = $this->actingAs($this->adminUser)
            ->postJson('/admin/api/storefront-studio/banners/reorder', [
                'order' => [$b2->id, $b1->id],
            ]);

        $response->assertStatus(200);

        $this->assertEquals(0, $b2->fresh()->sort_order);
        $this->assertEquals(1, $b1->fresh()->sort_order);
    }

    public function test_can_delete_banner(): void
    {
        $banner = Banner::create([
            'title' => 'To Delete',
            'position' => 'promo',
            'bg_color' => '#ffffff',
            'text_color' => '#000000',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->deleteJson("/admin/api/storefront-studio/banners/{$banner->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('banners', [
            'id' => $banner->id,
        ]);
    }
}
