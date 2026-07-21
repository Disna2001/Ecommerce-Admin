<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->unique()->index();
            $table->string('customer_name')->nullable();
            $table->enum('status', ['bot_active', 'awaiting_human', 'human_active', 'closed'])->default('bot_active');
            $table->integer('bot_reply_count')->default(0);
            $table->boolean('awaiting_handoff_confirmation')->default(false);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversations');
    }
};
