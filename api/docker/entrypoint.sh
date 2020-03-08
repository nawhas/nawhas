#!/usr/bin/env sh
set -xe

# If no command is provided (i.e. container running with default command)
# then we want to boot the application before starting PHP and nginx
if [ $# -eq 0 ]; then
  # php artisan boot
  exec docker-entrypoint
else
  # docker run --rm {container} php artisan migrate
  exec docker-entrypoint "$@"
fi
