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

# Entrypoint script
RUN echo '#!/bin/bash\n\
touch /tmp/database.sqlite\n\
php artisan migrate --force\n\
php artisan db:seed --force\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
php artisan serve --host=0.0.0.0 --port=$PORT' > /app/start.sh \
&& chmod +x /app/start.sh

EXPOSE 8000
CMD ["/bin/bash", "/app/start.sh"]