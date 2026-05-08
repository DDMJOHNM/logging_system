#!/bin/sh
set -e
cd /var/www/html

if [ ! -d vendor ]; then
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ -f artisan ]; then
  chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
  chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true
  php artisan config:clear 2>/dev/null || true
  php artisan migrate --force
fi

exec docker-php-entrypoint "$@"
