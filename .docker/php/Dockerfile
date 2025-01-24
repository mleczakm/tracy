FROM php:8.4-cli-alpine AS php-base

# Install composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/.composer

WORKDIR /app

RUN --mount=type=bind,from=mlocati/php-extension-installer:latest,source=/usr/bin/install-php-extensions,target=/usr/local/bin/install-php-extensions \
    IPE_SWOOLE_WITHOUT_IOURING=1 install-php-extensions ds intl opcache swoole

CMD ["/app/server"]

COPY composer.json composer.lock ./

RUN --mount=type=bind,from=composer/composer:2-bin,source=/composer,target=/usr/local/bin/composer \
    composer install --no-dev --no-progress -anq --no-scripts

FROM php-base AS php-prod

EXPOSE 80

COPY config ./config
COPY src ./src
COPY bin ./bin
COPY .env ./

RUN composer install --no-dev --no-progress -anq && \
    composer dump-env prod

ARG APP_VERSION
ENV APP_VERSION=$APP_VERSION

FROM php-base AS php-dev-base

COPY --from=composer/composer:2-bin /composer /usr/bin/composer

EXPOSE 8080

RUN --mount=type=bind,from=mlocati/php-extension-installer:latest,source=/usr/bin/install-php-extensions,target=/usr/local/bin/install-php-extensions \
    install-php-extensions pcov xdebug

FROM php-dev-base AS php-dev

COPY config ./config
COPY public ./public
COPY src ./src
COPY tests ./tests
COPY bin ./bin
COPY .env  ./

RUN echo "APP_ENV=dev" > .env.local && composer install --no-progress -anq && composer dump-env prod