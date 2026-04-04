<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class GeneralManager extends Component
{
    public $appName;
    public $appUrl;
    public $appEnv;
    public $appDebug;
    public $timezone;
    public $currency;
    public $dateFormat;
    public $timeFormat;
    public $itemsPerPage;
    public $lowStockThreshold;
    public $enableNotifications;
    public $maintenanceMode;
    public $backupFrequency;
    public $autoBackup;
    public $companyName;
    public $companyEmail;
    public $companyPhone;
    public $companyAddress;
    public $companyLogo;
    public $theme;
    public $language = 'en';

    protected $rules = [
        'appName' => 'required|string|min:3',
        'appUrl' => 'required|url',
        'timezone' => 'required|string',
        'currency' => 'required|string|size:3',
        'itemsPerPage' => 'required|integer|min:5|max:100',
        'lowStockThreshold' => 'required|integer|min:1|max:50',
        'companyName' => 'required|string|min:3',
        'companyEmail' => 'required|email',
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $this->appName = config('app.name');
        $this->appUrl = config('app.url');
        $this->appEnv = config('app.env');
        $this->appDebug = config('app.debug');
        $this->timezone = config('app.timezone');
        $this->currency = config('app.currency', 'USD');
        $this->dateFormat = config('app.date_format', 'Y-m-d');
        $this->timeFormat = config('app.time_format', 'H:i');
        $this->itemsPerPage = config('app.items_per_page', 15);
        $this->lowStockThreshold = config('app.low_stock_threshold', 10);
        $this->enableNotifications = config('app.enable_notifications', true);
        $this->maintenanceMode = app()->isDownForMaintenance();
        $this->backupFrequency = config('app.backup_frequency', 'daily');
        $this->autoBackup = config('app.auto_backup', true);
        $this->companyName = config('app.company_name', '');
        $this->companyEmail = config('app.company_email', '');
        $this->companyPhone = config('app.company_phone', '');
        $this->companyAddress = config('app.company_address', '');
        $this->theme = config('app.theme', 'light');
        $this->language = config('app.locale', 'en');
    }

    public function saveGeneral()
    {
        $this->validate();

        // Update .env file
        $this->updateEnvironmentFile([
            'APP_NAME' => '"' . $this->appName . '"',
            'APP_URL' => $this->appUrl,
            'APP_ENV' => $this->appEnv,
            'APP_DEBUG' => $this->appDebug ? 'true' : 'false',
            'APP_TIMEZONE' => $this->timezone,
            'APP_CURRENCY' => $this->currency,
            'APP_LOCALE' => $this->language,
        ]);

        // Update config settings in database or cache
        Cache::forever('app_settings', [
            'date_format' => $this->dateFormat,
            'time_format' => $this->timeFormat,
            'items_per_page' => $this->itemsPerPage,
            'low_stock_threshold' => $this->lowStockThreshold,
            'enable_notifications' => $this->enableNotifications,
            'backup_frequency' => $this->backupFrequency,
            'auto_backup' => $this->autoBackup,
            'company_name' => $this->companyName,
            'company_email' => $this->companyEmail,
            'company_phone' => $this->companyPhone,
            'company_address' => $this->companyAddress,
            'theme' => $this->theme,
        ]);

        session()->flash('message', 'General settings updated successfully.');
    }

    public function toggleMaintenance()
    {
        if ($this->maintenanceMode) {
            Artisan::call('up');
            $this->maintenanceMode = false;
            session()->flash('message', 'Application is now live.');
        } else {
            Artisan::call('down', [
                '--secret' => '1630542a-246b-4b66-afa1-dd72a4c43515'
            ]);
            $this->maintenanceMode = true;
            session()->flash('message', 'Application is now in maintenance mode.');
        }
    }

    public function runBackup()
    {
        try {
            Artisan::call('backup:run');
            session()->flash('message', 'Backup completed successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        
        session()->flash('message', 'All caches cleared successfully.');
    }

    private function updateEnvironmentFile($data)
    {
        $envFile = base_path('.env');
        $content = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            if (strpos($content, $key . '=') !== false) {
                $content = preg_replace('/^' . $key . '=.*$/m', $key . '=' . $value, $content);
            } else {
                $content .= PHP_EOL . $key . '=' . $value;
            }
        }

        file_put_contents($envFile, $content);
    }

    public function render()
    {
        return view('livewire.settings.general-manager');
    }
}