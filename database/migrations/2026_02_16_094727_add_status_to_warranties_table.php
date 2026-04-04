<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('warranties', function (Blueprint $table) {
            if (!Schema::hasColumn('warranties', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('coverage');
            }
        });
    }

    public function down()
    {
        Schema::table('warranties', function (Blueprint $table) {
            if (Schema::hasColumn('warranties', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};