<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('item_types', function (Blueprint $table) {
            if (!Schema::hasColumn('item_types', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('description');
            }
        });
    }

    public function down()
    {
        Schema::table('item_types', function (Blueprint $table) {
            if (Schema::hasColumn('item_types', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};