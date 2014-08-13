#! /bin/bash


#installing git if it does not exist
# type git  >/dev/null 2>&1 || {
#         sudo apt-get install -y git
#         echo "git installed successfully"
#    }


#installing composer if it does not exist
 type composer  >/dev/null 2>&1 || {
       sudo curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin && sudo mv /usr/local/bin/composer.phar /usr/local/bin/composer
         echo "composer installed successfully"
    }

#installing postgresql if it does not exist
 type psql  >/dev/null 2>&1 || {
	 sudo apt-get install -y postgresql-client
         echo "postgresql-client installed successfully"
    }


#installing nginx if it does not exist, and start nginx
 type nginx  >/dev/null 2>&1 || {
	sudo apt-get install -y nginx
	sudo service nginx start
         echo "nginx installed successfully and its running"
    }

#installing php-fpm and php-curl-extension curl if it does not exist
 type php  >/dev/null 2>&1 || {
        sudo  apt-get install -y php5-fpm && sudo apt-get install -y curl php5-curl
         echo "php installed successfully"
    }

#instaling mcrypt for php 
if [[ -z $(php -m | grep mcrypt) ]]; then
 sudo apt-get install -y  php5-mcrypt && -y  php5enmod mcrypt

fi


#creating a folder in /usr/share/nginx/html and cloning predictry from github

if [ ! -e "/usr/share/nginx/html/www" ] ; then
    cd /usr/share/nginx/html && mkdir "www" &&  sudo git clone https://github.com/perfectsen/predictry-pongo . 	
fi


