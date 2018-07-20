Vagrant.configure("2") do |jinya|
  jinya.vm.box = "generic/ubuntu1804"
  jinya.vm.provider :libvirt do |libvirt|
    libvirt.driver = "kvm"
    libvirt.cpus = 2
    libvirt.memory = 2048
  end

  jinya.vm.network :private_network, ip: "33.33.33.10"
  # jinya.vm.network :forwarded_port, guest: 80, host: 80
  # jinya.vm.network :forwarded_port, guest: 8025, host: 8025
  jinya.vm.synced_folder ".", "/jinya", type: "nfs"
  jinya.vm.provision "shell", path: "./vagrant-files/provision.sh", keep_color: true
end
