<?php

namespace App\Services\Operations;

use App\Models\AdminActivityLog;
use App\Models\NotificationOutbox;
use App\Models\SiteSetting;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class HostingReadinessService
{
    public function buildReport(int $staleWindowMinutes = 15): array
    {
        $hasSiteSettings = Schema::hasTable('site_settings');
        $hasNotificationOutbox = Schema::hasTable('notification_outboxes');
        $hasStocks = Schema::hasTable('stocks');
        $hasAdminActivityLogs = Schema::hasTable('admin_activity_logs');

        $queued = $hasNotificationOutbox ? NotificationOutbox::where('status', 'queued')->count() : 0;
        $staleQueued = $hasNotificationOutbox
            ? NotificationOutbox::where('status', 'queued')
                ->whereNotNull('queued_at')
                ->where('queued_at', '<=', now()->subMinutes($staleWindowMinutes))
                ->count()
            : 0;
        $failedNotifications = $hasNotificationOutbox ? NotificationOutbox::where('status', 'failed')->count() : 0;
        $retriedNotifications = $hasNotificationOutbox ? NotificationOutbox::where('attempt_count', '>', 1)->count() : 0;
        $lowStockCount = $hasStocks ? Stock::whereColumn('quantity', '<=', 'reorder_level')->count() : 0;

        $appUrl = (string) ($this->setting($hasSiteSettings, 'app_public_url') ?: config('app.url', ''));
        $forceHttps = $this->toBool($this->setting($hasSiteSettings, 'force_https', false));
        $appKeyConfigured = filled(config('app.key'));
        $databaseHealthy = $this->databaseHealthy();
        $cacheDriver = (string) config('cache.default', 'file');
        $sessionDriver = (string) config('session.driver', 'file');
        $queueDriver = (string) config('queue.default', 'sync');
        $environment = (string) app()->environment();
        $viewsPath = storage_path('framework/views');
        $cachePath = base_path('bootstrap/cache');

        $checks = [
            [
                'label' => 'Environment',
                'status' => app()->environment('production') ? 'healthy' : 'warning',
                'value' => $environment,
                'help' => app()->environment('production')
                    ? 'Application environment is set to production.'
                    : 'Switch APP_ENV to production before going live.',
                'icon' => 'fa-server',
            ],
            [
                'label' => 'App Debug',
                'status' => config('app.debug') ? 'warning' : 'healthy',
                'value' => config('app.debug') ? 'Enabled' : 'Disabled',
                'help' => config('app.debug')
                    ? 'Disable debug mode before production deployment.'
                    : 'Debug mode is off.',
                'icon' => 'fa-bug-slash',
            ],
            [
                'label' => 'App Key',
                'status' => $appKeyConfigured ? 'healthy' : 'warning',
                'value' => $appKeyConfigured ? 'Configured' : 'Missing',
                'help' => $appKeyConfigured
                    ? 'Encryption key is available.'
                    : 'Run php artisan key:generate before deployment.',
                'icon' => 'fa-key',
            ],
            [
                'label' => 'Public URL',
                'status' => filled($appUrl) ? 'healthy' : 'warning',
                'value' => filled($appUrl) ? $appUrl : 'Not configured',
                'help' => filled($appUrl)
                    ? 'Canonical app URL is configured.'
                    : 'Set APP_URL or app_public_url for reliable links, emails, and callbacks.',
                'icon' => 'fa-globe',
            ],
            [
                'label' => 'HTTPS Enforcement',
                'status' => $forceHttps ? 'healthy' : 'warning',
                'value' => $forceHttps ? 'Forced' : 'Off',
                'help' => $forceHttps
                    ? 'URLs will be generated with https.'
                    : 'Enable force_https when the site is behind a real TLS endpoint.',
                'icon' => 'fa-lock',
            ],
            [
                'label' => 'Database',
                'status' => $databaseHealthy ? 'healthy' : 'warning',
                'value' => $databaseHealthy ? config('database.default') : 'Unavailable',
                'help' => $databaseHealthy
                    ? 'Database connection responds normally.'
                    : 'Primary database connection could not be confirmed.',
                'icon' => 'fa-database',
            ],
            [
                'label' => 'Queue Driver',
                'status' => $queueDriver !== 'sync' ? 'healthy' : 'warning',
                'value' => $queueDriver,
                'help' => $queueDriver !== 'sync'
                    ? 'Queue driver is production-friendly.'
                    : 'Sync driver is active. Background jobs will run inside user requests.',
                'icon' => 'fa-clock',
            ],
            [
                'label' => 'Cache Driver',
                'status' => $cacheDriver !== 'array' ? 'healthy' : 'warning',
                'value' => $cacheDriver,
                'help' => $cacheDriver !== 'array'
                    ? 'Cache driver persists between requests.'
                    : 'Array cache is request-only and not suitable for production persistence.',
                'icon' => 'fa-gauge-high',
            ],
            [
                'label' => 'Session Driver',
                'status' => $sessionDriver !== 'array' ? 'healthy' : 'warning',
                'value' => $sessionDriver,
                'help' => $sessionDriver !== 'array'
                    ? 'Session driver persists between requests.'
                    : 'Array session driver will not keep user sessions alive across requests.',
                'icon' => 'fa-user-shield',
            ],
            [
                'label' => 'Mail Sender',
                'status' => $this->setting($hasSiteSettings, 'mail_from_address') ? 'healthy' : 'warning',
                'value' => $this->setting($hasSiteSettings, 'mail_from_address') ?: 'Not configured',
                'help' => $this->setting($hasSiteSettings, 'mail_from_address')
                    ? 'Mail sender address is configured.'
                    : 'Set a sender address in system settings.',
                'icon' => 'fa-envelope',
            ],
            [
                'label' => 'Public Storage Link',
                'status' => File::exists(public_path('storage')) ? 'healthy' : 'warning',
                'value' => File::exists(public_path('storage')) ? 'Linked' : 'Missing',
                'help' => File::exists(public_path('storage'))
                    ? 'Public storage path is available.'
                    : 'Run php artisan storage:link if media files are not loading publicly.',
                'icon' => 'fa-folder-tree',
            ],
            [
                'label' => 'Writable Cache Paths',
                'status' => $this->pathsWritable([$viewsPath, $cachePath]) ? 'healthy' : 'warning',
                'value' => $this->pathsWritable([$viewsPath, $cachePath]) ? 'Ready' : 'Permission issue',
                'help' => $this->pathsWritable([$viewsPath, $cachePath])
                    ? 'Blade and cache directories are writable.'
                    : 'storage/framework/views or bootstrap/cache is not writable by the PHP process.',
                'icon' => 'fa-folder-open',
            ],
            [
                'label' => 'Business Contact',
                'status' => filled($this->setting($hasSiteSettings, 'support_email')) && filled($this->setting($hasSiteSettings, 'support_phone')) ? 'healthy' : 'warning',
                'value' => filled($this->setting($hasSiteSettings, 'support_email')) ? $this->setting($hasSiteSettings, 'support_email') : 'Incomplete',
                'help' => filled($this->setting($hasSiteSettings, 'support_email')) && filled($this->setting($hasSiteSettings, 'support_phone'))
                    ? 'Storefront contact details are configured.'
                    : 'Add support email and phone so invoices, help pages, and customers see the correct contact details.',
                'icon' => 'fa-headset',
            ],
            [
                'label' => 'WhatsApp Service',
                'status' => !$this->setting($hasSiteSettings, 'whatsapp_enabled', false)
                    ? 'neutral'
                    : ($this->setting($hasSiteSettings, 'whatsapp_api_url') && $this->setting($hasSiteSettings, 'whatsapp_api_key') ? 'healthy' : 'warning'),
                'value' => !$this->setting($hasSiteSettings, 'whatsapp_enabled', false)
                    ? 'Disabled'
                    : ($this->setting($hasSiteSettings, 'whatsapp_provider', 'custom') ?: 'custom'),
                'help' => !$this->setting($hasSiteSettings, 'whatsapp_enabled', false)
                    ? 'WhatsApp automation is intentionally disabled.'
                    : ($this->setting($hasSiteSettings, 'whatsapp_api_url') && $this->setting($hasSiteSettings, 'whatsapp_api_key')
                        ? 'WhatsApp provider credentials are configured.'
                        : 'WhatsApp is enabled but credentials are incomplete.'),
                'icon' => 'fa-comment-dots',
            ],
            [
                'label' => 'AI Assistant',
                'status' => !$this->setting($hasSiteSettings, 'ai_enabled', true)
                    ? 'neutral'
                    : ($this->setting($hasSiteSettings, 'ai_api_key') ? 'healthy' : 'warning'),
                'value' => !$this->setting($hasSiteSettings, 'ai_enabled', true)
                    ? 'Disabled'
                    : ($this->setting($hasSiteSettings, 'ai_model', 'gpt-5') ?: 'Configured'),
                'help' => !$this->setting($hasSiteSettings, 'ai_enabled', true)
                    ? 'AI assistant is intentionally disabled.'
                    : ($this->setting($hasSiteSettings, 'ai_api_key')
                        ? 'AI credentials are present.'
                        : 'AI is enabled but the API key is missing.'),
                'icon' => 'fa-robot',
            ],
        ];

        $attention = collect([
            [
                'title' => 'Stale queued notifications',
                'count' => $staleQueued,
                'tone' => 'rose',
                'route' => route('admin.notification-outbox'),
                'note' => 'Queued entries older than '.$staleWindowMinutes.' minutes may indicate a stopped worker.',
                'icon' => 'fa-hourglass-half',
            ],
            [
                'title' => 'Failed notification deliveries',
                'count' => $failedNotifications,
                'tone' => 'amber',
                'route' => route('admin.notification-outbox'),
                'note' => 'Review provider errors, recipient issues, and retry history.',
                'icon' => 'fa-triangle-exclamation',
            ],
            [
                'title' => 'Retried notification records',
                'count' => $retriedNotifications,
                'tone' => 'indigo',
                'route' => route('admin.notification-outbox'),
                'note' => 'Repeated attempts can reveal template, credential, or recipient issues.',
                'icon' => 'fa-rotate-right',
            ],
            [
                'title' => 'Low stock inventory items',
                'count' => $lowStockCount,
                'tone' => 'emerald',
                'route' => route('admin.stocks'),
                'note' => 'Restock these items before they affect order flow.',
                'icon' => 'fa-box-open',
            ],
        ])->filter(fn ($item) => $item['count'] > 0)->values();

        $recentSignals = $hasAdminActivityLogs
            ? AdminActivityLog::with('user')->latest()->take(6)->get()
            : collect();

        $metrics = [
            'queued' => $queued,
            'stale_queued' => $staleQueued,
            'failed' => $failedNotifications,
            'retried' => $retriedNotifications,
            'low_stock' => $lowStockCount,
        ];

        $healthyCount = collect($checks)->where('status', 'healthy')->count();
        $score = (int) round(($healthyCount / max(count($checks), 1)) * 100);
        $scoreTone = $score >= 85 ? 'emerald' : ($score >= 65 ? 'amber' : 'rose');

        $checklist = [
            [
                'title' => 'Deployment identity',
                'ready' => filled($appUrl) && $forceHttps && $appKeyConfigured,
                'help' => 'Public URL, HTTPS enforcement, and app key should all be ready.',
            ],
            [
                'title' => 'Runtime persistence',
                'ready' => $databaseHealthy && $cacheDriver !== 'array' && $sessionDriver !== 'array',
                'help' => 'Database, cache, and session layers should all persist correctly.',
            ],
            [
                'title' => 'Background operations',
                'ready' => $queueDriver !== 'sync' && $staleQueued === 0,
                'help' => 'Use a real queue driver and keep the worker processing jobs.',
            ],
            [
                'title' => 'Communications',
                'ready' => filled($this->setting($hasSiteSettings, 'mail_from_address')) && filled($this->setting($hasSiteSettings, 'support_email')),
                'help' => 'Mail sender and visible support contact should both be configured.',
            ],
            [
                'title' => 'Media delivery',
                'ready' => File::exists(public_path('storage')),
                'help' => 'Public storage link should be available for images, files, and storefront assets.',
            ],
        ];

        return [
            'checks' => $checks,
            'attention' => $attention,
            'recentSignals' => $recentSignals,
            'metrics' => $metrics,
            'score' => $score,
            'scoreTone' => $scoreTone,
            'checklist' => $checklist,
            'deployCommands' => [
                'php artisan migrate --force',
                'php artisan storage:link',
                'php artisan system:prepare-hosting',
                'php artisan system:health-check',
            ],
        ];
    }

    protected function databaseHealthy(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    protected function toBool(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
    }

    protected function setting(bool $hasSiteSettings, string $key, mixed $default = null): mixed
    {
        if (!$hasSiteSettings) {
            return $default;
        }

        return SiteSetting::get($key, $default);
    }

    protected function pathsWritable(array $paths): bool
    {
        foreach ($paths as $path) {
            if (!File::exists($path) || !is_writable($path)) {
                return false;
            }
        }

        return true;
    }
}
