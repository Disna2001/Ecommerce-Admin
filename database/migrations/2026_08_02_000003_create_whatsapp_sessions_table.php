<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('baileys')->comment('baileys | meta_cloud | twilio | custom');
            $table->string('state')->default('disconnected')->comment('connecting | connected | disconnected');
            $table->string('phone_number')->nullable()->comment('Linked phone number once connected');
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_sessions');
    }
};
