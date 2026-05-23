FROM php:8.2-fpm-alpine

# Dependências do sistema
RUN apk add --no-cache \
    bash curl git zip unzip \
    libpng-dev libxml2-dev \
    nodejs npm \
    freetype-dev libjpeg-turbo-dev

# Extensões PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install \
    pdo_mysql mbstring xml bcmath \
    tokenizer ctype fileinfo zip gd

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Instala dependências PHP
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

# Instala dependências JS e compila assets
COPY package.json package-lock.json ./
RUN npm ci

# Copia o restante do código
COPY . .

# Compila assets
RUN npm run build

# Permissões
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["/var/www/html/docker/start.sh"]
