<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storefront_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('storefront_pages')->cascadeOnDelete();
            $table->string('type');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable();
            $table->json('style')->nullable();
            $table->integer('schema_version')->default(1);
            $table->string('slot')->default('before');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storefront_sections');
    }
};
