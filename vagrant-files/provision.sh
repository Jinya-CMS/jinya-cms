#!/usr/bin/env bash

echo I am provisioning...
echo Update system
sudo apk update
sudo apk upgrade

sudo apk add rsync

echo Install database
DB_DATA_PATH="/var/lib/mysql"
DB_ROOT_PASS="start"
DB_USER="jinya"
DB_PASS="jinya"
MAX_ALLOWED_PACKET="200M"

sudo apk add mysql mysql-client
sudo mysql_install_db --user=mysql --datadir=${DB_DATA_PATH}
sudo rc-service mariadb restart
sudo mysqladmin -u root password "${DB_ROOT_PASS}"

sudo mysql -u root -pstart -Bse "CREATE DATABASE IF NOT EXISTS jinya;GRANT ALL PRIVILEGES ON *.* TO 'jinya'@'%' IDENTIFIED BY 'jinya';FLUSH PRIVILEGES;"
sudo mysql -u root -pstart -Bse "quit"
sudo mysql -u root -pstart "jinya" < "/vagrant/vagrant-files/jinya-gallery-cms.sql"
sudo cp /vagrant/vagrant-files/maria-server-jinya.cnf /etc/my.cnf.d/maria-server-jinya.cnf

echo Install PHP 7
sudo apk add php7 php7-json php7-xml php7-fpm php7-pdo_mysql php7-zip php7-cli php7-common php7-opcache php7-curl php7-intl php7-mbstring php7-pecl-xdebug

echo Install apache2
sudo apk add apache2 php7-apache2
sudo mkdir -p /var/www/log

echo Set vhost
sudo rc-service apache2 stop
sudo cp /vagrant/vagrant-files/000-default.conf /etc/apache2/conf.d/default.conf

echo Enable xdebug
sudo cp /vagrant/vagrant-files/20-xdebug.ini /etc/php7/conf.d/xdebug.ini
sudo rc-service apache2 start

sudo apk add nodejs

echo Install mailhog
sudo wget --quiet -O /usr/local/bin/mailhog https://github.com/mailhog/MailHog/releases/download/v1.0.0/MailHog_linux_amd64

sudo ln -s /vagrant/vagrant-files/mailhog.sh /etc/init.d/mailhog
sudo chmod +x /etc/init.d/mailhog
sudo rc-update add mailhog default
sudo rc-service mailhog start

echo Prepare service to compile theme on changes
sudo apk add inotify-tools rsync
sudo ln -s /vagrant/vagrant-files/compile-theme.sh /etc/init.d/compile-theme
sudo chmod +x /etc/init.d/compile-theme
sudo rc-update add compile-theme default
sudo rc-service compile-theme start

echo I finished provisioning
