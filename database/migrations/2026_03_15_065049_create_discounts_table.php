<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->nullable(); // coupon code (optional)
            $table->string('type')->default('percentage'); // percentage, fixed
            $table->decimal('value', 10, 2); // % or flat amount
            $table->decimal('min_order_amount', 10, 2)->default(0);
            $table->decimal('max_discount_amount', 10, 2)->nullable(); // cap for percentage discounts
            $table->string('scope')->default('all'); // all, category, product
            $table->unsignedBigInteger('scope_id')->nullable(); // category_id or stock_id
            $table->boolean('has_timer')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('show_timer_on_site')->default(true);
            $table->string('timer_label')->nullable()->default('Sale ends in:');
            $table->integer('usage_limit')->nullable(); // null = unlimited
            $table->integer('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};