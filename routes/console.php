<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use App\Models\Stock;
use App\Models\User;
use App\Services\Operations\HostingReadinessService;
use App\Services\Storefront\StorefrontImageService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('system:health-check {--json : Output machine-readable JSON}', function () {
    $report = app(HostingReadinessService::class)->buildReport(15);
    $checks = collect($report['checks'])->mapWithKeys(function (array $check) {
        $key = str_replace(' ', '_', strtolower($check['label']));

        return [$key => [
            'value' => $check['value'],
            'status' => $check['status'] === 'healthy' ? 'ok' : ($check['status'] === 'neutral' ? 'neutral' : 'warning'),
            'message' => $check['help'],
        ]];
    })->all();

    $metrics = [
        'queued_notifications' => $report['metrics']['queued'],
        'stale_queued_notifications' => $report['metrics']['stale_queued'],
        'failed_notifications' => $report['metrics']['failed'],
        'retried_notifications' => $report['metrics']['retried'],
        'low_stock_items' => $report['metrics']['low_stock'],
        'hosting_score' => $report['score'],
    ];

    $hasWarning = collect($checks)->contains(fn ($check) => $check['status'] === 'warning')
        || $report['metrics']['stale_queued'] > 0
        || $report['metrics']['failed'] > 0;

    $payload = [
        'status' => $hasWarning ? 'warning' : 'ok',
        'score' => $report['score'],
        'checks' => $checks,
        'metrics' => $metrics,
    ];

    if ($this->option('json')) {
        $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $hasWarning ? self::FAILURE : self::SUCCESS;
    }

    $this->info('System Health Check');
    $this->newLine();

    foreach ($checks as $label => $check) {
        $prefix = $check['status'] === 'ok' ? '[OK]' : '[WARN]';
        $this->line(sprintf('%s %s: %s', $prefix, str_replace('_', ' ', ucfirst($label)), $check['value']));
        $this->line('      ' . $check['message']);
    }

    $this->newLine();
    $this->table(
        ['Metric', 'Value'],
        collect($metrics)->map(fn ($value, $label) => [str_replace('_', ' ', ucfirst($label)), $value])->values()->all()
    );

    if ($hasWarning) {
        $this->warn('System health check finished with warnings.');

        return self::FAILURE;
    }

    $this->info('System health check passed.');

    return self::SUCCESS;
})->purpose('Check production readiness and operational health');

Artisan::command('system:prepare-hosting {--skip-storage-link : Do not create the public storage symlink} {--skip-caches : Do not rebuild framework caches}', function () {
    $this->info('Preparing the application for hosted deployment...');
    $this->newLine();

    $runStep = function (string $command, string $label) {
        try {
            $exitCode = $this->call($command);

            if ($exitCode !== self::SUCCESS) {
                $this->error("{$label} failed.");

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            $this->error("{$label} failed: {$e->getMessage()}");

            return false;
        }
    };

    if (!$this->option('skip-storage-link')) {
        if (File::exists(public_path('storage'))) {
            $this->line('[OK] Public storage link already exists.');
        } else {
            if (!$runStep('storage:link', 'storage:link')) {
                return self::FAILURE;
            }
        }
    } else {
        $this->warn('Skipped storage:link by request.');
    }

    $skipCaches = (bool) $this->option('skip-caches');

    if (app()->environment('testing') && !$skipCaches) {
        $skipCaches = true;
        $this->warn('Testing environment detected. Cache rebuild skipped to keep the in-memory database stable.');
    }

    if (!$skipCaches) {
        foreach ([
            'config:clear' => 'config:clear',
            'view:clear' => 'view:clear',
            'route:clear' => 'route:clear',
            'event:clear' => 'event:clear',
            'config:cache' => 'config:cache',
            'route:cache' => 'route:cache',
            'view:cache' => 'view:cache',
            'event:cache' => 'event:cache',
        ] as $command => $label) {
            if (!$runStep($command, $label)) {
                return self::FAILURE;
            }
        }
    } else {
        $this->warn('Skipped cache rebuild by request.');
    }

    $this->newLine();
    $healthExitCode = $this->call('system:health-check');

    if ($healthExitCode !== self::SUCCESS) {
        $this->warn('Health check finished with warnings. Review the output above before going live.');
    }

    $this->newLine();
    $this->info('Hosting preparation finished.');

    return self::SUCCESS;
})->purpose('Build production-friendly caches and verify hosted deployment readiness');

Artisan::command('admin:restore-access {email : User email to elevate} {--super : Assign Super Admin instead of Admin}', function (string $email) {
    $user = User::where('email', $email)->first();

    if (!$user) {
        $this->error('User not found for email: ' . $email);

        return self::FAILURE;
    }

    $permissions = [
        'view dashboard',
        'view orders',
        'manage orders',
        'verify payments',
        'view inventory',
        'manage inventory',
        'view supply chain',
        'manage supply chain',
        'view invoices',
        'view pos',
        'view users',
        'create users',
        'edit users',
        'delete users',
        'view roles',
        'create roles',
        'edit roles',
        'delete roles',
        'view settings',
        'edit settings',
        'view activity logs',
        'view notification outbox',
        'view stock movements',
        'view system health',
        'view site management',
        'manage site management',
    ];

    foreach ($permissions as $permissionName) {
        Permission::firstOrCreate([
            'name' => $permissionName,
            'guard_name' => 'web',
        ]);
    }

    $roleName = $this->option('super') ? 'Super Admin' : 'Admin';
    $role = Role::firstOrCreate([
        'name' => $roleName,
        'guard_name' => 'web',
    ]);

    $role->syncPermissions($permissions);
    $user->syncRoles([$roleName]);

    $this->info("Access restored. {$user->email} is now assigned to {$roleName}.");

    return self::SUCCESS;
})->purpose('Restore admin access for an operator account');

Artisan::command('storefront:build-thumbnails {--force : Rebuild even if a derived image already exists}', function () {
    $imageService = app(StorefrontImageService::class);
    $force = (bool) $this->option('force');
    $processed = 0;

    $this->info('Building storefront image variants...');

    Stock::query()
        ->select('id', 'name', 'images')
        ->whereNotNull('images')
        ->orderBy('id')
        ->cursor()
        ->each(function (Stock $stock) use ($imageService, $force, &$processed) {
            foreach ((array) ($stock->images ?? []) as $path) {
                $imageService->buildAllForPath($path, $force);
                $processed++;
            }
        });

    $this->info("Storefront thumbnail build complete. Processed {$processed} image file(s).");

    return self::SUCCESS;
})->purpose('Build optimized storefront thumbnails for product images');
