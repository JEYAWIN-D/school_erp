FROM php:8.2-apache

# Install system dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev libpq-dev \
    zip unzip git curl gnupg2 ca-certificates \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql gd zip pcntl bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite headers

# Set the Apache document root to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Allow .htaccess overrides
RUN echo '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Copy composer files first (better Docker layer caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Install Node.js 20 for Vite build
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy package files and build frontend assets
COPY package.json package-lock.json ./
RUN npm ci

# Copy everything else
COPY . .

# Run composer scripts (package discovery, etc.)
RUN composer run-script post-autoload-dump --no-interaction || true

# Build Vite frontend assets
RUN npm run build

# Set correct file permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create startup script that sets up /tmp storage and starts Apache
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
# Create writable storage directories in /tmp (Render has read-only filesystem)\n\
mkdir -p /tmp/storage/framework/cache/data\n\
mkdir -p /tmp/storage/framework/sessions\n\
mkdir -p /tmp/storage/framework/views\n\
mkdir -p /tmp/storage/framework/testing\n\
mkdir -p /tmp/storage/logs\n\
mkdir -p /tmp/storage/app/public\n\
mkdir -p /tmp/bootstrap/cache\n\
chmod -R 777 /tmp/storage /tmp/bootstrap\n\
\n\
# Clear and cache config for production\n\
php artisan config:cache --no-interaction || true\n\
php artisan route:cache --no-interaction || true\n\
php artisan view:cache --no-interaction || true\n\
\n\
# Start Apache\n\
exec apache2-foreground\n\
' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]
