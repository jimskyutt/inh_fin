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

# Create .env from .env.example if it doesn't exist
RUN if [ ! -f .env ]; then cp .env.example .env; fi

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
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql pdo_pgsql pgsql mbstring exif pcntl bcmath zip

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Configure PHP
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configure PHP-FPM
RUN mkdir -p /var/run/php
COPY docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

# Configure Nginx
COPY docker/nginx/render.conf /etc/nginx/nginx.conf

# Copy application files
WORKDIR /var/www/html
COPY --from=vendor /app .
COPY --from=frontend /app/public/build /var/www/html/public/build

RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Copy entrypoint script and make it executable
COPY docker/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Health check
HEALTHCHECK --interval=30s --timeout=3s \
  CMD curl -f http://localhost/ || exit 1

# Start services
CMD ["/usr/local/bin/entrypoint.sh"]

# Expose ports for Nginx and PHP-FPM
EXPOSE 80 443 9000

# Set environment variables for Nginx and PHP-FPM
ENV PORT=80
ENV PHP_FPM_PORT=9000

# Start the application
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
