FROM php:7.4-apache

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN apt-get update
RUN apt-get install libzip-dev -y
RUN docker-php-ext-install pdo pdo_mysql zip
RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.remote_autostart=on" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.client_host=172.19.0.1" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini
RUN a2enmod rewrite
RUN a2enmod headers
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf