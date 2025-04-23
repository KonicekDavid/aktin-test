#!/bin/sh
set -e

chown -R www-data:www-data /var/www/aktin-test/temp /var/www/aktin-test/log || true
chmod -R 777 /var/www/aktin-test/temp /var/www/aktin-test/log || true

composer install

DB_FILE="/var/www/aktin-test/db/database.sqlite"
SQL_SCHEMA="/var/www/aktin-test/schema.sql"

if [ ! -f "$DB_FILE" ]; then
    echo "Creating SQLite database..."
    mkdir -p "$(dirname "$DB_FILE")"
    sqlite3 "$DB_FILE" < "$SQL_SCHEMA"
else
    echo "SQLite database already exists."
fi

chown -R www-data:www-data /var/www/aktin-test/db
chmod -R 775 /var/www/aktin-test/db
#chown root:root /var/www/aktin-test/db/database.sqlite

exec php-fpm