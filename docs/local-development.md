# Local Development Guide

## Project analysis

This repository is a Laravel 12 application with a Livewire 3 admin and storefront UI.

- Backend framework: Laravel 12 on PHP 8.2+
- Frontend tooling: Vite 7, Tailwind CSS, Blade, Livewire, Volt
- Default local database: SQLite at `database/database.sqlite`
- Background processing: database queue driver
- Session and cache storage: database tables
- Useful local command: `composer dev` starts the app server, queue worker, log tail, and Vite hot reload together

## Main app areas

- Public storefront: product browsing, cart, wishlist, checkout, and order tracking
- Admin operations: stock management, POS, invoices, notifications, stock movements, and audit logs
- Site management: banners, discounts, reviews, display items, and appearance controls
- Integrations: mail, WhatsApp notifications, Socialite auth, and an OpenAI-backed assistant

## Recommended local setup

Run this once on a new machine:

```powershell
composer run setup:local
```

What it does:

- installs PHP dependencies
- creates `.env` from `.env.example` if needed
- creates `database/database.sqlite` if missing
- generates the Laravel app key
- recreates the `public/storage` symlink
- runs database migrations
- installs Node dependencies

If `.env` already exists, the script keeps your current database settings. On this machine the existing `.env` was already pointed at MySQL, so a truly fresh local SQLite setup would mean replacing or editing `.env` back to the `.env.example` defaults before running the command.

## Live coding preview

Run this while developing:

```powershell
composer dev
```

This starts four processes:

- Laravel local server
- queue listener
- Laravel log tail
- Vite dev server with automatic browser refresh

Local URLs:

- App: `http://127.0.0.1:8000`
- Vite HMR: `http://127.0.0.1:5173`

If the browser does not auto-open, load the app URL manually and Vite will attach to the page.

## Windows and Herd note

If you are using Laravel Herd on Windows, `http://localhost/...` may show Herd's own 404 page instead of this project. In that case, use the project server URL directly:

- App: `http://127.0.0.1:9001`
- Admin: `http://127.0.0.1:9001/admin`

Start it with:

```powershell
composer run dev:windows
```

Why this exists:

- the default `composer dev` script runs `php artisan pail`
- `pail` needs the `pcntl` extension
- Windows Herd PHP does not provide `pcntl`
- `dev:windows` skips `pail` and starts a stable local preview stack

## Local validation

Run the backend test suite:

```powershell
php artisan test
```

Build production assets locally:

```powershell
npm run build
```

## Notes for this repo

- `.env.example` is already tuned for local SQLite development.
- Because sessions, cache, and queues use the database locally, migrations must be up to date before testing login or background jobs.
- User-uploaded files use Laravel storage, so `php artisan storage:link` is required for image and file preview from the browser.
