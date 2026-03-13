#!/usr/bin/env sh
set -eu

cd /var/www/html

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

if [ "${DB_CONNECTION:-mysql}" = "sqlite" ]; then
    sqlite_db_path="${DB_DATABASE:-/var/www/html/storage/database.sqlite}"
    mkdir -p "$(dirname "${sqlite_db_path}")"
    touch "${sqlite_db_path}"
fi

if [ "${APP_ENV:-production}" = "local" ] && [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

php artisan config:clear >/dev/null 2>&1 || true

if [ "${CONTAINER_ROLE:-app}" = "app" ]; then
    exec php-fpm -F
fi

if [ "${CONTAINER_ROLE:-app}" = "queue" ]; then
    exec php artisan queue:work --tries=3 --timeout=90
fi

if [ "${CONTAINER_ROLE:-app}" = "scheduler" ]; then
    exec php artisan schedule:work
fi

if [ "${CONTAINER_ROLE:-app}" = "reverb" ]; then
    exec php artisan reverb:start --host=0.0.0.0 --port="${REVERB_SERVER_PORT:-8080}"
fi

echo "Unknown CONTAINER_ROLE: ${CONTAINER_ROLE}" >&2
exit 1
