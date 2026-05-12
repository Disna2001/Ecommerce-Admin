<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

App\Models\SiteSetting::set('mail_mailer', 'resend');
App\Models\SiteSetting::set('mail_api_key', 're_VeuboJX1_FMFZNH2kXa4GPQ6p2wbLdAaL');
echo "SUCCESS";
