#!/bin/sh

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
while ! nc -z news_db 3306; do
    sleep 1
done
echo "MySQL is ready!"

# Install dependencies
composer install --no-interaction --no-progress --prefer-dist

# Clear configuration cache
php artisan config:clear

# Run migrations
php artisan migrate --force

# Start PHP-FPM
php-fpm 