# ---------------------------------------------------------------------------
# Test Image
# ---------------------------------------------------------------------------
FROM --platform=linux/amd64 szainmehdi/php:8.1-dev-20230802 as test

WORKDIR /var/www/api
ENV APP_ENV production
ENV COMPOSER_ALLOW_SUPERUSER 1

COPY composer.json composer.json
COPY composer.lock composer.lock
COPY app/Support/helpers.php app/Support/helpers.php

ENV TELESCOPE_ENABLED=false
RUN composer install --no-scripts

COPY . .
RUN composer run post-root-package-install && php artisan key:generate

# ---------------------------------------------------------------------------
# Build Image
# ---------------------------------------------------------------------------
FROM --platform=linux/amd64 szainmehdi/php:8.1-dev-20230802 as build

WORKDIR /var/www/api
ENV APP_ENV production

COPY . .
COPY --from=test /var/www/api/vendor /var/www/api/vendor
COPY --from=test /tmp/.composer /tmp/.composer

RUN composer install -n --no-dev --prefer-dist --no-scripts --no-autoloader \
    && composer dump-autoload -n -o --no-dev \
    && composer check-platform-reqs

# ---------------------------------------------------------------------------
# Production Image
# ---------------------------------------------------------------------------
FROM --platform=linux/amd64 szainmehdi/php:8.1-20230802 as production

WORKDIR /var/www/api

ARG GITHUB_SHA
ENV GIT_SHA $GITHUB_SHA

COPY --from=build /var/www/api /var/www/api

RUN chown -R www-data: /var/www/api

COPY docker/entrypoint.sh /usr/bin/docker-nawhas-api-entrypoint
RUN chmod +x /usr/bin/docker-nawhas-api-entrypoint

RUN php artisan package:discover \
 && php artisan horizon:publish \
 && php artisan route:cache \
 && php artisan event-sourcing:cache-event-handlers

HEALTHCHECK --interval=1m --timeout=3s CMD php artisan healthcheck
ENTRYPOINT [ "docker-nawhas-api-entrypoint" ]
