from php:8.3-fpm

RUN apt-get update && apt-get install -y \
    zip unzip libonig-dev libxml2-dev libzip-dev libicu-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring xml intl zip gd opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN echo "upload_max_filesize = 20M\npost_max_size = 25M\nmemory_limit = 256M" > /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html

COPY . .

RUN mkdir -p /var/www/html/storage/app/public/events/banners \
    /var/www/html/storage/app/public/events/vbg \
    /var/www/html/storage/app/public/profile-photos \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["sh", "-c", "mkdir -p /var/www/html/storage/app/public/events/banners /var/www/html/storage/app/public/events/vbg /var/www/html/storage/app/public/profile-photos && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache && exec php-fpm"]