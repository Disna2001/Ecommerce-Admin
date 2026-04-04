<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Services\Storefront\StorefrontDataService;
use App\Services\Tenancy\TenantManager;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!$this->app->runningInConsole() && $this->safeHasTable('tenants')) {
            app(TenantManager::class)->initializeFromRequest(request());
        }

        if (env('RENDER_EXTERNAL_HOSTNAME') && (!config('app.url') || config('app.url') === 'http://localhost')) {
            $renderUrl = 'https://'.env('RENDER_EXTERNAL_HOSTNAME');
            config(['app.url' => $renderUrl]);
            config(['app.asset_url' => $renderUrl]);
            config(['filesystems.disks.public.url' => rtrim($renderUrl, '/').'/storage']);
            URL::forceRootUrl($renderUrl);
            URL::forceScheme('https');
        }

        // Livewire page navigation can make Vite's CSS preload tags look unused,
        // which creates noisy browser warnings without improving much here.
        Vite::usePreloadTagAttributes(false);

        View::composer('layouts.shop', function ($view) {
            $view->with(app(StorefrontDataService::class)->getSharedLayoutData());
        });
        View::share('currentTenant', app(TenantManager::class)->current());

        // Define admin menu gate
        Gate::define('view-admin-menu', function ($user) {
            return $user->hasRole('Admin')
                || $user->hasRole('Super Admin')
                || $user->hasAnyPermission([
                    'view dashboard',
                    'view orders',
                    'view inventory',
                    'view users',
                    'view tenants',
                    'view settings',
                    'view activity logs',
                    'view notification outbox',
                    'view stock movements',
                    'view system health',
                    'view site management',
                ]);
        });

        if ($this->safeHasTable('site_settings')) {
            $appUrl = SiteSetting::get('app_public_url') ?: config('app.url');
            $renderExternalHostname = env('RENDER_EXTERNAL_HOSTNAME');
            $forceHttps = filter_var(SiteSetting::get('force_https', false), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
            $appTimezone = SiteSetting::get('app_timezone');
            $appLocale = SiteSetting::get('app_locale');
            $siteName = SiteSetting::get('site_name');
            $mailMailer = SiteSetting::get('mail_mailer');
            $mailFromAddress = SiteSetting::get('mail_from_address');
            $mailFromName = SiteSetting::get('mail_from_name');
            $smtpHost = SiteSetting::get('mail_smtp_host');
            $smtpPort = SiteSetting::get('mail_smtp_port');
            $smtpUsername = SiteSetting::get('mail_smtp_username');
            $smtpPassword = SiteSetting::get('mail_smtp_password');
            $smtpEncryption = SiteSetting::get('mail_smtp_encryption');

            if ((!$appUrl || $appUrl === 'http://localhost') && $renderExternalHostname) {
                $appUrl = 'https://'.$renderExternalHostname;
            }

            if ($siteName) {
                config(['app.name' => $siteName]);
            }

            $appUrlScheme = $appUrl ? parse_url($appUrl, PHP_URL_SCHEME) : null;

            if ($appUrl) {
                config(['app.url' => $appUrl]);
                config(['app.asset_url' => rtrim($appUrl, '/')]);
                config(['filesystems.disks.public.url' => rtrim($appUrl, '/').'/storage']);
                URL::forceRootUrl($appUrl);
            }

            if ($forceHttps || $appUrlScheme === 'https') {
                URL::forceScheme('https');
            }

            if ($appTimezone) {
                config(['app.timezone' => $appTimezone]);
                date_default_timezone_set($appTimezone);
            }

            if ($appLocale) {
                config(['app.locale' => $appLocale]);
                app()->setLocale($appLocale);
            }

            if ($mailMailer) {
                config(['mail.default' => $mailMailer]);
            }

            if ($mailFromAddress) {
                config(['mail.from.address' => $mailFromAddress]);
            }

            if ($mailFromName) {
                config(['mail.from.name' => $mailFromName]);
            }

            if ($smtpHost) {
                config(['mail.mailers.smtp.host' => $smtpHost]);
            }

            if ($smtpPort) {
                config(['mail.mailers.smtp.port' => $smtpPort]);
            }

            if ($smtpUsername) {
                config(['mail.mailers.smtp.username' => $smtpUsername]);
            }

            if ($smtpPassword) {
                config(['mail.mailers.smtp.password' => $smtpPassword]);
            }

            if ($smtpEncryption) {
                config(['mail.mailers.smtp.encryption' => $smtpEncryption]);
            }
        }

        if ($this->safeHasTable('permissions') && $this->safeHasTable('roles')) {
            $corePermissions = [
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
                'view tenants',
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

            foreach ($corePermissions as $permissionName) {
                Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);
            }

            foreach (['Admin', 'Super Admin'] as $roleName) {
                $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
                if ($role) {
                    $role->givePermissionTo($corePermissions);
                }
            }
        }
    }

    protected function safeHasTable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable) {
            return false;
        }
    }

    public function register(): void
    {
        $this->app->singleton(TenantManager::class, fn () => new TenantManager());

        Livewire::component('admin.site-management.appearance-manager',  \App\Livewire\Admin\SiteManagement\AppearanceManager::class);
        Livewire::component('admin.site-management.banner-manager',      \App\Livewire\Admin\SiteManagement\BannerManager::class);
        Livewire::component('admin.site-management.discount-manager',    \App\Livewire\Admin\SiteManagement\DiscountManager::class);
        Livewire::component('admin.site-management.display-item-manager',\App\Livewire\Admin\SiteManagement\DisplayItemManager::class);
        Livewire::component('admin.order-manager', \App\Livewire\Admin\OrderManager::class);
        Livewire::component('admin.admin-activity-log-manager', \App\Livewire\Admin\AdminActivityLogManager::class);
        Livewire::component('admin.notification-outbox-manager', \App\Livewire\Admin\NotificationOutboxManager::class);
        Livewire::component('admin.stock-movement-log-manager', \App\Livewire\Admin\StockMovementLogManager::class);
        Livewire::component('admin.system-health-manager', \App\Livewire\Admin\SystemHealthManager::class);
        Livewire::component('admin.site-management.review-manager',\App\Livewire\Admin\SiteManagement\ReviewManager::class);
        Livewire::component('admin.system-settings-manager', \App\Livewire\Admin\SystemSettingsManager::class);
    }
}
