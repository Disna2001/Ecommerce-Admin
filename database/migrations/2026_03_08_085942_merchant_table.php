<?php
// database/migrations/2024_01_01_000001_create_merchants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nic_number')->unique();
            $table->string('br_number')->unique();
            $table->string('nic_image_path');
            $table->string('shop_image_path');
            $table->string('merchant_selfie_path');
            $table->string('shop_name');
            $table->text('shop_address');
            $table->string('phone_number');
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('verification_status');
            $table->index('nic_number');
            $table->index('br_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};