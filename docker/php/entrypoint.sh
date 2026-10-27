#!/bin/sh
set -eu

mkdir -p storage/app/private storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
# Persist the generated application key across container recreation.
if [ -z "${APP_KEY:-}" ]; then
    if [ ! -s storage/app/private/application.key ]; then
        umask 077
        php artisan key:generate --show > storage/app/private/application.key
    fi
    APP_KEY="$(cat storage/app/private/application.key)"
    export APP_KEY
fi
chown -R www-data:www-data storage bootstrap/cache
if [ "${1:-}" = "php-fpm" ]; then
    gosu www-data php artisan migrate --force
fi
exec docker-php-entrypoint "$@"
