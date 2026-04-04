<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get current columns
        $columns = Schema::getColumnListing('stocks');
        
        // Check if make_id exists and ensure it has proper foreign key
        if (in_array('make_id', $columns)) {
            // Get foreign keys
            $foreignKeys = collect(DB::select('
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_NAME = "stocks" 
                AND COLUMN_NAME = "make_id" 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            '));
            
            // If no foreign key exists, add it
            if ($foreignKeys->isEmpty()) {
                Schema::table('stocks', function (Blueprint $table) {
                    $table->foreign('make_id')
                          ->references('id')
                          ->on('makes')
                          ->nullOnDelete();
                });
            }
        }
        
        // Add target_make_id if it doesn't exist
        if (!in_array('target_make_id', $columns)) {
            Schema::table('stocks', function (Blueprint $table) {
                $table->foreignId('target_make_id')
                      ->nullable()
                      ->after('target_category_id')
                      ->constrained('makes')
                      ->nullOnDelete();
            });
        }
        
        // Add model_name if it doesn't exist
        if (!in_array('model_name', $columns)) {
            Schema::table('stocks', function (Blueprint $table) {
                $table->string('model_name')->nullable()->after('brand_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            // Only remove target_make_id as it's the only one we might have added
            if (Schema::hasColumn('stocks', 'target_make_id')) {
                $table->dropForeign(['target_make_id']);
                $table->dropColumn('target_make_id');
            }
        });
    }
};