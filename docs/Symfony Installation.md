## Installation

---

## Install dependencies

```bash
apt-get update && apt-get upgrade -y
apt-get install apache2 php php-mysql php-xml php-mbstring mariadb-server php-pdo-mysql git
phpenmod pdo_mysql
a2enmod rewrite
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
mv composer.phar /usr/local/bin/composer
```

## Clone the repository

```bash
git clone https://github.com/EasyRecon/Bounty-Dashboard
mv Bounty-Dashboard /var/www/html
chown www-data:www-data /var/www/html/ -R && chmod 775 /var/www/html/ -R
```

## Create the database

```mysql
mysql -u root
CREATE DATABASE api;
GRANT ALL ON bugbounty.* TO 'bugbounty'@'localhost' IDENTIFIED BY '29ani6ibuKzyayWvCrLBQuTXp674R5hy';
FLUSH PRIVILEGES;
```

Before quitting MySQL, get the version of your MySQL server

```mysql
SELECT VERSION();
quit
```

and change the line below with you MySQL version in the `.env` file

```bash
DATABASE_URL="mysql://bugbounty:29ani6ibuKzyayWvCrLBQuTXp674R5hy@127.0.0.1:3306/bugbounty?serverVersion=<MYSQL VERSION>"
# If VERSION() output 10.3.27-MariaDB
DATABASE_URL="mysql://bugbounty:29ani6ibuKzyayWvCrLBQuTXp674R5hy@127.0.0.1:3306/bugbounty?serverVersion=10.3.27-MariaDB"
```

## Configure Apache and Project

Uncomment `extension=pdo_mysql` on `/etc/php/{version}/apache2/php.ini`
 Change `AllowOverride None` to `AllowOverride All` line 172 on `/etc/apache2/apache2.conf`

On `/etc/apache2/sites-enabled/000-default.conf` change `DocumentRoot /var/www/html/` by `DocumentRoot /var/www/html/RECON-API/Symfony/public` on line 12

Restart apache2

```bash
systemctl restart apache2
```

Install composer dependencies, load the database and the fixtures

```bash
cd /var/www/html/Bounty/
composer update
php bin/console doctrine:fixtures:load
```

Configure SMTP in `.env`, uncomment and replace the line below

```bash
# MAILER_DSN=smtp://localhost
```

For Gmail SMTP, it should be something like that

```bash
MAILER_DSN=gmail+smtp://<email address>:<password>@default
```

