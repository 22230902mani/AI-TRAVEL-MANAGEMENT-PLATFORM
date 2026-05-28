#!/bin/sh

# Dynamically set Nginx port from $PORT env variable provided by Render (default to 80)
if [ -n "$PORT" ]; then
    echo "Configuring Nginx to listen on port $PORT"
    sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/nginx.conf
else
    echo "No PORT environment variable detected, default to 80"
fi

# Run caching optimizations
echo "Optimizing Laravel configurations..."
php /var/www/artisan config:cache
php /var/www/artisan route:cache
php /var/www/artisan view:cache

# Execute migrations in production automatically on startup
echo "Executing database migrations..."
php /var/www/artisan migrate --force

# Launch supervisord to manage processes
echo "Starting services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
