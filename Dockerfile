FROM library/php:8.3-apache

EXPOSE 80

COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/install-php-extensions
COPY --from=library/composer:latest /usr/bin/composer /usr/local/bin/composer

COPY ./docker/conf/memory-limit.ini /usr/local/etc/php/conf.d/memory-limit.ini
COPY ./docker/conf/opcache.ini /usr/local/etc/php/conf.d/opcache-recommended.ini
COPY ./docker/entrypoint.sh /entrypoint.sh

COPY --chown=www-data ./ /var/www/jinya/

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN apt-get update
RUN apt-get install -y git unzip
RUN install-php-extensions pdo pdo_mysql zip opcache intl curl apcu imagick gd

WORKDIR /var/www/jinya
RUN composer install --no-dev --no-progress --optimize-autoloader --apcu-autoloader --no-interaction
WORKDIR /var/www/html

RUN a2enmod rewrite
RUN a2enmod headers

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
