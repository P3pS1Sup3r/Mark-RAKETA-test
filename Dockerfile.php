# Dockerfile.php
FROM php:8.2-fpm

# Установим необходимые расширения (пример: pdo_mysql)
RUN apt-get update && \
    apt-get install -y git unzip libzip-dev && \
    docker-php-ext-install pdo pdo_mysql sockets zip

# Установим Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

# (Опционально) Установи дополнительные пакеты, если нужно

WORKDIR /var/www/html