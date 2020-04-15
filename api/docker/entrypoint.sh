#!/usr/bin/env sh
set -xe

if [ $# -eq 0 ]; then
  php artisan boot
  exec php-fpm --nodaemonize
else
  exec "$@"
fi
