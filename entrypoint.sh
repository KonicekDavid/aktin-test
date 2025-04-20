#!/bin/sh
set -e

chown -R www-data:www-data /var/www/aktin-test/temp /var/www/aktin-test/log || true
chmod -R 777 /var/www/aktin-test/temp /var/www/aktin-test/log || true

composer install

exec php-fpm