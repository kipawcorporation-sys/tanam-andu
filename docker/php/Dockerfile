FROM php:8.2-fpm-alpine

# Pasang dependency sistem & build tools
RUN apk add --no-cache \
    bash \
    curl \
    git \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    shadow \
    tzdata

# Set timezone ke Asia/Jakarta
ENV TZ=Asia/Jakarta
RUN cp /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone

# Konfigurasi & install ekstensi PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        gd \
        zip \
        mbstring \
        exif \
        bcmath \
        intl \
        opcache

# Salin Composer resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set direktori kerja
WORKDIR /var/www/html

# Salin konfigurasi PHP kustom
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Salin script entrypoint
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# User www-data UID/GID 1000 agar match volume host
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]

