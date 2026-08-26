FROM php:8.3-fpm-alpine

RUN apk add --no-cache libpq-dev $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_pgsql \
    && pecl install pcov \
    && docker-php-ext-enable pcov \
    && apk del $PHPIZE_DEPS 

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
