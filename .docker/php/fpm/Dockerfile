FROM ghcr.io/kuaukutsu/php:8.3-fpm as app_setup

# Arguments
ARG UID=10001
ARG WORKDIR="/src"

# Configure
COPY conf/php.ini /usr/local/etc/php/php.ini
COPY conf/www.conf.default /usr/local/etc/php-fpm.d/www.conf.default
COPY conf/www.conf /usr/local/etc/php-fpm.d/www.conf

# Dependencies
COPY --from=composer:latest --link /usr/bin/composer /usr/bin/composer

# Add user
RUN adduser -u $UID -G www-data -s /bin/sh -D developer www-data

# Create structure application
RUN mkdir -p $WORKDIR \
    && chown -R $UID:www-data $WORKDIR

FROM app_setup AS app_stage

# Extensions

USER $UID
WORKDIR $WORKDIR

# /usr/bin/composer update --no-dev --no-interaction --no-progress --optimize-autoloader

EXPOSE 9000

FROM app_setup AS app_devel

# xDebug
RUN install-php-extensions xdebug

USER $UID
WORKDIR $WORKDIR

EXPOSE 9000
