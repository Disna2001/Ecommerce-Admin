<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY payment_method VARCHAR(50) NOT NULL DEFAULT 'cod'");
            DB::statement("ALTER TABLE orders MODIFY payment_status VARCHAR(50) NOT NULL DEFAULT 'unpaid'");
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_gateway')) {
                $table->string('payment_gateway')->nullable()->after('payment_method');
            }

            if (!Schema::hasColumn('orders', 'payment_gateway_transaction_id')) {
                $table->string('payment_gateway_transaction_id')->nullable()->after('payment_reference');
            }

            if (!Schema::hasColumn('orders', 'payment_gateway_payload')) {
                $table->json('payment_gateway_payload')->nullable()->after('payment_gateway_transaction_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            foreach (['payment_gateway_payload', 'payment_gateway_transaction_id', 'payment_gateway'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
