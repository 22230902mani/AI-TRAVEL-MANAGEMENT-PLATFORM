# Stage 1: Build Vite assets using Node.js
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Final lightweight PHP Alpine Image
FROM php:8.2-fpm-alpine

# Install system and build dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    su-exec \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    postgresql-dev \
    autoconf \
    g++ \
    make \
    openssl-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql pdo_mysql bcmath gd opcache mbstring

# Install and enable MongoDB PHP extension
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Copy Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Copy compiled Vite assets from Stage 1 into the production directory
COPY --from=node-builder /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Ensure ALL required storage subdirectories exist (prevents Permission Denied on laravel.log)
RUN mkdir -p \
    /var/www/storage/logs \
    /var/www/storage/framework/cache/data \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/www/bootstrap/cache

# Pre-create the laravel log file so php-fpm (www-data) can write to it
RUN touch /var/www/storage/logs/laravel.log

# Set correct ownership - www-data is the user php-fpm runs as
RUN chown -R www-data:www-data \
    /var/www/storage \
    /var/www/bootstrap/cache \
    /var/www/public

RUN chmod -R 775 \
    /var/www/storage \
    /var/www/bootstrap/cache \
    /var/www/public

# Copy deployment configurations
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/run.sh /usr/local/bin/run.sh

# Ensure startup script is executable and has Unix line endings
RUN sed -i 's/\r$//' /usr/local/bin/run.sh && chmod +x /usr/local/bin/run.sh

# Expose port (Render sets $PORT)
EXPOSE 80

# Start via startup script
ENTRYPOINT ["/bin/sh", "/usr/local/bin/run.sh"]