FROM harbor.ulbricht.casa/proxy/library/php:8.2-apache

EXPOSE 80

COPY --from=harbor.ulbricht.cloud/proxy/mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/install-php-extensions

COPY ./docker/conf/memory-limit.ini /usr/local/etc/php/conf.d/memory-limit.ini
COPY ./docker/conf/opcache.ini /usr/local/etc/php/conf.d/opcache-recommended.ini
COPY ./docker/entrypoint.sh /entrypoint.sh

COPY --chown=www-data ./ /var/www/jinya/

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN install-php-extensions pdo pdo_mysql zip opcache intl curl xdebug pcov apcu imagick

RUN a2enmod rewrite
RUN a2enmod headers

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]