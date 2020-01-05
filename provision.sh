# update the system
apt-get update

# so we can do video thumbnails
apt-get install -y ffmpeg

# install some things that we'll need
apt-get install -y imagemagick php7.2-imagick php7.2-mbstring php7.2-zip php7.2-intl php7.2-xml

echo '<VirtualHost *:80>
        ServerAdmin webmaster@localhost
        DocumentRoot /var/www/webroot

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

        <Directory /var/www/webroot>
                AllowOverride All
        </Directory>

</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

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

# enable modrewrite
a2enmod rewreite
service apache2 restart
