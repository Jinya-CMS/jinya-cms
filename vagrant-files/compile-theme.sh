#!/usr/bin/env bash
while inotifywait -r -e modify,create /opt/jinya/themes/; do
    php /opt/jinya/bin/console jinya:theme:compile
    php /opt/jinya/bin/console jinya:cache:compile
done