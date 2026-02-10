FROM registry.ulbricht.casa/jinya-cms/jinya-cms-php-base-test-image:frankenphp

EXPOSE 80

ENV SERVER_NAME=:80

RUN echo "apc.enable_cli=1" >> /usr/local/etc/php/php.ini
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.discover_client_host=yes" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.log_level=0" >> /usr/local/etc/php/conf.d/xdebug.ini
#RUN echo "xdebug.mode=profile" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.discover_client_host=yes" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.output_dir=/var/www/html/profile" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.use_compression = false" >> /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /var/www/html