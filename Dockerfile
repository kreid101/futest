FROM php:8.3-fpm-bookworm AS app

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev libsqlite3-dev libicu-dev libzip-dev libonig-dev libxml2-dev unzip gosu \
    && docker-php-ext-install -j$(nproc) pdo_pgsql pdo_sqlite mbstring intl zip bcmath pcntl opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*
# Fail the build immediately if PostgreSQL support is unavailable.
RUN php -r 'if (!in_array("pgsql", PDO::getAvailableDrivers(), true)) { fwrite(STDERR, "Missing pdo_pgsql extension\n"); exit(1); }'
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x docker/php/entrypoint.sh
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
ENTRYPOINT ["/bin/sh", "/var/www/html/docker/php/entrypoint.sh"]
CMD ["php-fpm"]

FROM nginx:1.28-alpine AS nginx
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/html/public /var/www/html/public
