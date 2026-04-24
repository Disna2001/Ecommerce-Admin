<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storefront_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('notification_key');
            $table->string('type')->default('general');
            $table->string('label')->nullable();
            $table->string('accent')->default('indigo');
            $table->string('title');
            $table->text('body');
            $table->string('action_url')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'user_id', 'notification_key'], 'storefront_notifications_unique');
            $table->index(['tenant_id', 'user_id', 'read_at'], 'storefront_notifications_read_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storefront_notifications');
    }
};
