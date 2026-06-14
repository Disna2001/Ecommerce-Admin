<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function index()
    {
        $versionPath = base_path('version.txt');
        $siteVersion = file_exists($versionPath) ? trim(file_get_contents($versionPath)) : '1.0.0';

        $settings = [
            'site_name' => SiteSetting::get('site_name', 'Display Lanka'),
            'logo_url' => SiteSetting::get('logo_path') ? asset('storage/' . SiteSetting::get('logo_path')) : null,
            'primary_color' => SiteSetting::get('primary_color', '#8b5cf6'),
            'secondary_color' => SiteSetting::get('secondary_color', '#d946ef'),
            'currency_symbol' => SiteSetting::get('currency_symbol', 'Rs.'),
            'support_email' => SiteSetting::get('support_email'),
            'support_phone' => SiteSetting::get('support_phone'),
            'site_version' => $siteVersion,
        ];

        return response()->json($settings);
    }
}
