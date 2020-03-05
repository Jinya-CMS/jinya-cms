FROM php:7.4-apache

VOLUME /var/www/html
ENV APP_ENV prod

# Update apt and install gpg
RUN apt-get update
RUN apt-get install gnupg2 -y

# Set apt repo for yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list

# Setup nodejs
RUN curl -sL https://deb.nodesource.com/setup_13.x | bash -

# Install required packages
RUN apt-get update
RUN apt-get install -y --no-install-recommends \
        libcurl4-openssl-dev \
        libevent-dev \
        libfreetype6-dev \
        libicu-dev \
        libjpeg-dev \
        libmcrypt-dev \
        libpng-dev \
        libxml2-dev \
        libxslt1-dev \
        libzip-dev \
        wget \
        nodejs \
        yarn

# Install the PHP extensions we need
RUN pecl channel-update pecl.php.net
RUN docker-php-ext-install exif intl opcache pcntl pdo_mysql zip curl gd xsl

# Enable apache rewrite
RUN a2enmod rewrite

WORKDIR /var/www/jinya

# Copy config files
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/conf/memory-limit.ini /usr/local/etc/php/conf.d/memory-limit.ini
COPY docker/conf/opcache.ini /usr/local/etc/php/conf.d/opcache-recommended.ini
COPY docker/entrypoint.sh /entrypoint.sh
COPY --chown=www-data . /var/www/jinya/

# Install composer
RUN wget https://getcomposer.org/download/1.9.3/composer.phar

# Install dependencies
RUN php composer.phar install --no-dev --no-scripts
RUN yarn

# Compile jinya
RUN php composer.phar dumpautoload --optimize
RUN yarn encore dev

# Clean installation
RUN rm -rf /var/www/jinya/var/log/ /var/www/jinya/var/cache/ /var/www/jinya/config/install.lock /var/www/jinya/public/public/ /var/www/jinya/vagrant-files/
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
EXPOSE 80
