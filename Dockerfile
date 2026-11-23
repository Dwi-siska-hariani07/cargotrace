FROM dunglas/frankenphp:php8.2-alpine

# Install required PHP extensions for Laravel & PostgreSQL
RUN install-php-extensions \
    pdo_pgsql \
    pdo_mysql \
    gd \
    intl \
    zip \
    bcmath \
    opcache

# Use production php.ini settings
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Get Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy application files
COPY . /app

# Copy Caddyfile
COPY Caddyfile /etc/caddy/Caddyfile

# Fix Windows CRLF line endings on shell scripts
RUN sed -i 's/\r$//' /app/entrypoint.sh && chmod +x /app/entrypoint.sh

# Install PHP dependencies without dev packages
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Set storage and bootstrap cache permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

EXPOSE 80

ENTRYPOINT ["/app/entrypoint.sh"]
