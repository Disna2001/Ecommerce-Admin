#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

PORT="${PORT:-10000}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-true}"
PUBLIC_STORAGE_PATH="${PUBLIC_STORAGE_PATH:-/var/www/html/storage/app/public}"

ensure_writable_paths() {
  mkdir -p \
    bootstrap/cache \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    "${PUBLIC_STORAGE_PATH}"

  chmod -R ug+rwX storage bootstrap/cache public || true

  if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data storage bootstrap/cache public || true
  fi
}

ensure_writable_paths

if [ "${PUBLIC_STORAGE_PATH}" != "/var/www/html/storage/app/public" ]; then
  rm -rf storage/app/public
  ln -snf "${PUBLIC_STORAGE_PATH}" storage/app/public
fi

rm -f bootstrap/cache/*.php
rm -f public/storage
php artisan storage:link || true
php artisan optimize:clear || true
php artisan package:discover --ansi

if [ "${RUN_MIGRATIONS}" = "true" ]; then
  php artisan migrate --force
fi

php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

ensure_writable_paths

sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \\*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
