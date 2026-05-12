<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$settings = App\Models\SiteSetting::whereIn('key', ['favicon_path', 'logo_path'])->get();
foreach ($settings as $s) {
    echo "KEY: " . $s->key . " | VALUE: " . $s->value . " | TYPE: " . $s->type . "\n";
}
