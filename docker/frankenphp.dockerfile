FROM registry.ulbricht.casa/jinya-cms/jinya-cms-php-base-image:8.5-frankenphp

COPY --chown=www-data ./ /var/www/jinya/
COPY ./docker/Caddyfile /etc/frankenphp/Caddyfile

WORKDIR /var/www/jinya
RUN composer install --no-dev --no-progress --optimize-autoloader --apcu-autoloader --no-interaction

WORKDIR /var/www/html
