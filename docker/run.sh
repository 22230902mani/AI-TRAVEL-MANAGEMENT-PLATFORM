#!/bin/sh
set -e

# Dynamically set Nginx port from $PORT env variable provided by Render (default to 80)
if [ -n "$PORT" ]; then
    echo "Configuring Nginx to listen on port $PORT"
    # Replace ALL listen directives (both IPv4 and IPv6)
    sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/nginx.conf
    sed -i "s/listen \[::\]:80;/listen [::]:$PORT;/g" /etc/nginx/nginx.conf
else
    echo "No PORT environment variable detected, defaulting to 80"
fi

# Fix storage permissions at runtime (important: Render may use overlayfs)
echo "Fixing storage permissions..."
mkdir -p /var/www/storage/logs \
         /var/www/storage/framework/cache/data \
         /var/www/storage/framework/sessions \
         /var/www/storage/framework/views \
         /var/www/bootstrap/cache

touch /var/www/storage/logs/laravel.log
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Run caching optimizations (as www-data to keep permissions consistent)
echo "Optimizing Laravel configurations..."
su-exec www-data php /var/www/artisan config:cache || echo "config:cache failed, continuing..."
su-exec www-data php /var/www/artisan route:cache  || echo "route:cache failed, continuing..."
su-exec www-data php /var/www/artisan view:cache   || echo "view:cache failed, continuing..."

# Execute migrations in production automatically on startup
echo "Executing database migrations..."
su-exec www-data php /var/www/artisan migrate --force || echo "migrate failed, continuing..."

# Launch supervisord to manage processes
echo "Starting services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
