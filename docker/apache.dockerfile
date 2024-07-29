FROM registry.ulbricht.casa/jinya-cms/jinya-cms-php-base-image:apache

COPY --chown=www-data ./ /var/www/jinya/

WORKDIR /var/www/jinya
RUN composer install --no-dev --no-progress --optimize-autoloader --apcu-autoloader --no-interaction

WORKDIR /var/www/html
