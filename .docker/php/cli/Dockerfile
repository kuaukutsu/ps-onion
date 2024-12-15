FROM ghcr.io/kuaukutsu/php:8.3-cli as app_cli_build

FROM app_cli_build AS app_setup

# Arguments
ARG UID=10001
ARG WORKDIR="/src"

# Configure
COPY conf/php.ini /usr/local/etc/php/php.ini

# Dependencies
COPY --from=composer:latest --link /usr/bin/composer /usr/bin/composer

# Add user
RUN adduser -u $UID -G www-data -s /bin/sh -D developer www-data

# Create structure application
RUN mkdir -p $WORKDIR \
    && chown -R $UID:www-data $WORKDIR

FROM app_setup AS app_stage

USER $UID
WORKDIR $WORKDIR

FROM app_setup AS app_devel

# xDebug
RUN install-php-extensions xdebug

USER $UID
WORKDIR $WORKDIR

FROM app_setup AS tools

USER $UID

# cpx
RUN composer global require cpx/cpx

WORKDIR $WORKDIR

FROM tools AS tools_psalm

RUN ~/.composer/vendor/bin/cpx psalm

FROM tools AS tools_phpstan

RUN ~/.composer/vendor/bin/cpx phpstan
