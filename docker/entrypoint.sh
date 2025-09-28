#!/bin/bash
set -e

# Create required directories if they don't exist
mkdir -p /var/run/php/
mkdir -p /var/run/nginx/

# Ensure proper permissions for PHP-FPM socket
chown -R www-data:www-data /var/run/php
chmod 755 /var/run/php

# Set permissions
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Generate application key if not set
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
    php /var/www/html/artisan key:generate
fi

# Clear and optimize Laravel
php /var/www/html/artisan config:clear
php /var/www/html/artisan route:clear
php /var/www/html/artisan view:clear
php /var/www/html/artisan cache:clear
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache
php /var/www/html/artisan view:cache

# Run database migrations
php /var/www/html/artisan migrate --force

# Start PHP-FPM
php-fpm -D -y /usr/local/etc/php-fpm.conf -F -R

# Start Nginx
nginx -g 'daemon off;'
