#! /bin/bash

CWD=$(pwd)

#installing git if it does not exist
 type git  >/dev/null 2>&1 || {
         sudo apt-get install -y git
         echo "git installed successfully"
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
	 sudo apt-get install -y postgresql-client
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
 sudo ln -s /etc/php5/conf.d/mcrypt.ini /etc/php5/mods-available/mcrypt.ini
 sudo php5enmod mcrypt
 sudo service php5-fpm restart
fi


#creating a folder in /usr/share/nginx/html and cloning predictry from github

if [ ! -e "/usr/share/nginx/html/www" ] ; then
    cd /usr/share/nginx/html && sudo mkdir "www" && cd www  && sudo git clone https://a0731e397121fcfcd8446931d0dc092da97e0669@github.com/perfectsen/predictry-pongo .

fi

#replacing the nginx default file

cp $CWD/default /etc/nginx/sites-available/

#replacing php.ini with the default one 
 
cp $CWD/php.ini /etc/php5/fpm/php.ini


# reloading nginx and php
service php5-fpm reload
service nginx reload
