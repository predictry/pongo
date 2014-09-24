#! /bin/bash

type aws  >/dev/null 2>&1 || {
	sudo apt-get update
	sudo apt-get install -y python-pip
	sudo pip install awscli
    }


#installing php-fpm and php-curl-extension curl if it does not exist
 type php  >/dev/null 2>&1 || {
        sudo  apt-get install -y php5-fpm && sudo apt-get install -y php5-cli && sudo apt-get install -y curl php5-curl
         echo "php installed successfully"
    }

#installing composer if it does not exist
 type composer  >/dev/null 2>&1 || {
       sudo curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin && sudo mv /usr/local/bin/composer.phar /usr/local/bin/composer
         echo "composer installed successfully"
    }

#installing postgresql if it does not exist
 type psql  >/dev/null 2>&1 || {
         sudo apt-get install -y php5-pgsql
         echo "postgresql-client installed successfully"
    }


#installing nginx if it does not exist, and start nginx
 type nginx  >/dev/null 2>&1 || {
	sudo apt-get install -y nginx
	sudo service nginx start
         echo "nginx installed successfully and its running"
    }


#instaling mcrypt for php 
if [[ -z $(php -m | grep mcrypt) ]]; then
 sudo apt-get install -y  php5-mcrypt 
 sudo service php5-fpm restart
fi


#creating a folder in /usr/share/nginx/html and cloning predictry from github

if [ ! -e "/usr/share/nginx/html/www" ] ; then
    cd /usr/share/nginx/html && sudo mkdir -p "www/pongo" && cd www/pongo  && sudo mv  /home/ubuntu/pongo/* . && sudo mv  /home/ubuntu/pongo/.* .

fi


#removing the configuration files for nginx php php-fpm  and replacing with the ones from github
sudo cp /home/ubuntu/pongo/default /etc/nginx/sites-available/
sudo cp /home/ubuntu/pongo/nginx.conf /etc/nginx/
sudo cp /home/ubuntu/pongo/php.ini /etc/php5/fpm/
sudo cp /home/ubuntu/pongo/www.conf /etc/php5/fpm/pool.d

#removing the aws folder sudo rm -R /home/ubuntu/aws
sudo rm -R /home/ubuntu/pongo/

#running composer 
cd /usr/share/nginx/html/www/pongo/ && sudo composer install --prefer-source && sudo php artisan migrate && sudo composer dumpautoload
cd /usr/share/nginx/html/www/pongo/ && sudo composer install && sudo composer dumpautoload

#changing permission for app/storage
sudo chmod -R 777 /usr/share/nginx/html/www/pongo/app/storage 



# reloading nginx and php
sudo service php5-fpm reload
sudo service nginx reload
