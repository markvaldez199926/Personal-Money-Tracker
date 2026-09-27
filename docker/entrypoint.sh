#!/bin/sh
set -e

# Determine default port based on database driver
DEFAULT_DB_PORT=3306
if [ "$DB_CONNECTION" = "pgsql" ]; then
    DEFAULT_DB_PORT=5432
fi
DB_PORT="${DB_PORT:-$DEFAULT_DB_PORT}"

# If sqlite, ensure database file exists
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    SQLITE_PATH="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    mkdir -p "$(dirname "$SQLITE_PATH")"
    if [ ! -f "$SQLITE_PATH" ]; then
        touch "$SQLITE_PATH"
        chown www-data:www-data "$SQLITE_PATH"
        chmod 664 "$SQLITE_PATH"
    fi
fi

# Wait for database host if defined and not sqlite
if [ -n "$DB_HOST" ] && [ "$DB_CONNECTION" != "sqlite" ]; then
    echo "Waiting for database connection on $DB_HOST:$DB_PORT..."
    MAX_RETRIES=30
    COUNT=0
    while ! nc -z "$DB_HOST" "$DB_PORT"; do
        sleep 1
        COUNT=$((COUNT + 1))
        if [ "$COUNT" -ge "$MAX_RETRIES" ]; then
            echo "Database connection timed out after $MAX_RETRIES seconds. Proceeding..."
            break
        fi
    done
    if [ "$COUNT" -lt "$MAX_RETRIES" ]; then
        echo "Database connection established!"
    fi
fi

# Ensure storage directory structure and permissions
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/storage/app/public

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create storage symlink
php artisan storage:link --force || true

# Generate application key if missing
if [ -z "$APP_KEY" ]; then
    echo "Generating application encryption key..."
    php artisan key:generate --force || true
fi

# Discover packages
php artisan package:discover --ansi || true

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force || true

# Ensure runtime directories for Nginx and Supervisor
mkdir -p /run/nginx /var/log/supervisor /var/log/nginx

# Dynamic PORT configuration for cloud providers (Render, Fly.io, Railway, Cloud Run)
if [ -n "$PORT" ]; then
    echo "Configuring Nginx to listen on port $PORT..."
    for conf in /etc/nginx/http.d/default.conf /etc/nginx/conf.d/default.conf; do
        if [ -f "$conf" ]; then
            sed -i "s/listen 80;/listen $PORT;/g" "$conf"
            sed -i "s/listen \[::\]:80;/listen [::]:$PORT;/g" "$conf"
        fi
    done
fi

echo "Starting container command: $@"
exec "$@"
