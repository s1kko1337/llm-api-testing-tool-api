# PHP CLI
FROM php:8.4.1-cli-alpine3.21 AS cli

COPY ./docker/php/cli/conf.d/* $PHP_INI_DIR/conf.d
COPY ./docker/php/php.ini-production $PHP_INI_DIR/php.ini

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions @composer-2 opcache gd pgsql pdo_pgsql

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1

RUN apk --no-cache add bash unzip zip

COPY ./ /app

WORKDIR /app

RUN rm -rf /app/vendor \
    && composer --no-ansi --no-interaction install --no-progress

# PHP-FPM
FROM php:8.4.1-fpm-alpine3.21 AS fpm

COPY ./docker/php/fpm/conf.d/* $PHP_INI_DIR/conf.d
COPY ./docker/php/php.ini-production $PHP_INI_DIR/php.ini

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions opcache gd pgsql pdo_pgsql

RUN apk --no-cache add bash

COPY --from=cli /app /var/www/html

WORKDIR /var/www/html

RUN chown -R 1000:1000 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

USER 1000

CMD php-fpm
EXPOSE 9000

# NGINX
FROM nginx:stable-alpine3.21 AS nginx

COPY docker/nginx/conf.d/* /etc/nginx/conf.d

COPY --from=cli /app/public /var/www/html/public

RUN apk update && apk upgrade && apk add --no-cache bash

ARG PHP_UPSTREAM_CONTAINER=php-fpm
ARG PHP_UPSTREAM_PORT=9000
RUN echo "upstream php-upstream { server ${PHP_UPSTREAM_CONTAINER}:${PHP_UPSTREAM_PORT}; }" > /etc/nginx/conf.d/upstream.conf

EXPOSE 80
