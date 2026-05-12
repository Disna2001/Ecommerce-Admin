<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$failedCount = \App\Models\NotificationOutbox::where('status', 'failed')->count();
$totalCount = \App\Models\NotificationOutbox::count();
$staleQueued = \App\Models\NotificationOutbox::where('status', 'queued')
                ->whereNotNull('queued_at')
                ->where('queued_at', '<=', now()->subMinutes(15))
                ->count();

echo "Total: $totalCount | Failed: $failedCount | Stale: $staleQueued\n";

$failed = \App\Models\NotificationOutbox::where('status', 'failed')->latest()->take(3)->get();
foreach ($failed as $f) {
    echo "ID: {$f->id} | Channel: {$f->channel} | Error: {$f->failure_message}\n";
}
