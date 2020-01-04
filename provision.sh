# update the system
apt-get update

# so we can do video thumbnails
apt-get install -y ffmpeg

# install some things that we'll need
apt-get install -y rpl imagemagick php7.2-imagick php7.2-mbstring php-zip php7.2-intl php7.2-xml

rpl public webroot /etc/apache2/sites-available/000-default.conf

# restart apache
/etc/init.d/apache2 restart

# restart php
/etc/init.d/php7.0-fpm restart

# install composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"

# install dependencies
cd /var/www
composer install

bin/cake migrations migrate
bin/cake migrations seed
