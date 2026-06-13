FROM node:22 AS assets

WORKDIR /app

COPY package*.json ./
RUN npm install --no-audit --no-fund

COPY . .
RUN npm run build


FROM php:8.3-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    default-mysql-client \
    git \
    libicu-dev \
    libonig-dev \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install bcmath exif gd intl mbstring pcntl pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
COPY --from=assets /app/public/build ./public/build
COPY start.sh /usr/local/bin/start.sh

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress \
    && chmod +x /usr/local/bin/start.sh \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 10000

CMD ["/usr/local/bin/start.sh"]
