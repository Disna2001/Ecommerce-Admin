<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('item_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warranty_id')->nullable()->constrained()->nullOnDelete();
            $table->string('model_number')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->json('specifications')->nullable();
            $table->json('images')->nullable();
            $table->string('tags')->nullable();
            $table->text('notes')->nullable();
        });
    }

    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropForeign(['item_type_id']);
            $table->dropForeign(['warranty_id']);
            $table->dropColumn([
                'brand_id', 'item_type_id', 'warranty_id', 'model_number',
                'color', 'size', 'weight', 'specifications', 'images', 'tags', 'notes'
            ]);
        });
    }
};