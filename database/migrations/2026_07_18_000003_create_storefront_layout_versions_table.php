<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storefront_layout_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('storefront_pages')->cascadeOnDelete();
            $table->string('status')->default('published');
            $table->json('snapshot');
            $table->text('note')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('published_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storefront_layout_versions');
    }
};
