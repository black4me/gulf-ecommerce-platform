FROM php:8.2-fpm-alpine

WORKDIR /var/www

RUN apk add --no-cache \
    curl \
    mysql-client \
    git \
    npm \
    && docker-php-ext-install pdo_mysql bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-interaction --prefer-dist && npm install && npm run production

RUN chown -R www-data:www-data /var/www

EXPOSE 9000

CMD ["php-fpm"]
