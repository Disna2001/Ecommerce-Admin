<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_discounts', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->unsignedBigInteger('tenant_id')->nullable()->index();
            $blueprint->foreignId('stock_id')->constrained()->onDelete('cascade');
            $blueprint->decimal('discount_percent', 5, 2);
            $blueprint->decimal('original_price', 12, 2);
            $blueprint->decimal('discounted_price', 12, 2);
            $blueprint->date('applied_date')->index();
            $blueprint->timestamps();

            $blueprint->unique(['stock_id', 'applied_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_discounts');
    }
};
