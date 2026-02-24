#!/bin/bash
set -e

APP_PATH="/var/www/hacker-news-laravel"
cd "$APP_PATH"

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Creating storage link..."
php artisan storage:link --force

echo "==> Reloading PHP-FPM..."
sudo service php8.5-fpm reload

echo "==> Deploy complete!"
