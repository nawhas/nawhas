FROM szainmehdi/php:7.4-dev as api-build

WORKDIR /var/www/api
ENV COMPOSER_ALLOW_SUPERUSER 1

# Quicken Composer Installation by paralleizing downloads
RUN composer global require hirak/prestissimo --prefer-dist

# Copy Dependencies files
COPY ./composer.json ./composer.lock ./

RUN composer install -n --no-dev --prefer-dist --no-scripts --no-autoloader

# ---------------------------------------------------------------------------
FROM szainmehdi/php:7.4

ENV COMPOSER_ALLOW_SUPERUSER 1
WORKDIR /var/www/api
COPY --from=api-build /var/www/api/vendor /var/www/api/vendor
COPY --from=api-build /usr/local/bin/composer /usr/local/bin/composer
COPY . .

RUN composer dump-autoload -n -o --no-dev \
    && composer check-platform-reqs

RUN chown -R www-data: /var/www/api

COPY entrypoint.sh /usr/bin/docker-api-entrypoint
RUN chmod +x /usr/bin/docker-api-entrypoint

ENTRYPOINT ["docker-api-entrypoint"]
EXPOSE 9000
CMD 'php-fpm'
