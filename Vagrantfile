Vagrant.configure("2") do |jinya|
  jinya.vm.box = "bento/ubuntu-18.04"
  jinya.vm.provider :virtualbox do |virtualbox|
    virtualbox.name = "jinya"
    virtualbox.cpus = 2
    virtualbox.memory = 2048
  end

  jinya.vm.network :private_network, ip: "33.33.33.10"
  jinya.vm.synced_folder ".", "/jinya"
  jinya.vm.provision "shell", path: "./vagrant-files/provision.sh", keep_color: true
end
