FROM php:7.3-apache-stretch

# install the PHP extensions we need
RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install -y --no-install-recommends \
        libcurl4-openssl-dev \
        libevent-dev \
        libfreetype6-dev \
        libicu-dev \
        libjpeg-dev \
        libmcrypt-dev \
        libpng-dev \
        libpq-dev \
        libxml2-dev \
        libzip-dev \
        unzip
RUN pecl channel-update pecl.php.net
RUN docker-php-ext-configure gd --with-freetype-dir=/usr --with-png-dir=/usr --with-jpeg-dir=/usr
RUN docker-php-ext-install exif gd intl opcache pcntl pdo_mysql zip curl

VOLUME /var/www/html

RUN a2enmod rewrite

ADD docker/vhost.conf /etc/apache2/sites-available/000-default.conf
ADD docker/conf/memory-limit.ini /usr/local/etc/php/conf.d/memory-limit.ini
ADD docker/conf/opcache.ini /usr/local/etc/php/conf.d/opcache-recommended.ini
ADD . /var/www/html/

ENTRYPOINT ["apache2-foreground"]
EXPOSE 80
