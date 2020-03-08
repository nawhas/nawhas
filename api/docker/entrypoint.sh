#!/usr/bin/env sh
set -xe

if [ $# -eq 0 ]; then
  php artisan boot
  exec docker-entrypoint
else
  exec docker-entrypoint "$@"
fi
