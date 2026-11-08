#!/bin/sh
set -e

# Pastikan folder penyimpanan dan upload tersedia
mkdir -p /var/www/html/storage/uploads/payment_proofs \
         /var/www/html/storage/uploads/qris \
         /var/www/html/storage/uploads/news \
         /var/www/html/storage/uploads/products \
         /var/www/html/storage/uploads/avatars \
         /var/www/html/storage/uploads/certificates \
         /var/www/html/storage/logs \
         /var/www/html/storage/cache

# Atur kepemilikan dan permission folder storage
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage

# Install dependency composer jika belum ada vendor
if [ ! -d "/var/www/html/vendor" ]; then
    echo ">> Menjalankan composer install..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Jalankan perintah utama (default: php-fpm)
exec "$@"

