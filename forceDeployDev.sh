#!/bin/bash
#git pull

sudo chmod -R 777 storage
sudo chmod -R 777 bootstrap/cache
sudo chmod -R 777 database/data
sudo chmod -R 777 database/schemas
sudo chmod -R +x resources/plugins/wk

sudo composer install --prefer-dist --no-interaction
sudo composer dump-autoload
npm install
npm run production
php artisan migrate --force

## queue workers
php artisan horizon:purge
php artisan horizon:terminate

php artisan config:clear
php artisan cache:clear
php artisan route:clear
