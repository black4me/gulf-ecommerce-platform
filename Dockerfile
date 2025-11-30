FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    git \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www

RUN composer install --no-interaction --prefer-dist

RUN npm install

RUN npm run production

RUN chown -R www-data:www-data /var/www

EXPOSE 9000

CMD ["php-fpm"]
