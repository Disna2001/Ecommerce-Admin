<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Customer snapshot (in case user data changes)
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();

            // Shipping address
            $table->string('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_postal_code')->nullable();
            $table->string('shipping_country')->default('Sri Lanka');

            // Status
            $table->enum('status', [
                'pending',
                'confirmed',
                'processing',
                'shipped',
                'delivered',
                'completed',
                'cancelled',
                'return_requested',
                'return_approved',
                'returned',
                'refunded',
            ])->default('pending');

            // Financials
            $table->decimal('subtotal',     12, 2)->default(0);
            $table->decimal('discount',     12, 2)->default(0);
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->decimal('total',        12, 2)->default(0);

            // Payment
            $table->enum('payment_method', ['cod', 'bank', 'card'])->default('cod');
            $table->enum('payment_status',  ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->string('payment_reference')->nullable();

            // Tracking
            $table->string('tracking_number')->nullable();
            $table->string('courier')->nullable();
            $table->string('tracking_url')->nullable();

            // Return
            $table->string('return_reason')->nullable();
            $table->text('return_notes')->nullable();
            $table->timestamp('return_requested_at')->nullable();
            $table->timestamp('return_approved_at')->nullable();

            // Coupon
            $table->string('coupon_code')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_id')->nullable()->constrained('stocks')->nullOnDelete();
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->decimal('unit_price',    12, 2);
            $table->decimal('sale_price',    12, 2);
            $table->integer('quantity');
            $table->decimal('subtotal',      12, 2);
            $table->json('product_snapshot')->nullable(); // stores product details at time of order
            $table->timestamps();
        });

        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->string('note')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_history');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};