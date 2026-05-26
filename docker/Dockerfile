FROM php:8.2-fpm-bullseye

RUN apt-get update && apt-get install -y \
    curl git zip unzip \
    libpng-dev libxml2-dev libzip-dev \
    libfreetype6-dev libjpeg62-turbo-dev \
    libonig-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install \
    pdo_mysql mbstring xml bcmath \
    ctype fileinfo zip gd \
 && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get install -y nodejs \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN composer create-project laravel/laravel . "11.*" \
    --no-interaction --prefer-dist --quiet

RUN composer require \
    livewire/livewire "^3.5" \
    barryvdh/laravel-dompdf "^3.1" \
    --no-interaction --quiet

RUN composer require --dev laravel/breeze "^2.1" \
    --no-interaction --quiet

RUN php artisan breeze:install livewire --no-interaction

RUN npm install --legacy-peer-deps && npm run build

COPY . .

RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["/var/www/html/docker/start.sh"]
