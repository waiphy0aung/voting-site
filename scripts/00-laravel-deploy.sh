#!/usr/bin/env bash
echo 'Running composer'
composer global require hirak/prestissimo
composer install --no-dev --working-dir=/var/www/html --ignore-platform-reqs
php artisan migrate:fresh
php artisan db:seed
php artisan passport:install

echo 'Caching config...'
php artisan config:cache

echo 'Caching routes...'
php artisan route:cache

echo 'Running migrations...'
php artisan migrate --force
# php artisan migrate:fresh
# php artisan db:seed
# php artisan passport:install
