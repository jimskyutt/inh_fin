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

# Start PHP-FPM in the background
echo "Starting PHP-FPM..."
php-fpm -D -y /usr/local/etc/php-fpm.conf -F -R

# Test Nginx configuration
echo "Testing Nginx configuration..."
if ! nginx -t; then
    echo "Nginx configuration test failed"
    exit 1
fi

# Start Nginx in the background
echo "Starting Nginx..."
nginx -g 'daemon off;' &

# Wait for services to start
sleep 5

# Check if Nginx is running
if ! pgrep "nginx" > /dev/null; then
    echo "Nginx failed to start"
    exit 1
fi

# Check if PHP-FPM is running
if ! pgrep "php-fpm" > /dev/null; then
    echo "PHP-FPM is not running"
    exit 1
fi

echo "Services are running. Showing logs..."

# Keep the container running and show logs
tail -f /var/log/nginx/error.log /var/log/nginx/access.log
