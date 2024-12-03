sudo composer dump-autoload
sudo composer install
sudo chmod -R 777 storage
sudo chmod -R 777 bootstrap/cache
sudo chmod -R 777 database/data
sudo chmod -R 777 database/schemas
sudo chmod -R +x resources/plugins/wk

php artisan config:clear
php artisan cache:clear
php artisan route:clear

npm install
npm run production

php artisan -v migrate:refresh --seed
#cat <(echo "SET FOREIGN_KEY_CHECKS=0;") ./database/sql_migrations/hbcloud_sample_data_production.sql | mysql -u hotbox -pHotbox1100 clouddev
