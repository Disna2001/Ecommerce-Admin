<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
header('Content-Type: application/json');
$settings = [];
$backupPath = __DIR__.'/../database/database.sqlite.backup-';
if (file_exists($backupPath)) {
    config(['database.connections.backup' => [
        'driver' => 'sqlite',
        'database' => $backupPath,
    ]]);
    $settings = \DB::connection('backup')->table('site_settings')->where('key', 'like', 'mail_%')->pluck('value', 'key')->all();
}
echo json_encode($settings);
