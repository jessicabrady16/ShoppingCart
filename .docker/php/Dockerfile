FROM php:8.3-fpm

# system deps
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libicu-dev libonig-dev libxml2-dev libpq-dev \
 && docker-php-ext-install pdo pdo_mysql intl zip opcache \
 && rm -rf /var/lib/apt/lists/*

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
