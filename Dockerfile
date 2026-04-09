FROM php:8.3-fpm

# Install dependensi sistem
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install ekstensi PHP yang dibutuhkan Laravel & MySQL
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Ambil Composer terbaru
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy seluruh file project ke dalam container
COPY . /var/www

# Atur izin akses folder storage dan cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]