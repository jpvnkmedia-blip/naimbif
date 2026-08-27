#!/bin/sh
set -e

# Buat folder penting
mkdir -p /var/www/storage/framework/cache/data \
         /var/www/storage/framework/sessions \
         /var/www/storage/framework/views \
         /var/www/storage/logs \
         /var/www/database

# Pastikan fail database SQLite wujud
if [ ! -f /var/www/database/database.sqlite ]; then
    touch /var/www/database/database.sqlite
fi

# Tetapkan perizinan penuh
chmod -R 777 /var/www/storage /var/www/bootstrap/cache /var/www/database

# Bersihkan cache
php /var/www/artisan config:clear || true
php /var/www/artisan route:clear || true
php /var/www/artisan view:clear || true
php /var/www/artisan cache:clear || true

# Jalankan migrasi dan seeder
echo "Menjalankan migrasi database..."
php /var/www/artisan migrate --force --seed || true

# Dapatkan PORT dari persekitaran (Render = 10000 atau default 8080)
LISTEN_PORT="${PORT:-8080}"
echo "Sistem NAIMbif bermula pada Port: $LISTEN_PORT"

# Jalankan server Laravel secara langsung
exec php /var/www/artisan serve --host=0.0.0.0 --port="$LISTEN_PORT"
