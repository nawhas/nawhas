#!/usr/bin/env sh
set -xe

ENV=$(php artisan inspect:env)

if [ "$ENV" = "local" ]; then
    echo 'Running in local mode.';
    exec "$@"
else
    # Cache configuration
    php artisan config:cache

    # Wait for the database.
    php artisan wait:database

    # Run Migrations
    php artisan doctrine:migrations:migrate --force --allow-no-migration

    # Clear doctrine metadata cache
    php artisan doctrine:clear:metadata:cache

    # Generate proxies
    php artisan doctrine:generate:proxies

    exec "$@"
fi;
