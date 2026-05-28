FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip libsqlite3-dev nodejs npm \
    && docker-php-ext-install pdo pdo_sqlite \
    && apt-get clean

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8000

CMD cp .env.example .env && \
    php artisan key:generate && \
    touch /tmp/database.sqlite && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    php artisan serve --host=0.0.0.0 --port=$PORT