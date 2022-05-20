# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

    # config.vm.box = "scotch/box"
    # config.vm.box = "damianlewis/ubuntu-16.04-lamp"
    config.vm.box = "generic/ubuntu2004"
    config.vm.network "private_network", ip: "192.168.33.10"
    config.vm.hostname = "personal-homepage.local"
    config.vm.synced_folder "./", "/var/www", owner: "www-data", group: "www-data"
    config.vm.provision "shell", path: "provision.sh"
    config.vm.network "forwarded_port", guest: 80, host: 8080

    config.vm.provider "parallels" do |prl|
        prl.name = "personal-homepage"
    end

end
