<?php

namespace Tests\Feature\Http\Admin;

use App\Models\Category;
use App\Models\Review;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ReviewApiTest extends TestCase
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
        $response = $this->getJson('/admin/api/storefront-studio/reviews');
        $this->assertTrue(in_array($response->status(), [401, 302]));
    }

    public function test_can_fetch_reviews_list_and_stats(): void
    {
        $user = User::factory()->create(['name' => 'Customer A']);
        $this->createReview([
            'user_id' => $user->id,
            'rating' => 5,
            'title' => 'Great product',
            'body' => 'High quality display replacement.',
            'is_approved' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/admin/api/storefront-studio/reviews');

        $response->assertStatus(200)
            ->assertJsonPath('stats.total', 1)
            ->assertJsonPath('stats.approved', 1)
            ->assertJsonPath('data.0.body', 'High quality display replacement.');
    }

    public function test_can_approve_review(): void
    {
        $review = $this->createReview([
            'user_id' => $this->adminUser->id,
            'rating' => 4,
            'body' => 'Pending moderation review body text.',
            'is_approved' => false,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->postJson("/admin/api/storefront-studio/reviews/{$review->id}/approve");

        $response->assertStatus(200)
            ->assertJsonPath('data.is_approved', true);

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'is_approved' => true,
        ]);
    }

    public function test_can_reject_review(): void
    {
        $review = $this->createReview([
            'user_id' => $this->adminUser->id,
            'rating' => 5,
            'body' => 'Initially approved review text.',
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)
            ->postJson("/admin/api/storefront-studio/reviews/{$review->id}/reject");

        $response->assertStatus(200)
            ->assertJsonPath('data.is_approved', false);

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'is_approved' => false,
            'approved_at' => null,
        ]);
    }

    public function test_can_toggle_flag(): void
    {
        $review = $this->createReview([
            'user_id' => $this->adminUser->id,
            'rating' => 1,
            'body' => 'Suspicious spam review text.',
            'is_approved' => false,
            'is_flagged' => false,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->patchJson("/admin/api/storefront-studio/reviews/{$review->id}/toggle-flag");

        $response->assertStatus(200)
            ->assertJsonPath('data.is_flagged', true);

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'is_flagged' => true,
        ]);
    }

    public function test_can_update_review(): void
    {
        $review = $this->createReview([
            'user_id' => $this->adminUser->id,
            'rating' => 3,
            'body' => 'Original review body text.',
            'is_approved' => false,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->putJson("/admin/api/storefront-studio/reviews/{$review->id}", [
                'rating' => 5,
                'title' => 'Updated Title',
                'body' => 'Updated review body text that is long enough.',
                'is_approved' => true,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.rating', 5)
            ->assertJsonPath('data.title', 'Updated Title')
            ->assertJsonPath('data.is_approved', true);

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 5,
            'title' => 'Updated Title',
            'is_approved' => true,
        ]);
    }

    public function test_can_delete_review(): void
    {
        $review = $this->createReview([
            'user_id' => $this->adminUser->id,
            'rating' => 2,
            'body' => 'Delete me please.',
            'is_approved' => false,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->deleteJson("/admin/api/storefront-studio/reviews/{$review->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_can_perform_bulk_actions(): void
    {
        $r1 = $this->createReview(['user_id' => $this->adminUser->id, 'rating' => 5, 'body' => 'Bulk 1', 'is_approved' => false]);
        $r2 = $this->createReview(['user_id' => $this->adminUser->id, 'rating' => 4, 'body' => 'Bulk 2', 'is_approved' => false]);

        // Bulk approve
        $response = $this->actingAs($this->adminUser)
            ->postJson('/admin/api/storefront-studio/reviews/bulk', [
                'action' => 'approve',
                'ids' => [$r1->id, $r2->id],
            ]);

        $response->assertStatus(200)->assertJsonPath('count', 2);
        $this->assertTrue($r1->fresh()->is_approved);
        $this->assertTrue($r2->fresh()->is_approved);

        // Bulk reject
        $this->actingAs($this->adminUser)
            ->postJson('/admin/api/storefront-studio/reviews/bulk', [
                'action' => 'reject',
                'ids' => [$r1->id, $r2->id],
            ])->assertStatus(200);

        $this->assertFalse($r1->fresh()->is_approved);
        $this->assertFalse($r2->fresh()->is_approved);
    }

    // ==================== END-TO-END STOREFRONT TESTIMONIALS INTEGRATION TESTS ====================

    public function test_homepage_shows_fallback_quotes_when_zero_reviews_are_approved(): void
    {
        // 1. Create a pending (unapproved) review
        $this->createReview([
            'user_id' => $this->adminUser->id,
            'rating' => 5,
            'body' => 'Unapproved customer text',
            'is_approved' => false,
        ]);

        // 2. Load storefront homepage
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('Ordered a replacement screen')
            ->assertDontSee('Unapproved customer text');
    }

    public function test_approving_review_makes_it_appear_on_homepage_testimonials(): void
    {
        $customer = User::factory()->create(['name' => 'Amara Silva']);
        $review = $this->createReview([
            'user_id' => $customer->id,
            'rating' => 5,
            'title' => 'Excellent Display Assembly',
            'body' => 'Outstanding display assembly service and fast Colombo delivery!',
            'is_approved' => false,
        ]);

        // 1. Initially unapproved -> not on homepage
        $this->get('/')->assertDontSee('Outstanding display assembly service');

        // 2. Approve via Studio API
        $this->actingAs($this->adminUser)
            ->postJson("/admin/api/storefront-studio/reviews/{$review->id}/approve")
            ->assertStatus(200);

        // 3. Load homepage -> approved review now replaces fallback text!
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('Outstanding display assembly service')
            ->assertSee('Amara Silva');
    }

    public function test_rejecting_approved_review_removes_it_from_homepage_testimonials(): void
    {
        $customer = User::factory()->create(['name' => 'Nalin Perera']);
        $review = $this->createReview([
            'user_id' => $customer->id,
            'rating' => 5,
            'body' => 'Great genuine phone screen replacement!',
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        // 1. Currently approved -> appears on homepage
        $this->get('/')->assertSee('Great genuine phone screen replacement!');

        // 2. Reject/unpublish via Studio API
        $this->actingAs($this->adminUser)
            ->postJson("/admin/api/storefront-studio/reviews/{$review->id}/reject")
            ->assertStatus(200);

        // 3. Load homepage -> review gone, fallback quotes return
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertDontSee('Great genuine phone screen replacement!')
            ->assertSee('Ordered a replacement screen');
    }

    public function test_deleting_review_removes_it_from_homepage_testimonials(): void
    {
        $customer = User::factory()->create(['name' => 'Kasun Dissanayake']);
        $review = $this->createReview([
            'user_id' => $customer->id,
            'rating' => 5,
            'body' => 'Superb packaging and original parts!',
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        // 1. Delete review via Studio API
        $this->actingAs($this->adminUser)
            ->deleteJson("/admin/api/storefront-studio/reviews/{$review->id}")
            ->assertStatus(200);

        // 2. Load homepage -> review gone
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertDontSee('Superb packaging and original parts!')
            ->assertSee('Ordered a replacement screen');
    }

    protected function createReview(array $attributes = []): Review
    {
        if (!isset($attributes['stock_id'])) {
            $category = Category::create([
                'name' => 'General ' . Str::random(4),
                'slug' => 'gen-' . Str::random(6),
            ]);

            $supplier = Supplier::create([
                'name' => 'Supplier ' . Str::random(4),
                'email' => 'supp_' . Str::random(5) . '@example.com',
            ]);

            $stock = Stock::create([
                'category_id' => $category->id,
                'supplier_id' => $supplier->id,
                'sku' => 'SKU-' . Str::random(8),
                'item_code' => 'ITM-' . Str::random(8),
                'name' => 'Review Stock Item',
                'unit_price' => 1000,
                'selling_price' => 2000,
                'quantity' => 10,
                'status' => 'active',
            ]);

            $attributes['stock_id'] = $stock->id;
        }

        return Review::create(array_merge([
            'rating' => 5,
            'body' => 'Sample review text.',
            'is_approved' => false,
        ], $attributes));
    }
}
