FROM php:8.3-fpm

# Gunakan repo Debian 13 (Trixie)
RUN echo "deb http://deb.debian.org/debian trixie main contrib non-free non-free-firmware" > /etc/apt/sources.list \
    && apt-get update && apt-get install -y \
    git unzip zip libpng-dev libjpeg62-turbo-dev libfreetype6-dev libzip-dev zlib1g-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
