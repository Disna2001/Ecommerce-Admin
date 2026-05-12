<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$failed = \App\Models\NotificationOutbox::where('status', 'failed')->latest()->take(5)->get();
foreach ($failed as $f) {
    echo "ID: {$f->id} | Channel: {$f->channel} | Error: {$f->failure_message}\n";
}
