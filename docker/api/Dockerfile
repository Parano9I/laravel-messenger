FROM php:8.1-fpm-alpine

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN apk update && apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    zip

RUN rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip && \
    docker-php-ext-configure gd && \
    docker-php-ext-install gd


RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug-3.1.5 \
    && docker-php-ext-enable xdebug

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -S www -G www

COPY --chown=www:www-data ./api /var/www

WORKDIR /var/www
USER www

EXPOSE 9000
CMD ["php-fpm"]