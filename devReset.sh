#!/bin/bash
sudo composer install
sudo composer dump-autoload

sudo chmod -R 777 storage
sudo chmod -R 777 bootstrap/cache
sudo chmod -R 777 database/data
sudo chmod -R 777 database/schemas
sudo chmod -R +x resources/plugins/wk

php artisan config:clear
php artisan cache:clear
php artisan route:clear

php artisan -v migrate:refresh --seed
cat <(echo "SET FOREIGN_KEY_CHECKS=0;") ./database/sql_migrations/hbcloud_sample_data.sql | mysql -u hotbox -pHotbox1100 newdemo 
