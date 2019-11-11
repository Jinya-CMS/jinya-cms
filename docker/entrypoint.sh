#!/usr/bin/env bash
cp -r /var/www/jinya/* /var/www/html/
touch /var/www/html/.env
mkdir /var/www/html/public/public/
chown -R www-data:www-data /var/www/html/
apache2-foreground
exec "$@"
