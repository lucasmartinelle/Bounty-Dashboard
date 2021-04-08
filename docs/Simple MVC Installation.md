## Installation for Simple MVC version

```bash
git clone https://github.com/lucasmartinelle/Bounty-Dashboard
```

Change your language (`default :EN`) and SMTP information in the file `./app/init.php`.

Build the container & start :

```bash
docker-compose build
docker-compose up -d
```

**No SMTP  ?** 

If you do not have an SMTP server, after installation, go to the container and connect to the MySQL database.

```mysql
mysql -u root -h dbbountydash -p bugbounty
UPDATE users SET active = 'Y' WHERE username = 'your_username'
```

