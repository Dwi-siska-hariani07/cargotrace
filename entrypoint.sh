#!/usr/bin/env sh
set -e

echo "=== Running Laravel Database Migrations ==="
php artisan migrate --force

echo "=== Running Database Seeders ==="
php artisan db:seed --force || true

echo "=== Caching Config and Routes ==="
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "=== Starting FrankenPHP Server ==="
exec frankenphp run --config /etc/caddy/Caddyfile
