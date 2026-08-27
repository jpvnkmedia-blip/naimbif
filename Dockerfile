FROM php:8.3-fpm-alpine

# Pasang dependency sistem & PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
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

# Pasang dependensi PHP tanpa dev
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Konfigurasi perizinan folder storage dan cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Konfigurasi Nginx
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Konfigurasi Supervisor untuk jalankan Nginx & PHP-FPM serentak
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Salin script startup untuk automigrate SQLite
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Port Cloud Run (Default 8080)
EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
