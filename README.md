

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
  * Markdown support   
* Template management system
   * Templates are applicable to a report
* Invoice creation system
  * This feature is currently planned for Intigriti according to the French billing model.
* Settings
  * Add/remove user (Administrator/hunter)
  * Site language (FR/EN)
  * update reCaptcha keys

## Feature to come

* Complete rewrite with Symfony

## Installation

```bash
git clone https://github.com/lucasmartinelle/Bounty-Dashboard
```

Change your language (`default :EN`) and SMTP information in the file `./app/init.php`.

Build the container & start :
```
docker-compose build
docker-compose up -d
```

**No SMTP  ?** 

If you do not have an SMTP server, after installation, go to the container and connect to the MySQL database.

```
mysql -u root -h dbbountydash -p bugbounty
UPDATE users SET active = 'Y' WHERE username = 'your_username'
```

## Screenshots

**Dashboard :**

![Dashboard](https://zupimages.net/up/21/10/pvnt.png)

**Platforms :**
![Platforms](https://zupimages.net/up/21/09/zqhh.png)

**Programs :**
![Programs ](https://zupimages.net/up/21/09/k4ke.png)
**Notes :**
![Notes ](https://zupimages.net/up/21/10/b60z.png)

**Templates :**
![Templates ](https://zupimages.net/up/21/09/0buw.png)

**Reports :**
![Reports](https://zupimages.net/up/21/09/vavk.png)

**Invoices :**
![Invoices ](https://zupimages.net/up/21/09/76b4.png)

**Settings :**
![Settings ](https://zupimages.net/up/21/09/lgfw.png)
