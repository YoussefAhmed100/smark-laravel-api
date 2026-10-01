FROM php:8.4-cli

# System deps + PHP extensions (pdo_pgsql is the one Laravel needs for PostgreSQL)
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libpq-dev libzip-dev libonig-dev \
    && docker-php-ext-install pdo_pgsql pgsql zip mbstring bcmath pcntl \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install dependencies first (better layer caching)
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-scripts --no-autoloader --prefer-dist

# Copy the app
COPY . .
RUN composer dump-autoload --optimize \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]