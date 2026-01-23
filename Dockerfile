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

# Збільшуємо memory_limit для PHP-FPM і CLI
RUN echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/memory-limit.ini && \
    echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/memory-limit.ini && \
    echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/memory-limit.ini && \
    echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/memory-limit.ini

# Додаткові рекомендації для WordPress/Bedrock
RUN echo "define('WP_MEMORY_LIMIT', '512M');" > /usr/local/etc/php/conf.d/wp-memory.php && \
    echo "define('WP_MAX_MEMORY_LIMIT', '1024M');" >> /usr/local/etc/php/conf.d/wp-memory.php

WORKDIR /var/www