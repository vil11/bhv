
###### dependencies
rm -rf vendor
composer clear-cache && composer update && composer install --prefer-source

composer update james-heinrich/getid3:2.0
