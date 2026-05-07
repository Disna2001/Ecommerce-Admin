<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automated_discount_rules', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->unsignedBigInteger('tenant_id')->nullable()->index();
            $blueprint->string('name')->default('Standard Daily Logic');
            $blueprint->boolean('is_active')->default(true);
            $blueprint->decimal('min_margin_percent', 5, 2)->default(10.00);
            $blueprint->decimal('max_discount_percent', 5, 2)->default(30.00);
            $blueprint->integer('daily_items_limit')->default(10);
            $blueprint->string('rotation_strategy')->default('random'); // random, slow_moving, overstocked
            $blueprint->json('target_categories')->nullable();
            $blueprint->json('target_brands')->nullable();
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automated_discount_rules');
    }
};
