<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ItemType;
use App\Models\Make;
use App\Models\SiteSetting;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\Warranty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StarterCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()->where('is_default', true)->first() ?? Tenant::query()->first();

        if (!$tenant) {
            $this->command?->warn('No tenant found. Skipping starter catalog seed.');

            return;
        }

        $category = Category::withoutGlobalScopes()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'subscriptions'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Subscriptions',
                'slug' => 'subscriptions',
                'description' => 'Digital subscription products and access packages.',
            ]
        );

        $make = Make::withoutGlobalScopes()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'DIGITAL'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Digital',
                'code' => 'DIGITAL',
                'description' => 'Digital delivery catalog items.',
                'country_of_origin' => 'Sri Lanka',
                'website' => 'https://displaylanka.shop',
                'is_active' => true,
            ]
        );

        $brand = Brand::withoutGlobalScopes()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'display-lanka'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Display Lanka',
                'slug' => 'display-lanka',
                'description' => 'Primary in-house storefront catalog brand.',
                'logo' => null,
                'website' => 'https://displaylanka.shop',
                'status' => 'active',
            ]
        );

        $itemType = ItemType::withoutGlobalScopes()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'digital-account'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Digital Account',
                'slug' => 'digital-account',
                'description' => 'Delivered account-based digital products.',
                'status' => 'active',
            ]
        );

        $supplier = Supplier::withoutGlobalScopes()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'email' => 'catalog@displaylanka.shop'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Display Lanka Catalog',
                'email' => 'catalog@displaylanka.shop',
                'phone' => '+94 70 000 0000',
                'address' => 'Sri Lanka',
                'company' => 'Display Lanka',
                'contact_person' => 'Catalog Ops',
                'tax_number' => null,
                'payment_terms' => 'Internal',
                'status' => 'active',
            ]
        );

        $warranty = Warranty::withoutGlobalScopes()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Activation Support'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Activation Support',
                'type' => 'service',
                'duration' => 1,
                'terms' => 'Includes onboarding and activation assistance for the delivered product.',
                'coverage' => 'Activation support only',
                'status' => 'active',
            ]
        );

        $stock = Stock::withoutGlobalScopes()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'item_code' => 'DLK-NET-1M'],
            [
                'tenant_id' => $tenant->id,
                'sku' => 'SKU-DLK-NET-1M',
                'item_code' => 'DLK-NET-1M',
                'name' => 'Netflix Premium 1 Month',
                'description' => 'Starter product for validating checkout, catalog, and storefront rendering.',
                'category_id' => $category->id,
                'make_id' => $make->id,
                'brand_id' => $brand->id,
                'item_type_id' => $itemType->id,
                'supplier_id' => $supplier->id,
                'warranty_id' => $warranty->id,
                'quantity' => 25,
                'reorder_level' => 5,
                'unit_price' => 2500.00,
                'selling_price' => 2990.00,
                'wholesale_price' => 2500.00,
                'location' => 'Digital Fulfillment',
                'barcode' => 'DLK-NET-1M',
                'status' => 'active',
                'model_name' => 'Premium Monthly Access',
                'model_number' => 'NET-1M',
                'color' => null,
                'size' => null,
                'weight' => null,
                'specifications' => [
                    'delivery' => 'Instant',
                    'region' => 'Sri Lanka',
                    'support' => 'Included',
                ],
                'images' => [],
                'videos' => [],
                'tags' => 'netflix,premium,subscription,digital',
                'notes' => 'Seeded starter product for deployment QA.',
                'quality_level' => null,
                'target_category_id' => null,
                'target_item_type_id' => null,
                'target_make_id' => null,
                'target_brand_id' => null,
                'target_model' => null,
                'target_model_number' => null,
            ]
        );

        foreach ([
            'featured_product_ids' => [$stock->id],
            'new_arrivals_ids' => [$stock->id],
            'deal_product_ids' => [$stock->id],
        ] as $key => $value) {
            SiteSetting::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'key' => $key],
                [
                    'tenant_id' => $tenant->id,
                    'key' => $key,
                    'value' => json_encode($value),
                    'type' => 'json',
                    'group' => 'display',
                    'label' => Str::headline($key),
                ]
            );
        }

        $this->command?->info('Starter catalog seeded successfully.');
    }
}
