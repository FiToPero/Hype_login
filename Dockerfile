# ---------- Base image with PHP + Composer ----------
FROM php:8.3-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    git \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    oniguruma-dev \
    bash \
    icu-dev \
    zlib-dev \
    libxml2-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pdo_mysql \
    intl \
    zip \
    opcache \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

# Install Redis extension via PECL
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

# Uncomment extension in php.ini templates
RUN sed -i 's/;extension=intl/extension=intl/g' /usr/local/etc/php/php.ini-development \
    && sed -i 's/;extension=intl/extension=intl/g' /usr/local/etc/php/php.ini-production

RUN sed -i 's/;opcache.enable=1/opcache.enable=1/g' /usr/local/etc/php/php.ini-development \
    && sed -i 's/;opcache.enable=1/opcache.enable=1/g' /usr/local/etc/php/php.ini-production

RUN sed -i 's/;opcache.memory_consumption=128/opcache.memory_consumption=128/g' /usr/local/etc/php/php.ini-development \
    && sed -i 's/;opcache.memory_consumption=128/opcache.memory_consumption=128/g' /usr/local/etc/php/php.ini-production

RUN sed -i 's/;opcache.max_accelerated_files=10000/opcache.max_accelerated_files=10000/g' /usr/local/etc/php/php.ini-development \
    && sed -i 's/;opcache.max_accelerated_files=10000/opcache.max_accelerated_files=10000/g' /usr/local/etc/php/php.ini-production


# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


# ---------- Development stage ----------
FROM base AS dev

# Install Node.js for frontend assets (optional, for Laravel Mix/Vite)
RUN apk add --no-cache nodejs npm

# Set development environment
ENV APP_ENV=local
ENV APP_DEBUG=true

WORKDIR /var/www/html


# Create app user and group
RUN addgroup -g 1000 app && adduser -D -u 1000 -G app app

# Give FULL permissions
RUN chown -R app:app /var/www/html \
    && chmod -R 775 /var/www/html

# Switch to app user
USER app

CMD ["php-fpm"]


# ---------- Production stage ----------
FROM base AS prod

# Set production environment
ENV APP_ENV=production
ENV APP_DEBUG=false

WORKDIR /var/www/html

# Copy app source
COPY . /var/www/html

# Install PHP dependencies without dev
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Set restrictive permissions for production security
RUN chown -R www-data:www-data /var/www/html

USER www-data

# Cache Laravel configuration and routes
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

CMD ["php-fpm"]

