#!/bin/sh
set -e

chown -R www-data:www-data /var/www/api-test/temp /var/www/api-test/log || true
chmod -R 777 /var/www/api-test/temp /var/www/api-test/log || true

composer install

DB_FILE="/var/www/api-test/db/database.sqlite"
SQL_SCHEMA="/var/www/api-test/schema.sql"

if [ ! -f "$DB_FILE" ]; then
    echo "Creating SQLite database..."
    mkdir -p "$(dirname "$DB_FILE")"
    sqlite3 "$DB_FILE" < "$SQL_SCHEMA"
else
    echo "SQLite database already exists."
fi

chown -R www-data:www-data /var/www/api-test/db
chmod -R 775 /var/www/api-test/db
#chown root:root /var/www/api-test/db/database.sqlite

exec php-fpm