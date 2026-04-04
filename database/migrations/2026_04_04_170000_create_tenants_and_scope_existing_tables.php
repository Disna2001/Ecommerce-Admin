<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    protected array $tenantTables = [
        'users',
        'site_settings',
        'categories',
        'brands',
        'makes',
        'item_types',
        'item_quality_levels',
        'suppliers',
        'warranties',
        'stocks',
        'banners',
        'discounts',
        'reviews',
        'orders',
        'order_items',
        'order_status_histories',
        'order_status_history',
        'invoices',
        'invoice_items',
        'merchants',
        'notification_outboxes',
        'stock_movement_logs',
        'admin_activity_logs',
    ];

    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('primary_domain')->unique();
            $table->json('domains')->nullable();
            $table->string('status')->default('active');
            $table->boolean('is_default')->default(false);
            $table->json('data')->nullable();
            $table->timestamps();
        });

        foreach ($this->tenantTables as $table) {
            if (!$this->tableHasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
                });
            }
        }

        if (Schema::hasTable('site_settings') && $this->indexExists('site_settings', 'site_settings_key_unique')) {
            Schema::table('site_settings', function (Blueprint $table) {
                $table->dropUnique('site_settings_key_unique');
            });
        }

        if (Schema::hasTable('site_settings') && !$this->indexExists('site_settings', 'site_settings_tenant_id_key_unique')) {
            Schema::table('site_settings', function (Blueprint $table) {
                $table->unique(['tenant_id', 'key'], 'site_settings_tenant_id_key_unique');
            });
        }

        $tenantId = $this->createDefaultTenant();

        if ($tenantId) {
            foreach ($this->tenantTables as $table) {
                if (Schema::hasTable($table) && $this->tableHasColumn($table, 'tenant_id')) {
                    DB::table($table)->whereNull('tenant_id')->update(['tenant_id' => $tenantId]);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('site_settings') && $this->indexExists('site_settings', 'site_settings_tenant_id_key_unique')) {
            Schema::table('site_settings', function (Blueprint $table) {
                $table->dropUnique('site_settings_tenant_id_key_unique');
            });
        }

        if (Schema::hasTable('site_settings') && !$this->indexExists('site_settings', 'site_settings_key_unique')) {
            Schema::table('site_settings', function (Blueprint $table) {
                $table->unique('key', 'site_settings_key_unique');
            });
        }

        foreach ($this->tenantTables as $tableName) {
            if (Schema::hasTable($tableName) && $this->tableHasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropConstrainedForeignId('tenant_id');
                });
            }
        }

        Schema::dropIfExists('tenants');
    }

    protected function createDefaultTenant(): ?int
    {
        $appUrl = env('APP_URL', 'http://localhost');
        $host = parse_url($appUrl, PHP_URL_HOST) ?: 'localhost';
        $slug = Str::slug((string) env('APP_NAME', 'default-tenant')) ?: 'default-tenant';
        $now = now();

        $existing = DB::table('tenants')->where('primary_domain', $host)->first();

        if ($existing) {
            DB::table('tenants')->where('id', $existing->id)->update([
                'domains' => json_encode(array_values(array_unique([$host, 'localhost', '127.0.0.1']))),
                'is_default' => true,
                'updated_at' => $now,
            ]);

            return (int) $existing->id;
        }

        return (int) DB::table('tenants')->insertGetId([
            'name' => (string) env('APP_NAME', 'Default Tenant'),
            'slug' => $slug,
            'primary_domain' => $host,
            'domains' => json_encode(array_values(array_unique([$host, 'localhost', '127.0.0.1']))),
            'status' => 'active',
            'is_default' => true,
            'data' => json_encode([]),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    protected function tableHasColumn(string $table, string $column): bool
    {
        return Schema::hasTable($table) && Schema::hasColumn($table, $column);
    }

    protected function indexExists(string $table, string $index): bool
    {
        try {
            return collect(DB::select("SHOW INDEX FROM `{$table}`"))->contains(fn ($row) => $row->Key_name === $index);
        } catch (\Throwable) {
            return false;
        }
    }
};
