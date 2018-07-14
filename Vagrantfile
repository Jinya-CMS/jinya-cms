Vagrant.configure("2") do |jinya|
  provision_script = <<-SCRIPT
echo I am provisioning...
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
echo Install database
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password start'
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password start'
sudo apt-get -y install mysql-server mysql-client
mysql -u root -pstart -Bse "CREATE DATABASE jinya;GRANT ALL PRIVILEGES ON *.* TO 'jinya'@'%' IDENTIFIED BY 'jinya';FLUSH PRIVILEGES;"
mysql -u jinya -pjinya -Bse "quit"
mysql -u jinya -pjinya "jinya" < "/jinya/vagrant-files/jinya-gallery-cms.sql"
echo Install PHP 7.2
sudo apt-get install -y php7.2 php7.2-json php7.2-xml php7.2-fpm php7.2-mysql php7.2-zip php7.2-cli php7.2-common php7.2-opcache php7.2-curl php7.2-intl php7.2-mbstring
echo Install apache2
sudo apt-get install -y apache2 libapache2-mod-php
echo Set vhost
sudo service apache2 stop
sudo a2enmod rewrite
sudo cp /jinya/vagrant-files/000-default.conf /etc/apache2/sites-available/000-default.conf
sudo service apache2 start
bash <(wget -qO- "https://gist.githubusercontent.com/varghesejacob/c31a844042ca5ced6b72ccab3cd6055b/raw/03137373a3cb1d5493e2475b2024d109c4f48e13/Mailhog%2520Bash%2520Script%2520(systemd)")
echo I finished provisioning
  SCRIPT
  jinya.vm.box = "generic/ubuntu1804"
  jinya.vm.provider :libvirt do |libvirt|
    libvirt.driver = "kvm"
    libvirt.cpus = 2
    libvirt.memory = 2048
  end
  jinya.vm.network :forwarded_port, guest: 80, host: 80
  jinya.vm.network :forwarded_port, guest: 8025, host: 8025
  jinya.vm.network :forwarded_port, guest: 1025, host: 1025
  jinya.vm.network :forwarded_port, guest: 3306, host: 3306
  jinya.vm.synced_folder ".", "/jinya", type: "nfs"
  jinya.vm.provision "shell", inline: provision_script
end
