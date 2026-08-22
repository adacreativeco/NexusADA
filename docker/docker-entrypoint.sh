#!/bin/sh
set -e

# Run migrations if necessary
if [ "$1" = 'php-fpm' ]; then
    echo "Running storage link and config cache..."
    php artisan storage:link --force || true
fi

exec "$@"
