#!/bin/bash
set -e

# Установить зависимости, если нужно
composer install || true

# Запустить php-fpm
php-fpm
