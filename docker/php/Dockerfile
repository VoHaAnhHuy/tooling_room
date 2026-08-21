FROM php:8.3-fpm

# Cài đặt các thư viện hệ thống và PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip unzip git \
    && docker-php-ext-install pdo_mysql gd

# Cài đặt Composer vào container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/backend