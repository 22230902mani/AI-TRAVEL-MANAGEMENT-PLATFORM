#!/bin/sh
set -e

# Dynamically set Nginx port from $PORT env variable provided by Render (default to 80)
if [ -n "$PORT" ]; then
    echo "Configuring Nginx to listen on port $PORT"
    sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/nginx.conf
    sed -i "s/listen \[::\]:80;/listen [::]:$PORT;/g" /etc/nginx/nginx.conf
else
    echo "No PORT environment variable detected, defaulting to 80"
fi

# Fix storage permissions at runtime (Render overlayfs can reset permissions)
echo "Fixing storage permissions..."
mkdir -p /var/www/storage/logs \
         /var/www/storage/framework/cache/data \
         /var/www/storage/framework/sessions \
         /var/www/storage/framework/views \
         /var/www/bootstrap/cache

touch /var/www/storage/logs/laravel.log
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Run caching optimizations as www-data (same user as php-fpm) to avoid permission conflicts
echo "Optimizing Laravel configurations..."
su-exec www-data php /var/www/artisan config:cache  || echo "WARNING: config:cache failed"
su-exec www-data php /var/www/artisan route:cache   || echo "WARNING: route:cache failed"
su-exec www-data php /var/www/artisan view:cache    || echo "WARNING: view:cache failed"

# Execute migrations automatically on startup
echo "Executing database migrations..."
su-exec www-data php /var/www/artisan migrate --force || echo "WARNING: migrate failed"

# Launch supervisord to manage nginx + php-fpm
echo "Starting services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf