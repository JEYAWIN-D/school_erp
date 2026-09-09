# ============================================================
# Laravel 12 + Vite + PostgreSQL
# Render deployment
# ============================================================

FROM php:8.3-apache

# ------------------------------------------------------------
# 1. Install system dependencies and PHP extensions
# ------------------------------------------------------------
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    libicu-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    curl \
    gnupg2 \
    ca-certificates \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        gd \
        zip \
        pcntl \
        bcmath \
        intl \
        mbstring \
        exif \
    && docker-php-ext-install opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# OPcache tuning for production
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.save_comments=1'; \
    echo 'opcache.fast_shutdown=1'; \
} > /usr/local/etc/php/conf.d/opcache.ini


# ------------------------------------------------------------
# 2. Enable Apache modules required by Laravel
# ------------------------------------------------------------
RUN a2enmod rewrite headers deflate expires


# ------------------------------------------------------------
# 3. Configure Apache to serve Laravel /public
# ------------------------------------------------------------
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri \
    -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf

RUN sed -ri \
    -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf


# ------------------------------------------------------------
# 4. Allow Laravel .htaccess
# ------------------------------------------------------------
RUN echo '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' \
    > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel


# ------------------------------------------------------------
# 5. Application directory
# ------------------------------------------------------------
WORKDIR /var/www/html


# ------------------------------------------------------------
# 6. Install Composer
# ------------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


# ------------------------------------------------------------
# 7. Install Laravel PHP dependencies
# ------------------------------------------------------------
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-scripts


# ------------------------------------------------------------
# 8. Install Node.js 20
# ------------------------------------------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


# ------------------------------------------------------------
# 9. Install frontend dependencies
# ------------------------------------------------------------
COPY package.json package-lock.json ./

RUN npm ci


# ------------------------------------------------------------
# 10. Copy the Laravel application
# ------------------------------------------------------------
COPY . .


# ------------------------------------------------------------
# 11. Run Composer package discovery
# ------------------------------------------------------------
RUN composer dump-autoload \
    --optimize \
    --no-interaction


# ------------------------------------------------------------
# 12. Build Vite production assets
# ------------------------------------------------------------
RUN npm run build


# ------------------------------------------------------------
# 13. Create Laravel writable directories
# ------------------------------------------------------------
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/app/public \
    bootstrap/cache


# ------------------------------------------------------------
# 14. Set permissions
# ------------------------------------------------------------
RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache

RUN chmod -R 775 \
    storage \
    bootstrap/cache


# ------------------------------------------------------------
# 15. Create Laravel startup script
# ------------------------------------------------------------
RUN cat <<'EOF' > /usr/local/bin/start.sh
#!/bin/bash

set -e

echo "=========================================="
echo "Starting Laravel application"
echo "=========================================="

# Render supplies PORT.
# Use 10000 if PORT is not supplied.
PORT=${PORT:-10000}

echo "Using port: ${PORT}"

# ----------------------------------------------------------
# Configure Apache to use Render's PORT
# ----------------------------------------------------------

sed -i "s/^Listen 80$/Listen ${PORT}/" \
    /etc/apache2/ports.conf

sed -i "s/<VirtualHost \\*:80>/<VirtualHost *:${PORT}>/" \
    /etc/apache2/sites-available/000-default.conf

# ----------------------------------------------------------
# Laravel storage link
# ----------------------------------------------------------

php artisan storage:link || true

# ----------------------------------------------------------
# Create /tmp/storage dirs (Render sets LARAVEL_STORAGE_PATH=/tmp/storage)
# /tmp is writable on Render — the app dir is read-only after image build
# ----------------------------------------------------------

if [ -n "$LARAVEL_STORAGE_PATH" ]; then
    echo "Creating writable storage at ${LARAVEL_STORAGE_PATH} ..."
    mkdir -p \
        "${LARAVEL_STORAGE_PATH}/framework/cache/data" \
        "${LARAVEL_STORAGE_PATH}/framework/sessions" \
        "${LARAVEL_STORAGE_PATH}/framework/views" \
        "${LARAVEL_STORAGE_PATH}/framework/testing" \
        "${LARAVEL_STORAGE_PATH}/logs" \
        "${LARAVEL_STORAGE_PATH}/app/public"
    chmod -R 775 "${LARAVEL_STORAGE_PATH}"
fi

# ----------------------------------------------------------
# Clear old cached configuration
# ----------------------------------------------------------

php artisan optimize:clear || true


# ----------------------------------------------------------
# Run database migrations (creates new tables on each deploy)
# ----------------------------------------------------------

echo "Running database migrations..."
php artisan migrate --force --no-interaction || echo "Migrations failed (non-fatal, continuing)"

# ----------------------------------------------------------
# Cache Laravel configuration for speed
# ----------------------------------------------------------

php artisan config:cache --no-interaction || true

# Route cache can fail if routes contain closures,
# therefore don't stop deployment if it fails.
php artisan route:cache --no-interaction || true

# View cache
php artisan view:cache --no-interaction || true

echo "=========================================="
echo "Starting Apache"
echo "=========================================="

# Start Apache in foreground
exec apache2-foreground
EOF


# ------------------------------------------------------------
# 16. Make startup script executable
# ------------------------------------------------------------
RUN chmod +x /usr/local/bin/start.sh


# ------------------------------------------------------------
# 17. Render listens on port 10000
# ------------------------------------------------------------
EXPOSE 10000


# ------------------------------------------------------------
# 18. Start application
# ------------------------------------------------------------
CMD ["/usr/local/bin/start.sh"]