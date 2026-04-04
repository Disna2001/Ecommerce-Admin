<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_id')->constrained('stocks')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('rating');          // 1–5
            $table->string('title')->nullable();
            $table->text('body');
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_flagged')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // One review per user per product
            $table->unique(['user_id', 'stock_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};