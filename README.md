## Bounty Dashboard
![banner](https://zupimages.net/up/21/09/e6iu.png)
The objective of this project is to facilitate the management of your reports as well as collaborative work by providing a web-based solution.

**Note :** The project is currently in testing phase, do not hesitate to open an issue if you encounter a bug or if you want to suggest an addition.

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

## Feature to come

* Docker based installation
* Auto-Install script

## Installation

**Installation of prerequisites :**

```bash
apt-get update && apt-get upgrade -y
apt-get install apache2 php php-mysql mariadb-server
cd /var/www/html/
git clone https://github.com/lucasmartinelle/Bounty-Dashboard
chown -R www-data:www-data /var/www/html/
```

**Create the database :**

```bash
mysql -u root
CREATE DATABASE bugbounty;
GRANT ALL ON bugbounty.* TO 'bugbounty'@'localhost' IDENTIFIED BY '29ani6ibuKzyayWvCrLBQuTXp674R5hy';
FLUSH PRIVILEGES;
quit
```

It is recommended to change the password, this change should also be reflected in the file  `/var/www/html/Bounty-Dashboard/app/init.php`

**Import the SQL File :**

```bash
mysql -u root bugbounty < base.sql
```
**Apache2 configuration :**

 * Uncomment `extension=pdo_mysql` on `/etc/php/{version}/apache2/php.ini`
 * Change `AllowOverride None` to `AllowOverride All` line 172 on `/etc/apache2/apache2.conf`
 * On `/etc/apache2/sites-enabled/000-default.conf` change `DocumentRoot /var/www/html/` by `DocumentRoot /var/www/html/Bounty-Dashboard` on line 12
 * Enabling Apache's `mod_rewrite` module : `a2enmod rewrite`

**Restart apache2 :** 

```bash
systemctl restart apache2
```

## Screenshots

**Dashboard :**
![Dashboard](https://zupimages.net/up/21/09/zqhh.png)

**Platforms :**
![Platforms]()

**Programs :**
![Programs ](https://zupimages.net/up/21/09/k4ke.png)

**Templates :**
![Templates ](https://zupimages.net/up/21/09/0buw.png)

**Reports :**
![Reports](https://zupimages.net/up/21/09/vavk.png)

**Invoices :**
![Invoices ]()

**Settings :**
![Settings ](https://zupimages.net/up/21/09/lgfw.png)

