# Multi-stage build for optimized production image
FROM php:8.2-fpm-alpine AS base

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    nginx \
    supervisor \
    mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && git config --global --add safe.directory /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first (layer caching optimization)
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application files
COPY . .

# Set proper permissions for Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev

# Development stage
FROM base AS development
RUN composer install --prefer-dist
ENV APP_ENV=local
ENV APP_DEBUG=true

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 8000
EXPOSE 8000

# Set entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Start PHP-FPM
CMD ["php-fpm"]

# Production stage
FROM base AS production

# Copy nginx configuration
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Copy supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Optimize Laravel for production
RUN php artisan config:cache || true \
    && php artisan route:cache || true \
    && php artisan view:cache || true

ENV APP_ENV=production
ENV APP_DEBUG=false

# Expose port 8000
EXPOSE 8000

# Start supervisor (manages nginx + php-fpm)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]