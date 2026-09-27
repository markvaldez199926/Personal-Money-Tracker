# syntax=docker/dockerfile:1

# -------------------------------------------------------------
# Stage 1: Build Frontend Assets (Vite & Tailwind CSS)
# -------------------------------------------------------------
FROM node:20-alpine AS node_builder
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY resources/ ./resources/
COPY public/ ./public/
COPY vite.config.js postcss.config.js tailwind.config.js ./

RUN npm run build

# -------------------------------------------------------------
# Stage 2: Install Composer Dependencies
# -------------------------------------------------------------
FROM composer:2 AS composer_builder
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

COPY . ./
RUN composer dump-autoload --optimize --no-dev --no-interaction --no-scripts

# -------------------------------------------------------------
# Stage 3: Production PHP-FPM Runtime
# -------------------------------------------------------------
FROM php:8.2-fpm-alpine AS runner
WORKDIR /var/www/html

# Install system utilities, web server, and supervisor
RUN apk add --no-cache \
    curl \
    git \
    nginx \
    supervisor \
    netcat-openbsd \
    su-exec \
    tzdata \
    bash

# Install PHP extensions using the official extension installer
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
        pcntl \
        exif

# Copy PHP, OPcache, Nginx, and Supervisord configuration
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Ensure clean Nginx directories and copy master nginx.conf and site default.conf
RUN mkdir -p /etc/nginx/http.d /run/nginx /var/log/nginx \
    && rm -rf /etc/nginx/http.d/* /etc/nginx/conf.d/*
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy application files
COPY . /var/www/html

# Copy pre-built vendor from stage 2
COPY --from=composer_builder /app/vendor /var/www/html/vendor

# Copy compiled frontend assets from stage 1
COPY --from=node_builder /app/public/build /var/www/html/public/build

# Run package discovery with all PHP extensions loaded
RUN php artisan package:discover --ansi || true

# Setup entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Fix directory permissions for Laravel storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
