<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->string('quality_level')->nullable()->after('brand_id');
            $table->foreignId('target_category_id')->nullable()->constrained('categories')->after('quality_level');
            $table->foreignId('target_item_type_id')->nullable()->constrained('item_types')->after('target_category_id');
            $table->foreignId('target_brand_id')->nullable()->constrained('brands')->after('target_item_type_id');
            $table->string('target_model')->nullable()->after('target_brand_id');
            $table->string('target_model_number')->nullable()->after('target_model');
            $table->decimal('wholesale_price', 10, 2)->nullable()->after('selling_price');
        });
    }

    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn([
                'quality_level',
                'target_category_id',
                'target_item_type_id',
                'target_brand_id',
                'target_model',
                'target_model_number',
                'wholesale_price'
            ]);
        });
    }
};