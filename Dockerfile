FROM php:8.3-cli-alpine

# Pasang dependency sistem & PHP extensions
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite \
    sqlite-dev \
    icu-dev \
    oniguruma-dev \
    && docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd intl

# Pasang Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Salin fail kod aplikasi
COPY . .

# Set default Environment Variables
ENV APP_NAME="NAIMbif - Ladang Bridlot" \
    APP_ENV=production \
    APP_DEBUG=true \
    APP_KEY="base64:YVfDpakeFLsXPGzPjwyH3UWbwglTFLrImnnWFwhiZ60=" \
    APP_TIMEZONE="Asia/Kuala_Lumpur" \
    APP_LOCALE="ms" \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/var/www/database/database.sqlite \
    SESSION_DRIVER=file \
    CACHE_STORE=file \
    QUEUE_CONNECTION=sync \
    LOG_CHANNEL=stderr

# Pasang dependensi PHP tanpa dev
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Konfigurasi perizinan folder storage, bootstrap cache, dan database
RUN mkdir -p /var/www/storage/framework/cache/data \
             /var/www/storage/framework/sessions \
             /var/www/storage/framework/views \
             /var/www/storage/logs \
             /var/www/database \
    && chmod -R 777 /var/www/storage /var/www/bootstrap/cache /var/www/database

# Salin script startup
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Port Cloud Run / Render
EXPOSE 8080 10000 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
