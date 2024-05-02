FROM php:8.1-fpm

RUN apt update && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip libfreetype6-dev libjpeg62-turbo-dev \
    && docker-php-ext-install intl opcache pcntl pdo pdo_mysql \
    && pecl install apcu \
    && pecl install grpc \
    && docker-php-ext-enable apcu \
    && docker-php-ext-enable grpc \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && docker-php-ext-configure gd \
    && docker-php-ext-install gd


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

USER root

WORKDIR /var/www/html
