FROM php:8.0-apache

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

ADD . /var/www/html

RUN apt-get update
RUN apt-get install libzip-dev git -y
RUN docker-php-ext-install pdo pdo_mysql zip
RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.discover_client_host=yes" >> /usr/local/etc/php/conf.d/xdebug.ini
RUN a2enmod rewrite
RUN a2enmod headers
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf