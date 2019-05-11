#!/usr/bin/env bash
while inotifywait -r -e modify,create /var/www/jinya/themes/; do
    cd /var/www/jinya/themes/jinya-default-theme/
    /var/www/jinya/themes/node_modules/.bin/gulp
    cd /var/www/jinya
    php /var/www/jinya/bin/console jinya:theme:compile
    php /var/www/jinya/bin/console jinya:cache:compile
done