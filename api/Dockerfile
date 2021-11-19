# ---------------------------------------------------------------------------
# Test Image
# ---------------------------------------------------------------------------
FROM szainmehdi/php:7.4-dev-20210813 as test

WORKDIR /var/www/api
ENV APP_ENV production
ENV COMPOSER_ALLOW_SUPERUSER 1

COPY composer.json composer.json
COPY composer.lock composer.lock
COPY database/seeds database/seeds
COPY database/factories database/factories

ENV TELESCOPE_ENABLED=false
RUN composer install --no-scripts

COPY . .
RUN composer run post-root-package-install && php artisan key:generate
RUN ./docker/scripts/psalm.sh
RUN composer test

# ---------------------------------------------------------------------------
# Build Image
# ---------------------------------------------------------------------------
FROM szainmehdi/php:7.4-dev-20210813 as build

WORKDIR /var/www/api
ENV APP_ENV production

COPY . .
COPY --from=test /var/www/api/vendor /var/www/api/vendor
COPY --from=test /tmp/.composer /tmp/.composer

ENV TELESCOPE_ENABLED=false
RUN composer install -n --no-dev --prefer-dist --no-scripts --no-autoloader \
    && composer dump-autoload -n -o --no-dev \
    && composer check-platform-reqs

# ---------------------------------------------------------------------------
# Production Image
# ---------------------------------------------------------------------------
FROM szainmehdi/php:7.4-20210813 as production

WORKDIR /var/www/api

ARG GITHUB_SHA
ARG GITHUB_REF
ENV GIT_SHA $GITHUB_SHA
ENV GIT_BRANCH $GITHUB_REF

COPY --from=build /var/www/api /var/www/api

RUN chown -R www-data: /var/www/api

COPY docker/entrypoint.sh /usr/bin/docker-nawhas-api-entrypoint
RUN chmod +x /usr/bin/docker-nawhas-api-entrypoint

HEALTHCHECK --interval=1m --timeout=3s CMD php artisan healthcheck
ENTRYPOINT [ "docker-nawhas-api-entrypoint" ]
