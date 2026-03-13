FROM composer:2 AS composer_deps

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader

FROM node:22-alpine AS frontend_assets

WORKDIR /var/www/html

COPY package.json pnpm-lock.yaml ./
RUN corepack enable && pnpm install --frozen-lockfile

COPY resources ./resources
COPY public ./public
COPY vite.config.ts tsconfig.json tailwind.config.ts ./

RUN pnpm run build

FROM php:8.4-fpm-alpine AS app

WORKDIR /var/www/html

RUN apk add --no-cache \
        bash \
        curl \
        ffmpeg \
        freetype-dev \
        git \
        icu-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libwebp-dev \
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
        mbstring \
        pdo_mysql \
        pcntl \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/pear

COPY --from=composer_deps /var/www/html/vendor ./vendor
COPY . .
COPY --from=frontend_assets /var/www/html/public/build ./public/build

COPY docker-entrypoint.sh /usr/local/bin/start-container
RUN chmod +x /usr/local/bin/start-container \
    && chown -R www-data:www-data storage bootstrap/cache

ENV CONTAINER_ROLE=app

ENTRYPOINT ["/usr/local/bin/start-container"]
