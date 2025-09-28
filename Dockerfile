# Build stage
FROM composer:2.7 as vendor

WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --no-dev

# Copy application files
COPY . .

# Generate the application key
RUN php artisan key:generate --ansi
# Build assets
FROM node:18 as frontend

WORKDIR /app

# Copy all necessary files for frontend build
COPY package*.json .
COPY vite.config.js .
COPY tailwind.config.js .
COPY postcss.config.js .
COPY resources/ ./resources/

# Install dependencies and build assets
RUN npm install && \
    npm run build
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mbstring exif pcntl bcmath zip

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Configure PHP
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configure Nginx
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/site.conf /etc/nginx/sites-available/default

# Copy application files
WORKDIR /var/www/html
COPY --from=vendor /app .
COPY --from=frontend /app/public/build /var/www/html/public/build

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 80
EXPOSE 80

# Start the application
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
