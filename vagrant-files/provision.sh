#!/usr/bin/env bash

echo I am provisioning...
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update

echo Copy files
sudo mkdir -p /opt/jinya/
sudo rsync -a --exclude '.git' --exclude '.idea' --exclude '.vagrant' --exclude '.scannerwork' --exclude '.circleci' --exclude 'vagrant-files' /jinya /opt/
sudo chmod -R 777 /opt/jinya/

echo Install database
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password start'
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password start'
sudo apt-get -y install mysql-server mysql-client
mysql -u root -pstart -Bse "CREATE DATABASE IF NOT EXISTS jinya;GRANT ALL PRIVILEGES ON *.* TO 'jinya'@'%' IDENTIFIED BY 'jinya';FLUSH PRIVILEGES;"
mysql -u jinya -pjinya -Bse "quit"
mysql -u jinya -pjinya "jinya" < "/jinya/vagrant-files/jinya-gallery-cms.sql"
sudo cp /jinya/vagrant-files/mysqld.cnf /etc/mysql/mysql.conf.d/
sudo service mysql restart

echo Install PHP 7.2
sudo apt-get install -y php7.2 php7.2-json php7.2-xml php7.2-fpm php7.2-mysql php7.2-zip php7.2-cli php7.2-common php7.2-opcache php7.2-curl php7.2-intl php7.2-mbstring php-xdebug

echo Install tideways
sudo apt install -y php7.2-dev make
git clone https://github.com/tideways/php-profiler-extension.git
cd php-profiler-extension
phpize
./configure
make
sudo make install
echo "extension=tideways_xhprof.so" | sudo tee /etc/php/7.2/mods-available/tideways_xhprof.ini
sudo phpenmod tideways_xhprof

echo Install apache2
sudo apt-get install -y apache2 libapache2-mod-php

echo Set vhost
sudo service apache2 stop
sudo a2enmod rewrite
sudo cp /jinya/vagrant-files/000-default.conf /etc/apache2/sites-available/000-default.conf

echo Enable xdebug
sudo cp /jinya/vagrant-files/20-xdebug.ini /etc/php/7.2/mods-available/xdebug.ini
sudo phpenmod xdebug
sudo service apache2 start

echo Install mailhog
sudo wget --quiet -O /usr/local/bin/mailhog https://github.com/mailhog/MailHog/releases/download/v1.0.0/MailHog_linux_amd64
sudo chmod +x /usr/local/bin/mailhog

sudo tee /etc/systemd/system/mailhog.service <<EOL
[Unit]
Description=MailHog Service
After=network.service vagrant.mount

[Service]
Type=simple
ExecStart=/usr/bin/env /usr/local/bin/mailhog > /dev/null 2>&1 &

[Install]
WantedBy=multi-user.target
EOL

sudo systemctl enable mailhog
sudo systemctl start mailhog

echo I finished provisioning