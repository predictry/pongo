1-Run the bash code using the below command
	bash run.sh
2-Go to /usr/share/nginx/html/www directory 
3-Once in the directory remove the .env.sample.php file 
4-Create a .env.php file and set the configurations
5-Run the composer using the below command :
	composer install 

6-chmod -R 777 app/storage (YOU MAY WANT TO USE A BETTER PERMISSION IN PRODUCTION)
