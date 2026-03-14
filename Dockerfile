FROM composer:2 AS composer_deps

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --ignore-platform-req=ext-exif \
    --prefer-dist \
    --optimize-autoloader

FROM php:8.4-fpm-alpine AS app

WORKDIR /var/www/html

RUN apk add --no-cache \
        $PHPIZE_DEPS \
        bash \
        curl \
        ffmpeg \
        freetype-dev \
        git \
        icu-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libwebp-dev \
        sqlite-dev \
        libzip-dev \
        oniguruma-dev \
        unzip \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install \
        bcmath \
        exif \
        gd \
        intl \
        mbstring \
        pdo_mysql \
        pdo_sqlite \
        pcntl \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/pear

COPY --from=composer_deps /usr/bin/composer /usr/local/bin/composer
COPY --from=composer_deps /var/www/html/vendor ./vendor
COPY . .

COPY docker-entrypoint.sh /usr/local/bin/start-container
RUN chmod +x /usr/local/bin/start-container \
    && php artisan package:discover --ansi \
    && chown -R www-data:www-data storage bootstrap/cache

ENV CONTAINER_ROLE=app

ENTRYPOINT ["/usr/local/bin/start-container"]
