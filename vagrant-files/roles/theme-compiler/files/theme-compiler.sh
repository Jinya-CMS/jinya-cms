#!/usr/bin/env bash
while inotifywait -r -e modify,create /vagrant/themes/; do
    php /vagrant/bin/console jinya:theme:compile
    php /vagrant/bin/console jinya:cache:compile
done