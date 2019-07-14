# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

    config.vm.box = "scotch/box"
    config.vm.network "private_network", ip: "192.168.33.10"
    config.vm.hostname = "personal-homepage.devel"
    config.vm.synced_folder "./", "/var/www", :mount_options => ["dmode=777", "fmode=777"]
    config.vm.provision "shell", path: "provision.sh"
    config.vm.network "forwarded_port", guest: 80, host: 8080

end
