#!/usr/bin/env bash

set -e

APP_DIR="$(cd "$(dirname "$0")" && pwd)"
PHP_BIN="/opt/alt/php84/usr/bin/php"
COMPOSER_BIN="/usr/local/bin/composer"

cd "$APP_DIR"

echo "PHP yang digunakan:"
"$PHP_BIN" -v

echo "Install dependency production..."
"$PHP_BIN" "$COMPOSER_BIN" install --no-dev --optimize-autoloader

echo "Menjalankan migration..."
"$PHP_BIN" artisan migrate --force

echo "Membersihkan cache Laravel..."
"$PHP_BIN" artisan optimize:clear

echo "Deployment selesai."
