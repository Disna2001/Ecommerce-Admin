<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Profile
            if (!Schema::hasColumn('users', 'profile_photo_path')) {
                $table->string('profile_photo_path')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('profile_photo_path');
            }
            if (!Schema::hasColumn('users', 'dob')) {
                $table->date('dob')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('dob');
            }
            if (!Schema::hasColumn('users', 'preferences')) {
                $table->json('preferences')->nullable()->after('address');
            }

            // Social OAuth
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->unique()->after('preferences');
            }
            if (!Schema::hasColumn('users', 'google_token')) {
                $table->text('google_token')->nullable()->after('google_id');
            }
            if (!Schema::hasColumn('users', 'facebook_id')) {
                $table->string('facebook_id')->nullable()->unique()->after('google_token');
            }
            if (!Schema::hasColumn('users', 'facebook_token')) {
                $table->text('facebook_token')->nullable()->after('facebook_id');
            }
            if (!Schema::hasColumn('users', 'github_id')) {
                $table->string('github_id')->nullable()->unique()->after('facebook_token');
            }
            if (!Schema::hasColumn('users', 'github_token')) {
                $table->text('github_token')->nullable()->after('github_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_photo_path','phone','dob','address','preferences',
                'google_id','google_token','facebook_id','facebook_token',
                'github_id','github_token',
            ]);
        });
    }
};