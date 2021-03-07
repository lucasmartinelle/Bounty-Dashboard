## Bounty Dashboard
![banner](https://zupimages.net/up/21/09/e6iu.png)
The objective of this project is to facilitate the management of your reports as well as collaborative work by providing a web-based solution.

## Available features
* Dashboard with your customizable statistics according to different filters
* Add/remove platforms
* Add/Remove Programs
  * System for adding notes to a program
* Report management system
* Template management system
   * Templates are applicable to a report
* Invoice creation system
  * This feature is currently planned for Intigriti according to the French billing model.
* Settings
  * Add/remove user (Administrator/hunter)
  * Site language (FR/EN)
  * Enable/disable reCaptcha

## Installation

```bash
apt-get update && apt-get upgrade -y
apt-get install apache2 php php-mysql mariadb-server
a2enmod rewrite
git clone https://github.com/lucasmartinelle/Bounty-Dashboard
mv Bounty-Dashboard/ /var/www/html/
chown www-data:www-data /var/www/html/ -R && chmod 775 /var/www/html/ -R
```

### Create the database :

```bash
mysql -u root
CREATE DATABASE bugbounty;
GRANT ALL ON bugbounty.* TO 'bugbounty'@'localhost' IDENTIFIED BY '29ani6ibuKzyayWvCrLBQuTXp674R5hy';
FLUSH PRIVILEGES;
quit
```

If you want, change the password for `bugbounty` user and also in `/var/www/html/Bounty-Dashboard/app/init.php`

###  Import the SQL File :

```bash
cd /var/www/html/Bounty-Dashboard/
mysql -u root bugbounty < base.sql
```

Uncomment `extension=pdo_mysql` on `/etc/php/{version}/apache2/php.ini`
 Change `AllowOverride None` to `AllowOverride All` line 172 on `/etc/apache2/apache2.conf`

On `/etc/apache2/sites-enabled/000-default.conf` change `DocumentRoot /var/www/html/` by `DocumentRoot /var/www/html/Bounty-Dashboard` on line 12

### Restart apache2 :

```bash
systemctl restart apache2
```