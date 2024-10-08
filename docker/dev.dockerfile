FROM registry.ulbricht.casa/jinya-cms/jinya-cms-php-base-test-image:apache

EXPOSE 80

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN echo "apc.enable_cli=1" >> /usr/local/etc/php/php.ini
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.discover_client_host=yes" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.log_level=0" >> /usr/local/etc/php/conf.d/xdebug.ini
#RUN echo "xdebug.mode=profile" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.discover_client_host=yes" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.output_dir=/var/www/html/profile" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.use_compression = false" >> /usr/local/etc/php/conf.d/xdebug.ini
RUN install-php-extensions gd

RUN a2enmod rewrite
RUN a2enmod headers

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

CMD ["apache2-foreground"]