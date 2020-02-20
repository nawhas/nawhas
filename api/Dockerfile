FROM szainmehdi/php:7.4-nginx

WORKDIR /var/www/api
ENV APP_ENV production
ENV COMPOSER_ALLOW_SUPERUSER 1

COPY . .

RUN composer install -n --no-dev --prefer-dist --no-scripts --no-autoloader \
    && composer dump-autoload -n -o --no-dev \
    && composer check-platform-reqs

RUN chown -R www-data: /var/www/api

COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/entrypoint.sh /usr/bin/docker-nawhas-api-entrypoint
RUN chmod +x /usr/bin/docker-nawhas-api-entrypoint

ENTRYPOINT [ "docker-nawhas-api-entrypoint" ]
CMD []
