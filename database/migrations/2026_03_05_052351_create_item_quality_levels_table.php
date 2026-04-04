<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_quality_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->integer('level_order')->default(0);
            $table->string('color')->default('#6B7280');
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default quality levels
        DB::table('item_quality_levels')->insert([
            [
                'name' => 'Premium',
                'code' => 'premium',
                'description' => 'Highest quality products with exceptional features and materials',
                'level_order' => 1,
                'color' => '#8B5CF6',
                'icon' => 'star',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'High',
                'code' => 'high',
                'description' => 'High quality products with excellent features',
                'level_order' => 2,
                'color' => '#3B82F6',
                'icon' => 'trending-up',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Standard',
                'code' => 'standard',
                'description' => 'Standard quality products with essential features',
                'level_order' => 3,
                'color' => '#10B981',
                'icon' => 'check-circle',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Economy',
                'code' => 'economy',
                'description' => 'Budget-friendly products with basic features',
                'level_order' => 4,
                'color' => '#F59E0B',
                'icon' => 'dollar-sign',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('item_quality_levels');
    }
};