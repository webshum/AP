FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libbz2-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    vim && \
    docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql \
    bcmath \
    zip \
    gd

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g pm2

RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www