# ============================================================
# Dockerfile untuk Casual Steps — Railway Deployment
# Stack: PHP 8.2 + Apache + MySQL Client
# ============================================================

FROM php:8.2-apache

# -----------------------------------------------------------
# 1. Install dependensi sistem & ekstensi PHP yang dibutuhkan
# -----------------------------------------------------------
RUN apt-get update && apt-get install -y --no-install-recommends \
    # Dependensi untuk ekstensi GD (image processing)
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    # Dependensi untuk ekstensi zip (dibutuhkan Composer & beberapa library)
    libzip-dev \
    unzip \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        mysqli \
        pdo \
        pdo_mysql \
        gd \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# -----------------------------------------------------------
# 2. Aktifkan Apache mod_rewrite & mod_headers
# -----------------------------------------------------------
RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork \
    && a2enmod rewrite headers

# -----------------------------------------------------------
# 3. Konfigurasi Apache — Document Root di /var/www/html
#    Mengizinkan .htaccess override agar aturan keamanan bekerja
# -----------------------------------------------------------
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/custom.conf \
    && a2enconf custom

# -----------------------------------------------------------
# 4. Konfigurasi PHP untuk production
# -----------------------------------------------------------
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Override beberapa setting PHP
RUN echo "upload_max_filesize = 10M\n\
post_max_size = 12M\n\
memory_limit = 256M\n\
max_execution_time = 120\n\
display_errors = Off\n\
log_errors = On\n\
error_log = /dev/stderr" > "$PHP_INI_DIR/conf.d/custom.ini"

# -----------------------------------------------------------
# 5. Install Composer
# -----------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# -----------------------------------------------------------
# 6. Copy seluruh project ke container
# -----------------------------------------------------------
COPY . /var/www/html/

# -----------------------------------------------------------
# 7. Install dependensi PHP via Composer (tanpa dev packages)
# -----------------------------------------------------------
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# -----------------------------------------------------------
# 8. Set permission yang tepat untuk Apache
# -----------------------------------------------------------
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# -----------------------------------------------------------
# 9. Expose port (Railway akan override via $PORT)
# -----------------------------------------------------------
EXPOSE 80

# -----------------------------------------------------------
# 10. Startup: Sesuaikan port Apache dengan $PORT dari Railway,
#     lalu jalankan Apache di foreground
# -----------------------------------------------------------
CMD ["sh", "-c", "\
    sed -i \"s/Listen 80/Listen ${PORT:-80}/g\" /etc/apache2/ports.conf && \
    sed -i \"s/:80/:${PORT:-80}/g\" /etc/apache2/sites-available/000-default.conf && \
    apache2-foreground \
"]
