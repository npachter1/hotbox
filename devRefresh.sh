#!/bin/bash
sudo chmod -R 777 storage
sudo chmod -R 777 bootstrap/cache
sudo chmod -R 777 database/data
sudo chmod -R 777 database/schemas
sudo chmod -R +x resources/plugins/wk

sudo composer install
sudo composer dump-autoload

php artisan config:clear
php artisan cache:clear
php artisan route:clear

php artisan migrate
