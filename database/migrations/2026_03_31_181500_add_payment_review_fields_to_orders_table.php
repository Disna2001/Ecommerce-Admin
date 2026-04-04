<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_review_status')) {
                $table->string('payment_review_status')->nullable()->after('payment_status');
            }

            if (!Schema::hasColumn('orders', 'payment_review_note')) {
                $table->text('payment_review_note')->nullable()->after('payment_review_status');
            }

            if (!Schema::hasColumn('orders', 'payment_proof_path')) {
                $table->string('payment_proof_path')->nullable()->after('payment_review_note');
            }

            if (!Schema::hasColumn('orders', 'payment_submitted_at')) {
                $table->timestamp('payment_submitted_at')->nullable()->after('payment_proof_path');
            }

            if (!Schema::hasColumn('orders', 'payment_verified_at')) {
                $table->timestamp('payment_verified_at')->nullable()->after('payment_submitted_at');
            }

            if (!Schema::hasColumn('orders', 'payment_verified_by')) {
                $table->foreignId('payment_verified_by')->nullable()->after('payment_verified_at')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'payment_verified_by')) {
                $table->dropConstrainedForeignId('payment_verified_by');
            }

            $table->dropColumn([
                'payment_review_status',
                'payment_review_note',
                'payment_proof_path',
                'payment_submitted_at',
                'payment_verified_at',
            ]);
        });
    }
};
