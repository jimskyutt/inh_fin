#!/bin/bash
set -e

# Create required directories if they don't exist
mkdir -p /var/run/php/
mkdir -p /var/run/nginx/
mkdir -p /var/log/nginx/
touch /var/log/nginx/error.log
chmod 777 /var/log/nginx/error.log

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

#!/bin/bash
set -e

# Create required directories
mkdir -p /var/run/php
mkdir -p /var/run/nginx
mkdir -p /var/log/nginx

# Set permissions
chown -R www-data:www-data /var/run/php
chmod 755 /var/run/php
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Start PHP-FPM
echo "Starting PHP-FPM..."
php-fpm -D -y /usr/local/etc/php-fpm.conf -F -R

# Test Nginx configuration
echo "Testing Nginx configuration..."
nginx -t

# Start Nginx in the foreground
echo "Starting Nginx..."
nginx -g 'daemon off;'
