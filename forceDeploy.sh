#!/bin/bash
git pull origin master

chmod -R 777 storage
chmod -R 777 bootstrap/cache
chmod -R 777 database/data
chmod -R 777 database/schemas
chmod -R +x resources/plugins/wk

composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader
composer dump-autoload
npm install
npm run production
php artisan migrate --force

( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service php7.2-fpm reload ) 9>/tmp/fpmlock

## queue workers
php artisan horizon:purge
php artisan horizon:terminate

php artisan config:clear
php artisan cache:clear
php artisan route:clear
