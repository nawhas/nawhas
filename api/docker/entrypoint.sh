#!/usr/bin/env sh
set -xe

if [ $# -eq 0 ]; then
  php artisan boot
  exec docker-fpm-entrypoint
else
  exec docker-fpm-entrypoint "$@"
fi
