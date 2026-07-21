<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('whatsapp_conversations')->cascadeOnDelete();
            $table->enum('direction', ['inbound', 'outbound']);
            $table->enum('sender_type', ['customer', 'bot', 'agent', 'system']);
            $table->text('content');
            $table->enum('message_type', ['text', 'template', 'system_note'])->default('text');
            $table->json('tool_calls')->nullable();
            $table->string('provider_message_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
