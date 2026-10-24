#!/bin/bash
set -e

# Update Apache port to Render's dynamic PORT
PORT_NUMBER="${PORT:-10000}"
sed -ri -e "s/Listen [0-9]+/Listen ${PORT_NUMBER}/g" /etc/apache2/ports.conf
sed -ri -e "s/<VirtualHost \*:[0-9]+>/<VirtualHost \*:${PORT_NUMBER}>/g" /etc/apache2/sites-available/000-default.conf

# Discover packages & clear/cache configurations
php artisan package:discover --ansi || true
php artisan storage:link || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "BreakZone Cafe starting Apache on port ${PORT_NUMBER}..."
exec apache2-foreground
