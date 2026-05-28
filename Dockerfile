FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    postgresql-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql pdo_mysql bcmath gd opcache mbstring

# Copy Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Set directory permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/public
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/public

# Copy deployment configurations
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/run.sh /usr/local/bin/run.sh

# Ensure startup script is executable
RUN chmod +x /usr/local/bin/run.sh

# Expose port (Render sets $PORT)
EXPOSE 80

# Start via startup script
ENTRYPOINT ["/usr/local/bin/run.sh"]
