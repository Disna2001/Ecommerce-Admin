<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_outboxes', function (Blueprint $table) {
            $table->unsignedInteger('attempt_count')->default(1)->after('payload');
            $table->timestamp('last_attempt_at')->nullable()->after('attempt_count');
        });
    }

    public function down(): void
    {
        Schema::table('notification_outboxes', function (Blueprint $table) {
            $table->dropColumn(['attempt_count', 'last_attempt_at']);
        });
    }
};
