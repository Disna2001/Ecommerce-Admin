<?php

namespace Tests\Feature\Http\Admin;

use App\Models\AutomatedDiscountRule;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Stock;
use App\Models\User;
use App\Services\Storefront\ProductPricingService;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DiscountApiTest extends TestCase
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
        $response = $this->getJson('/admin/api/storefront-studio/discounts');
        $this->assertTrue(in_array($response->status(), [401, 302]));
    }

    public function test_can_fetch_discounts_list_and_stats(): void
    {
        Discount::create([
            'name' => 'Promo 1',
            'code' => 'PROMO1',
            'type' => 'percentage',
            'value' => 10,
            'scope' => 'all',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/admin/api/storefront-studio/discounts');

        $response->assertStatus(200)
            ->assertJsonPath('stats.total', 1)
            ->assertJsonPath('stats.coupons', 1)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_create_coupon_discount(): void
    {
        $payload = [
            'name' => 'Summer Sale',
            'code' => 'SUMMER20',
            'type' => 'percentage',
            'value' => 20,
            'min_order_amount' => 1000,
            'max_discount_amount' => 500,
            'scope' => 'all',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson('/admin/api/storefront-studio/discounts', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Summer Sale')
            ->assertJsonPath('data.code', 'SUMMER20');

        $this->assertDatabaseHas('discounts', [
            'name' => 'Summer Sale',
            'code' => 'SUMMER20',
            'value' => 20,
        ]);
    }

    public function test_discount_validation_rules(): void
    {
        // 1. Name is required
        $res = $this->actingAs($this->adminUser)
            ->postJson('/admin/api/storefront-studio/discounts', [
                'type' => 'percentage',
                'value' => 10,
                'scope' => 'all',
            ]);
        $res->assertStatus(422)->assertJsonValidationErrors(['name']);

        // 2. Unique coupon code
        Discount::create([
            'name' => 'Existing Code',
            'code' => 'DUPLICATE',
            'type' => 'fixed',
            'value' => 100,
            'scope' => 'all',
        ]);

        $res2 = $this->actingAs($this->adminUser)
            ->postJson('/admin/api/storefront-studio/discounts', [
                'name' => 'New Discount',
                'code' => 'DUPLICATE',
                'type' => 'percentage',
                'value' => 10,
                'scope' => 'all',
            ]);
        $res2->assertStatus(422)->assertJsonValidationErrors(['code']);

        // 3. Ends at must be after or equal starts at
        $res3 = $this->actingAs($this->adminUser)
            ->postJson('/admin/api/storefront-studio/discounts', [
                'name' => 'Invalid Dates',
                'type' => 'percentage',
                'value' => 10,
                'scope' => 'all',
                'starts_at' => '2026-08-10 10:00:00',
                'ends_at'   => '2026-08-01 10:00:00',
            ]);
        $res3->assertStatus(422)->assertJsonValidationErrors(['ends_at']);
    }

    public function test_can_generate_coupon_code(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson('/admin/api/storefront-studio/discounts/generate-code');

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('code'));
        $this->assertEquals(8, strlen($response->json('code')));
    }

    public function test_can_update_discount(): void
    {
        $discount = Discount::create([
            'name' => 'Old Name',
            'type' => 'percentage',
            'value' => 10,
            'scope' => 'all',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->putJson("/admin/api/storefront-studio/discounts/{$discount->id}", [
                'name' => 'Updated Name',
                'type' => 'fixed',
                'value' => 250,
                'scope' => 'all',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Name')
            ->assertJsonPath('data.type', 'fixed');

        $this->assertDatabaseHas('discounts', [
            'id' => $discount->id,
            'name' => 'Updated Name',
            'value' => 250,
        ]);
    }

    public function test_can_toggle_discount_active(): void
    {
        $discount = Discount::create([
            'name' => 'Active Toggle Test',
            'type' => 'percentage',
            'value' => 10,
            'scope' => 'all',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->patchJson("/admin/api/storefront-studio/discounts/{$discount->id}/toggle");

        $response->assertStatus(200)
            ->assertJsonPath('data.is_active', false);

        $this->assertDatabaseHas('discounts', [
            'id' => $discount->id,
            'is_active' => false,
        ]);
    }

    public function test_can_delete_discount(): void
    {
        $discount = Discount::create([
            'name' => 'To Delete',
            'type' => 'percentage',
            'value' => 10,
            'scope' => 'all',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->deleteJson("/admin/api/storefront-studio/discounts/{$discount->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('discounts', ['id' => $discount->id]);
    }

    public function test_can_fetch_and_update_automated_discount_rules(): void
    {
        $showRes = $this->actingAs($this->adminUser)
            ->getJson('/admin/api/storefront-studio/automated-discounts');

        $showRes->assertStatus(200)
            ->assertJsonStructure(['rule', 'daily_discounts', 'daily_stats']);

        $updateRes = $this->actingAs($this->adminUser)
            ->patchJson('/admin/api/storefront-studio/automated-discounts', [
                'is_active' => true,
                'min_margin_percent' => 15,
                'max_discount_percent' => 25,
                'daily_items_limit' => 12,
                'rotation_strategy' => 'overstocked',
            ]);

        $updateRes->assertStatus(200)
            ->assertJsonPath('rule.min_margin_percent', 15)
            ->assertJsonPath('rule.rotation_strategy', 'overstocked');

        $this->assertDatabaseHas('automated_discount_rules', [
            'min_margin_percent' => 15,
            'rotation_strategy' => 'overstocked',
        ]);
    }

    public function test_can_trigger_automated_discount_orchestration(): void
    {
        AutomatedDiscountRule::create([
            'is_active' => true,
            'min_margin_percent' => 10,
            'max_discount_percent' => 30,
            'daily_items_limit' => 5,
            'rotation_strategy' => 'random',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->postJson('/admin/api/storefront-studio/automated-discounts/orchestrate');

        $response->assertStatus(200);
    }

    // ==================== END-TO-END CHECKOUT PRICING TESTS ====================

    public function test_created_coupon_applies_correctly_in_cart_and_checkout_pricing(): void
    {
        // 1. Create a coupon discount via Studio API (Fixed Rs 200 off)
        $this->actingAs($this->adminUser)
            ->postJson('/admin/api/storefront-studio/discounts', [
                'name' => 'Save 200 Coupon',
                'code' => 'SAVE200',
                'type' => 'fixed',
                'value' => 200,
                'min_order_amount' => 500,
                'scope' => 'all',
                'is_active' => true,
            ])->assertStatus(201);

        // 2. Setup storefront stock item (Price: Rs 1000)
        $stock = $this->createStockItem(sellingPrice: 1000);

        // 3. Put product into session cart
        session([
            'cart' => [
                $stock->id => [
                    'id' => $stock->id,
                    'name' => $stock->name,
                    'price' => 1000,
                    'original_price' => 1000,
                    'quantity' => 1,
                ]
            ]
        ]);

        // 4. Test Cart component coupon application
        Livewire::test('shop.cart')
            ->set('couponCode', 'SAVE200')
            ->call('applyCoupon')
            ->assertSet('couponError', false)
            ->assertSet('couponApplied', true);

        $this->assertEquals(200, session('cart_discount'));

        // 5. Verify Checkout calculates subtotal (1000) - discount (200) + shipping (350) = 1150 total
        Livewire::test('shop.checkout')
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->set('phone', '0771234567')
            ->set('address', '123 Main St')
            ->set('city', 'Colombo')
            ->set('postal_code', '00100')
            ->set('payment_method', 'cod')
            ->call('placeOrder');

        $this->assertDatabaseHas('orders', [
            'customer_email' => 'john@example.com',
            'subtotal' => 1000,
            'discount' => 200,
            'shipping_fee' => 350,
            'total' => 1150,
        ]);
    }

    public function test_expired_or_deactivated_discount_is_rejected_in_checkout(): void
    {
        // 1. Create discount via Studio API
        $createRes = $this->actingAs($this->adminUser)
            ->postJson('/admin/api/storefront-studio/discounts', [
                'name' => 'Deactivated Coupon',
                'code' => 'DISABLED10',
                'type' => 'percentage',
                'value' => 10,
                'scope' => 'all',
                'is_active' => true,
            ]);
        $createRes->assertStatus(201);
        $discountId = $createRes->json('data.id');

        // 2. Deactivate the coupon via Studio API
        $this->actingAs($this->adminUser)
            ->patchJson("/admin/api/storefront-studio/discounts/{$discountId}/toggle")
            ->assertStatus(200)
            ->assertJsonPath('data.is_active', false);

        // 3. Put product in cart and try applying deactivated code
        session(['cart' => [1 => ['id' => 1, 'name' => 'Item', 'price' => 1000, 'quantity' => 1]]]);

        Livewire::test('shop.cart')
            ->set('couponCode', 'DISABLED10')
            ->call('applyCoupon')
            ->assertSet('couponError', true)
            ->assertSet('couponApplied', false);

        $this->assertEquals(0, session('cart_discount', 0));
    }

    public function test_automatic_scope_discount_applies_to_product_pricing_on_storefront_and_cart(): void
    {
        $category = Category::create(['name' => 'Electronics', 'slug' => 'electronics']);
        $stock = $this->createStockItem(sellingPrice: 100000, categoryId: $category->id);

        // Create an automatic category discount via Studio API (20% off all Electronics)
        $this->actingAs($this->adminUser)
            ->postJson('/admin/api/storefront-studio/discounts', [
                'name' => 'Electronics 20% Sale',
                'type' => 'percentage',
                'value' => 20,
                'scope' => 'category',
                'scope_id' => $category->id,
                'is_active' => true,
            ])->assertStatus(201);

        // Resolve product pricing
        $pricingService = app(ProductPricingService::class);
        $finalPrice = $pricingService->finalPriceForProduct($stock);

        // 100,000 - 20% = 80,000
        $this->assertEquals(80000.0, $finalPrice);
    }

    protected function createStockItem(int $sellingPrice = 1000, ?int $categoryId = null): Stock
    {
        $category = $categoryId ? Category::find($categoryId) : Category::create([
            'name' => 'General',
            'slug' => 'general-' . Str::random(5),
        ]);

        $supplier = \App\Models\Supplier::create([
            'name' => 'Supplier ' . Str::random(5),
            'email' => 'supplier_' . Str::random(5) . '@example.com',
        ]);

        return Stock::create([
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'sku' => 'SKU-' . Str::random(8),
            'item_code' => 'ITM-' . Str::random(8),
            'name' => 'Test Stock Item',
            'unit_price' => $sellingPrice * 0.5,
            'selling_price' => $sellingPrice,
            'quantity' => 10,
            'reorder_level' => 1,
            'status' => 'active',
            'storefront_enabled' => true,
            'storefront_quantity' => 10,
        ]);
    }
}
