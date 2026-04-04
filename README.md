# Stock AI

Stock AI is a Laravel + Livewire commerce and operations system with:

- storefront browsing, cart, wishlist, checkout, and order tracking
- admin dashboard, stock management, POS, invoices, and audit logs
- payment review, notification outbox, stock movement ledger, and health checks
- site management, branding controls, AI assistant, and communications settings

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan storage:link
php artisan serve
```

For a smoother local Windows or cross-platform setup, use:

```bash
composer run setup:local
composer dev
```

`composer dev` gives you the full coding loop with Laravel, queue processing, logs, and Vite hot reload together. See [docs/local-development.md](./docs/local-development.md) for the project analysis and local preview workflow.

On Windows with Laravel Herd, use this instead if `composer dev` fails because `pail` requires `pcntl`:

```bash
composer run dev:windows
```

## Multi-tenancy

This codebase now supports hostname-based multi-tenancy on one Laravel app and one server stack. Tenant resolution, scoped models, and tenant settings are documented in [docs/multi-tenancy.md](./docs/multi-tenancy.md).

## Repository purpose

This repository is the standalone `Ecommerce+Admin` product extraction for the website + POS + admin system. It no longer includes the separate master platform admin panel.

## Hosting checklist

Configure these before going live:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL` or admin `app_public_url`
- database credentials
- mail transport or SMTP credentials
- queue driver that is not `sync`
- public storage link
- object storage for user uploads on any platform with ephemeral local disks

Recommended server commands:

```bash
php artisan migrate --force
php artisan storage:link
php artisan system:prepare-hosting
php artisan system:health-check
```

## Render deployment

This repository includes Render-ready deployment files:

- [Dockerfile](./Dockerfile)
- [render.yaml](./render.yaml)
- [docker/render/start.sh](./docker/render/start.sh)
- [docker/apache/000-default.conf](./docker/apache/000-default.conf)

Recommended Render flow:

1. Create a new Blueprint from this repository.
2. Let Render provision the web service and PostgreSQL database from [render.yaml](./render.yaml).
3. Set `APP_KEY` in Render to a real Laravel application key before the first production boot. Generate one locally with `php artisan key:generate --show`.
4. Deploy the Blueprint. The app will automatically use Render's external hostname at runtime when `APP_URL` is not explicitly set.
5. If you later attach a custom domain, set `APP_URL` to that final domain and redeploy once.

Render-specific behavior:

- The deployment uses Docker because Render's official Laravel guide recommends Docker for existing Laravel applications.
- The startup script recreates the storage link, runs migrations, and warms Laravel caches at boot.
- The default Render environment uses:
  - `SESSION_DRIVER=file`
  - `CACHE_STORE=file`
  - `QUEUE_CONNECTION=sync`
  This keeps first deploys simple on a single web service.
- Public uploads work immediately, but on a free Render web service they are ephemeral. For durable uploads later, use a persistent disk or object storage.
- The app supports custom public storage settings through:
  - `PUBLIC_STORAGE_PATH`
  - `PUBLIC_STORAGE_URL`

Mail on Render:

- Render's official docs say free web services block outbound SMTP on ports `25`, `465`, and `587`.
- Because of that, [render.yaml](./render.yaml) defaults `MAIL_MAILER=log`.
- For real delivery on Render, switch to an API-based provider or a Render plan / mail path that supports your chosen transport.

Official references:

- Render Laravel with Docker:
  https://render.com/docs/deploy-php-laravel-docker
- Render environment variables:
  https://render.com/docs/environment-variables
- Render service types:
  https://render.com/docs/service-types

## Laravel Cloud deployment

This repository is now prepared for Laravel Cloud object storage by including the S3 Flysystem adapter in [composer.json](./composer.json).

Recommended Laravel Cloud environment:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-app.laravel.cloud`
- `DB_CONNECTION=mysql` or your attached Laravel Cloud database driver
- `DB_URL` from the attached database service
- `FILESYSTEM_DISK=s3`
- `SESSION_DRIVER=database`
- `CACHE_STORE=database`
- `QUEUE_CONNECTION=database`

For persistent uploads on Laravel Cloud:

- create or attach object storage in Laravel Cloud
- use the provided `AWS_*` / S3 environment variables
- keep `FILESYSTEM_DISK=s3`

Why this matters:

- Laravel Cloud application filesystems are ephemeral
- product images, logos, banners, payment proofs, profile photos, and uploaded documents should not stay on local disk
- this app already stores uploads through Laravel's filesystem abstraction, so switching to the `s3` disk is the correct Cloud deployment path

Recommended Laravel Cloud post-deploy commands:

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Production commands

Health and recovery:

```bash
php artisan system:health-check
php artisan admin:restore-access your@email.com
```

Hosting prep:

```bash
php artisan system:prepare-hosting
```

Storefront media:

```bash
php artisan storefront:build-thumbnails
php artisan storefront:build-thumbnails --force
```

## Admin production areas

Use these pages after deployment:

- `Admin > System Health`
- `Admin > Settings`
- `Admin > Notification Outbox`
- `Admin > Stock Movements`
- `Admin > Activity Logs`

## Notes

- Business-facing contact details, public URL, locale, timezone, and HTTPS behavior can be managed from admin settings.
- Customer emails can be tested directly from the admin settings page.
- Route caching is now safe for deployment because storefront/profile closure pages were replaced with cacheable routes.
