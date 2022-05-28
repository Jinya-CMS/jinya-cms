FROM quay.imanuel.dev/dockerhub/library---php:8.1-apache

COPY ./docker/conf/memory-limit.ini /usr/local/etc/php/conf.d/memory-limit.ini
COPY ./docker/conf/opcache.ini /usr/local/etc/php/conf.d/opcache-recommended.ini
COPY ./docker/entrypoint.sh /entrypoint.sh
COPY --chown=www-data ./ /var/www/jinya/

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN apt-get update
RUN apt-get install libzip-dev libicu-dev libmagickwand-dev -y
RUN docker-php-ext-install pdo pdo_mysql zip opcache intl gd
RUN pecl install imagick
RUN docker-php-ext-enable imagick
RUN a2enmod rewrite
RUN a2enmod headers
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
EXPOSE 80