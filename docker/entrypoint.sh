#!/bin/sh
set -e

# Buat folder yang diperlukan jika belum ada
mkdir -p /var/www/storage/framework/cache/data \
         /var/www/storage/framework/sessions \
         /var/www/storage/framework/views \
         /var/www/storage/logs \
         /var/www/database

# Pastikan fail database SQLite wujud
if [ ! -f /var/www/database/database.sqlite ]; then
    touch /var/www/database/database.sqlite
fi

# Tetapkan perizinan penuh untuk www-data
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/database
chmod -R 777 /var/www/storage /var/www/bootstrap/cache /var/www/database

# Laraskan Port dinamik (Render menggunakan PORT atau default 8080/10000)
LISTEN_PORT=${PORT:-8080}
echo "Konfigurasi Nginx untuk mendengar pada Port: $LISTEN_PORT"
sed -i "s/listen [0-9]\+ default_server;/listen $LISTEN_PORT default_server;/g" /etc/nginx/http.d/default.conf
sed -i "s/listen \[::\]:[0-9]\+ default_server;/listen [::]:$LISTEN_PORT default_server;/g" /etc/nginx/http.d/default.conf

# Bersihkan cache lama
php /var/www/artisan config:clear || true
php /var/www/artisan route:clear || true
php /var/www/artisan view:clear || true
php /var/www/artisan cache:clear || true

# Jalankan migrasi dan seeder database
echo "Menjalankan migrasi database..."
php /var/www/artisan migrate --force --seed || echo "Migrasi telah lengkap."

# Jalankan supervisord (Nginx + PHP-FPM)
echo "Memulakan Nginx dan PHP-FPM..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
