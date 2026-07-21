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
use App\Services\Tenancy\TenantManager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StorefrontDisplaysSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Resolve tenant context
        $tenant = Tenant::query()->where('is_default', true)->first() ?? Tenant::query()->first();
        if (!$tenant) {
            $this->command?->warn('No tenant found. Skipping.');
            return;
        }
        app(TenantManager::class)->setCurrent($tenant);

        // 2. Scan public storage directory for available stock images
        $images = [];
        $imageFiles = glob(storage_path('app/public/stock-images/*.*'));
        if ($imageFiles) {
            foreach ($imageFiles as $file) {
                $images[] = 'stock-images/' . basename($file);
            }
        }

        // 3. Create or update phone display brands
        $brandsData = [
            'samsung' => 'Samsung',
            'apple' => 'Apple',
            'xiaomi' => 'Xiaomi',
            'oneplus' => 'OnePlus',
            'oppo' => 'Oppo',
            'vivo' => 'Vivo',
            'huawei' => 'Huawei',
        ];
        $brands = [];
        foreach ($brandsData as $slug => $name) {
            $brands[$slug] = Brand::updateOrCreate(
                ['slug' => $slug],
                [
                    'tenant_id' => $tenant->id,
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $name . ' replacement parts and displays.',
                    'status' => 'active',
                ]
            );
        }

        // 4. Create or update Display Category
        $category = Category::updateOrCreate(
            ['slug' => 'display'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Display',
                'slug' => 'display',
                'description' => 'Mobile phone display panels and assemblies.',
            ]
        );

        // 5. Create or update Make
        $make = Make::updateOrCreate(
            ['code' => 'SUNL'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Sunlong',
                'code' => 'SUNL',
                'description' => 'OEM display manufacturer.',
                'country_of_origin' => 'China',
                'website' => 'https://displaylanka.shop',
                'is_active' => true,
            ]
        );

        // 6. Create or update Item Types (Oled & Lcd)
        $itemTypes = [];
        foreach (['oled' => 'Oled', 'lcd' => 'Lcd'] as $slug => $name) {
            $itemTypes[$slug] = ItemType::updateOrCreate(
                ['slug' => $slug],
                [
                    'tenant_id' => $tenant->id,
                    'name' => $name,
                    'slug' => $slug,
                    'status' => 'active',
                ]
            );
        }

        // 7. Create or update Supplier
        $supplier = Supplier::updateOrCreate(
            ['email' => 'catalog@displaylanka.shop'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Display Lanka Catalog',
                'email' => 'catalog@displaylanka.shop',
                'phone' => '+94 70 000 0000',
                'address' => 'Sri Lanka',
                'company' => 'Display Lanka',
                'contact_person' => 'Catalog Ops',
                'payment_terms' => 'Internal',
                'status' => 'active',
            ]
        );

        // 8. Create or update Warranty
        $warranty = Warranty::updateOrCreate(
            ['name' => 'No warrenty'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'No warrenty',
                'type' => 'none',
                'duration' => 0,
                'terms' => 'Sold without warranty.',
                'coverage' => 'None',
                'status' => 'active',
            ]
        );

        // 9. Define phone display models to seed
        $displaysToCreate = [
            [
                'brand_slug' => 'samsung',
                'name' => 'Samsung S23 Ultra AMOLED Display',
                'model_name' => 'S23 Ultra AMOLED',
                'model_number' => 'SM-S918B',
                'item_type_slug' => 'oled',
                'sku' => 'SKU-SAM-S23U-DISP',
                'item_code' => 'SAM-S23U-DISP',
                'selling_price' => 65000.00,
                'quantity' => 12,
                'storefront_quantity' => 8,
                'tags' => 'samsung,display,amoled,s23,ultra',
            ],
            [
                'brand_slug' => 'samsung',
                'name' => 'Samsung A54 OLED Display',
                'model_name' => 'A54 OLED Assembly',
                'model_number' => 'SM-A546B',
                'item_type_slug' => 'oled',
                'sku' => 'SKU-SAM-A54-DISP',
                'item_code' => 'SAM-A54-DISP',
                'selling_price' => 22500.00,
                'quantity' => 15,
                'storefront_quantity' => 10,
                'tags' => 'samsung,display,oled,a54',
            ],
            [
                'brand_slug' => 'apple',
                'name' => 'iPhone 14 Pro Max Super Retina OLED Display',
                'model_name' => 'iPhone 14 Pro Max Display',
                'model_number' => 'A2894',
                'item_type_slug' => 'oled',
                'sku' => 'SKU-APL-14PM-DISP',
                'item_code' => 'APL-14PM-DISP',
                'selling_price' => 75000.00,
                'quantity' => 10,
                'storefront_quantity' => 6,
                'tags' => 'apple,iphone,display,oled,14promax',
            ],
            [
                'brand_slug' => 'apple',
                'name' => 'iPhone 13 OLED Display',
                'model_name' => 'iPhone 13 Display Assembly',
                'model_number' => 'A2633',
                'item_type_slug' => 'oled',
                'sku' => 'SKU-APL-IP13-DISP',
                'item_code' => 'APL-IP13-DISP',
                'selling_price' => 35000.00,
                'quantity' => 18,
                'storefront_quantity' => 12,
                'tags' => 'apple,iphone,display,oled,iphone13',
            ],
            [
                'brand_slug' => 'apple',
                'name' => 'iPhone 11 LCD Display',
                'model_name' => 'iPhone 11 Liquid Retina LCD',
                'model_number' => 'A2221',
                'item_type_slug' => 'lcd',
                'sku' => 'SKU-APL-IP11-DISP',
                'item_code' => 'APL-IP11-DISP',
                'selling_price' => 15000.00,
                'quantity' => 25,
                'storefront_quantity' => 15,
                'tags' => 'apple,iphone,display,lcd,iphone11',
            ],
            [
                'brand_slug' => 'xiaomi',
                'name' => 'Xiaomi Redmi Note 12 Pro OLED Display',
                'model_name' => 'Redmi Note 12 Pro OLED',
                'model_number' => '22101316G',
                'item_type_slug' => 'oled',
                'sku' => 'SKU-XIA-RN12P-DISP',
                'item_code' => 'XIA-RN12P-DISP',
                'selling_price' => 18500.00,
                'quantity' => 20,
                'storefront_quantity' => 12,
                'tags' => 'xiaomi,redmi,display,oled,note12pro',
            ],
            [
                'brand_slug' => 'oneplus',
                'name' => 'OnePlus 11 Fluid AMOLED Display',
                'model_name' => 'OnePlus 11 AMOLED Assembly',
                'model_number' => 'CPH2449',
                'item_type_slug' => 'oled',
                'sku' => 'SKU-OP-OP11-DISP',
                'item_code' => 'OP-OP11-DISP',
                'selling_price' => 38000.00,
                'quantity' => 8,
                'storefront_quantity' => 5,
                'tags' => 'oneplus,display,amoled,oneplus11',
            ],
            [
                'brand_slug' => 'oppo',
                'name' => 'Oppo Reno 10 Pro AMOLED Display',
                'model_name' => 'Reno 10 Pro AMOLED',
                'model_number' => 'CPH2525',
                'item_type_slug' => 'oled',
                'sku' => 'SKU-OPP-R10P-DISP',
                'item_code' => 'OPP-R10P-DISP',
                'selling_price' => 24000.00,
                'quantity' => 14,
                'storefront_quantity' => 8,
                'tags' => 'oppo,display,amoled,reno10',
            ],
            [
                'brand_slug' => 'vivo',
                'name' => 'Vivo V27 AMOLED Display',
                'model_name' => 'Vivo V27 AMOLED Panel',
                'model_number' => 'V2246',
                'item_type_slug' => 'oled',
                'sku' => 'SKU-VIV-V27-DISP',
                'item_code' => 'VIV-V27-DISP',
                'selling_price' => 21500.00,
                'quantity' => 16,
                'storefront_quantity' => 10,
                'tags' => 'vivo,display,amoled,v27',
            ],
        ];

        // 10. Seed/update display replacement stocks
        foreach ($displaysToCreate as $idx => $data) {
            $prodImages = [];

            Stock::updateOrCreate(
                ['item_code' => $data['item_code']],
                [
                    'tenant_id' => $tenant->id,
                    'sku' => $data['sku'],
                    'item_code' => $data['item_code'],
                    'name' => $data['name'],
                    'description' => "Premium quality display replacement for the " . $data['name'] . ". Tested and certified.",
                    'category_id' => $category->id,
                    'make_id' => $make->id,
                    'brand_id' => $brands[$data['brand_slug']]->id,
                    'item_type_id' => $itemTypes[$data['item_type_slug']]->id,
                    'supplier_id' => $supplier->id,
                    'warranty_id' => $warranty->id,
                    'quantity' => $data['quantity'],
                    'reorder_level' => 5,
                    'unit_price' => $data['selling_price'] * 0.8,
                    'selling_price' => $data['selling_price'],
                    'wholesale_price' => $data['selling_price'] * 0.9,
                    'location' => 'Store Front A',
                    'barcode' => $data['item_code'],
                    'status' => 'active',
                    'storefront_enabled' => true,
                    'storefront_quantity' => $data['storefront_quantity'],
                    'model_name' => $data['model_name'],
                    'model_number' => $data['model_number'],
                    'color' => 'Black',
                    'specifications' => [
                        'Quality' => 'OEM Quality',
                        'Warranty' => 'None',
                        'Colors' => 'Black',
                    ],
                    'images' => $prodImages,
                    'tags' => $data['tags'],
                ]
            );
        }

        // 11. Ensure existing products (Samsung S20, Samsung A20, Netflix) are storefront-enabled
        $samsungS20 = Stock::find(1);
        if ($samsungS20) {
            $samsungS20->update([
                'storefront_enabled' => true,
                'storefront_quantity' => 10,
                'quantity' => max(10, $samsungS20->quantity),
            ]);
        }

        $netflix = Stock::find(3);
        if ($netflix) {
            $netflix->update([
                'storefront_enabled' => true,
                'storefront_quantity' => 25,
                'quantity' => max(25, $netflix->quantity),
            ]);
        }

        // 12. Retrieve IDs by SKU to construct display section mappings
        $featuredSkus = ['SKU-SAM-S23U-DISP', 'SKU-APL-14PM-DISP', 'SKU-APL-IP13-DISP', 'SKU-XIA-RN12P-DISP', 'SKU-DLK-NET-1M'];
        $newArrivalSkus = ['SKU-APL-14PM-DISP', 'SKU-VIV-V27-DISP', 'SKU-OP-OP11-DISP', 'SKU-SAM-A54-DISP', 'SKU-OPP-R10P-DISP'];
        $dealSkus = ['SKU-APL-IP11-DISP', 'SKU-XIA-RN12P-DISP', 'SKU-DLK-NET-1M'];

        $featuredIds = Stock::whereIn('sku', $featuredSkus)->pluck('id')->all();
        if ($samsungS20) {
            $featuredIds[] = 1; // Also feature Samsung S20
        }

        $newArrivalsIds = Stock::whereIn('sku', $newArrivalSkus)->pluck('id')->all();
        $dealIds = Stock::whereIn('sku', $dealSkus)->pluck('id')->all();

        // 13. Update site settings
        SiteSetting::updateOrCreate(
            ['key' => 'featured_product_ids'],
            [
                'tenant_id' => $tenant->id,
                'value' => json_encode($featuredIds),
                'type' => 'json',
                'group' => 'display',
                'label' => 'Featured Product IDs'
            ]
        );

        SiteSetting::updateOrCreate(
            ['key' => 'new_arrivals_ids'],
            [
                'tenant_id' => $tenant->id,
                'value' => json_encode($newArrivalsIds),
                'type' => 'json',
                'group' => 'display',
                'label' => 'New Arrivals IDs'
            ]
        );

        SiteSetting::updateOrCreate(
            ['key' => 'deal_product_ids'],
            [
                'tenant_id' => $tenant->id,
                'value' => json_encode($dealIds),
                'type' => 'json',
                'group' => 'display',
                'label' => 'Deal Product IDs'
            ]
        );

        $this->command?->info('Storefront displays and display list settings populated successfully.');
    }
}
