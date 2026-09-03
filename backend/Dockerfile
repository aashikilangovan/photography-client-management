FROM php:8.3-cli

# libpq-dev is needed to compile the pdo_pgsql extension (Postgres driver);
# unzip/git are needed by Composer when it fetches packages.
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev unzip git \
    && docker-php-ext-install pdo pdo_pgsql pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-interaction --optimize-autoloader

EXPOSE 8000

# `php artisan serve` is fine for local dev / a portfolio project — a real
# production deployment would put this behind php-fpm + nginx instead.
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
