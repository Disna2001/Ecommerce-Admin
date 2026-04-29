<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->boolean('storefront_enabled')->default(false)->after('status');
            $table->unsignedInteger('storefront_quantity')->default(0)->after('storefront_enabled');
        });

        DB::table('stocks')
            ->where('status', 'active')
            ->update([
                'storefront_enabled' => true,
                'storefront_quantity' => DB::raw('quantity'),
            ]);
    }

    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn(['storefront_enabled', 'storefront_quantity']);
        });
    }
};
