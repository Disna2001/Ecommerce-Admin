<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('description')->nullable();
            $table->string('color', 32)->default('sky');
            $table->boolean('is_active')->default(true);
            $table->string('default_role_name')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
        });

        $defaults = [
            [
                'name' => 'Store Management',
                'slug' => 'store-management',
                'description' => 'Owns storefront visibility, pricing presentation, banners, and merchandising.',
                'color' => 'sky',
            ],
            [
                'name' => 'Warehouse Management',
                'slug' => 'warehouse-management',
                'description' => 'Handles stock intake, stock counts, inventory corrections, and storage readiness.',
                'color' => 'amber',
            ],
            [
                'name' => 'Site Management',
                'slug' => 'site-management',
                'description' => 'Maintains catalog publishing, site settings, navigation, and presentation polish.',
                'color' => 'violet',
            ],
            [
                'name' => 'Order Management',
                'slug' => 'order-management',
                'description' => 'Processes new orders, updates order states, tracks fulfilment, and reviews returns.',
                'color' => 'emerald',
            ],
            [
                'name' => 'POS Team',
                'slug' => 'pos-team',
                'description' => 'Runs in-person checkout, counter billing, and quick walk-in customer operations.',
                'color' => 'rose',
            ],
        ];

        $tenantIds = DB::table('tenants')->pluck('id');

        foreach ($tenantIds as $tenantId) {
            foreach ($defaults as $team) {
                DB::table('teams')->insert([
                    'tenant_id' => $tenantId,
                    'name' => $team['name'],
                    'slug' => $team['slug'],
                    'description' => $team['description'],
                    'color' => $team['color'],
                    'is_active' => true,
                    'default_role_name' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('team_user');
        Schema::dropIfExists('teams');
    }
};
