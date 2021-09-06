FROM php:7.3-fpm-alpine

ARG INFRA_DIR

RUN apk add --no-cache --update \
    nginx \
    zlib-dev \
    icu-dev \
    libev \
    git \
    python3 \
    py-pip \
    nodejs \
    autoconf \
    g++ \
    libtool \
    make \
    && pip install supervisor \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-configure pdo_mysql \
    && docker-php-ext-install pdo_mysql \
    && docker-php-source extract \
    && apk add --no-cache --virtual .phpize-deps-configure $PHPIZE_DEPS \
    #APC
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-enable opcache \
    #cleanup
    && apk del .phpize-deps-configure \
    && docker-php-source delete \
    && rm -rf /var/cache/apk/* && rm -rf /tmp/*

#nginc config
#ADD ${INFRA_DIR}/nginx.conf /etc/nginx/
#ADD ${INFRA_DIR}/symfony.conf /etc/nginx/conf.d/default.conf
##php config
#ADD ${INFRA_DIR}/php.ini /usr/local/etc/php/php.ini
#ADD ${INFRA_DIR}/php-fpm.pool.conf /usr/local/etc/php-fpm.d/docker.conf
#
##supervisor
#COPY ${INFRA_DIR}/supervisord.conf /etc/supervisord.conf

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --quiet --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/server

# install composer dependencies - separate to cache
#COPY composer.json composer.lock ./
#RUN composer install --no-dev --no-scripts --no-autoloader

# add code
#COPY . ./

#ENV SYMFONY_ENV=prod

#RUN mkdir ./var && \
#    composer dump-autoload --optimize --apcu && \
##	composer run-script post-install-cmd && \
#    chown -R www-data:www-data ./var
#
#CMD ["supervisord", "--nodaemon", "--configuration", "/etc/supervisord.conf"]
#
#EXPOSE 80
