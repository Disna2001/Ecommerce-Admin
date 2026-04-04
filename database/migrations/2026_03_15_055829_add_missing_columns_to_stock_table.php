<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            // Only add columns that don't already exist
            if (!Schema::hasColumn('stocks', 'make_id')) {
                $table->foreignId('make_id')->nullable()->constrained('makes')->nullOnDelete();
            }
            if (!Schema::hasColumn('stocks', 'brand_id')) {
                $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            }
            if (!Schema::hasColumn('stocks', 'item_type_id')) {
                $table->foreignId('item_type_id')->nullable()->constrained('item_types')->nullOnDelete();
            }
            if (!Schema::hasColumn('stocks', 'warranty_id')) {
                $table->foreignId('warranty_id')->nullable()->constrained('warranties')->nullOnDelete();
            }
            if (!Schema::hasColumn('stocks', 'model_name')) {
                $table->string('model_name')->nullable();
            }
            if (!Schema::hasColumn('stocks', 'model_number')) {
                $table->string('model_number')->nullable();
            }
            if (!Schema::hasColumn('stocks', 'color')) {
                $table->string('color')->nullable();
            }
            if (!Schema::hasColumn('stocks', 'size')) {
                $table->string('size')->nullable();
            }
            if (!Schema::hasColumn('stocks', 'weight')) {
                $table->decimal('weight', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('stocks', 'specifications')) {
                $table->json('specifications')->nullable();
            }
            if (!Schema::hasColumn('stocks', 'images')) {
                $table->json('images')->nullable();
            }
            if (!Schema::hasColumn('stocks', 'tags')) {
                $table->string('tags')->nullable();
            }
            if (!Schema::hasColumn('stocks', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('stocks', 'barcode')) {
                $table->string('barcode')->nullable()->unique();
            }
            if (!Schema::hasColumn('stocks', 'location')) {
                $table->string('location')->nullable();
            }
            if (!Schema::hasColumn('stocks', 'reorder_level')) {
                $table->integer('reorder_level')->default(10);
            }
            if (!Schema::hasColumn('stocks', 'quality_level')) {
                $table->string('quality_level')->nullable();
            }
            if (!Schema::hasColumn('stocks', 'wholesale_price')) {
                $table->decimal('wholesale_price', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('stocks', 'target_category_id')) {
                $table->foreignId('target_category_id')->nullable()->constrained('categories')->nullOnDelete();
            }
            if (!Schema::hasColumn('stocks', 'target_item_type_id')) {
                $table->foreignId('target_item_type_id')->nullable()->constrained('item_types')->nullOnDelete();
            }
            if (!Schema::hasColumn('stocks', 'target_make_id')) {
                $table->foreignId('target_make_id')->nullable()->constrained('makes')->nullOnDelete();
            }
            if (!Schema::hasColumn('stocks', 'target_brand_id')) {
                $table->foreignId('target_brand_id')->nullable()->constrained('brands')->nullOnDelete();
            }
            if (!Schema::hasColumn('stocks', 'target_model')) {
                $table->string('target_model')->nullable();
            }
            if (!Schema::hasColumn('stocks', 'target_model_number')) {
                $table->string('target_model_number')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            // Drop foreign keys first
            $foreignKeys = [
                'make_id', 'brand_id', 'item_type_id', 'warranty_id',
                'target_category_id', 'target_item_type_id', 'target_make_id', 'target_brand_id',
            ];
            foreach ($foreignKeys as $fk) {
                if (Schema::hasColumn('stocks', $fk)) {
                    $table->dropForeign(['stocks_' . $fk . '_foreign']);
                    $table->dropColumn($fk);
                }
            }

            $columns = [
                'model_name', 'model_number', 'color', 'size', 'weight',
                'specifications', 'images', 'tags', 'notes', 'barcode',
                'location', 'reorder_level', 'quality_level', 'wholesale_price',
                'target_model', 'target_model_number',
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('stocks', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};