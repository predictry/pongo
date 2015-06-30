## Predictry Front End and Web Services API "pongo"

Trivial: *Pongo* is the scientific name of orangutans.

## Installation Steps

1. Clone the repository
2. Put the public folder of the repo into the Virtual Host. In your httpd-vhosts.conf (for apache)
<VirtualHost *:80>
    ServerAdmin stewart@perfectsen.com
    DocumentRoot "/Users/stewart/development/predictry/pongo/public"
    ServerName pongo.local
</VirtualHost>
3. Create your database predictry_db in postgres
4. Checkout "production" branch to get the latest code. (important)
5. Create .env.php file based on .env.sample.php file in the same dir. (cp .env.sample.php .env.php)
6. Change the variables in .env.php such as database, aws keys, etc.
7. Composer install.

#make sure you have postgres pdo 
8. Run "php artisan migrate"
9. Run "php artisan db:seed"
10. Done.

## Documentation

Documentation can be found on this link http://api.predictry.com/integration/
