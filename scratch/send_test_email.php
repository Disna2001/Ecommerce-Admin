<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$apiKey = App\Models\SiteSetting::get('mail_api_key');
config(['services.resend.key' => $apiKey]);
config(['mail.default' => 'resend']);

echo "MAILER: " . config('mail.default') . "\n";
echo "KEY: " . (config('services.resend.key') ? 'PRESENT' : 'NULL') . "\n";

try {
    Mail::raw('This is a test email from your Ecommerce System using Resend API.', function($msg) {
        $msg->to('disnachamuditha123@gmail.com')->subject('System Email Test');
    });
    echo "EMAIL_SENT_SUCCESSFULLY";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
