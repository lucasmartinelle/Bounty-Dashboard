

## Bounty Dashboard
![banner](https://zupimages.net/up/21/09/e6iu.png)
The objective of this project is to facilitate the management of your reports as well as collaborative work by providing a web-based solution.

**Note :** The project is currently in testing phase, do not hesitate to open an issue if you encounter a bug or if you want to suggest an addition.

## Available features
* Dashboard with your customizable statistics according to different filters
* Add/remove platforms and import data
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

## Installation

**Set environment variables**

First of all, configure the SMTP parameters in the `/app/.env` file. Here is an example of configuration for gmail:

```bash
MAILER_DSN=gmail+smtp://<email address>:<password>@default
```

Then, launch docker-compose:

```bash
docker-compose up -d --build
```

Navigate in the symfony container and execute the following command:

```bash
sh ./prepare.sh
```

Enjoy !

## Screenshots

**Dashboard :**

![Dashboard](https://zupimages.net/up/21/24/8lno.png)

**Profile :**

![](https://zupimages.net/up/21/24/vdc7.png)

**Settings :**

![](https://zupimages.net/up/21/24/jqg5.png)

**Platforms :**
![Platforms](https://zupimages.net/up/21/24/stey.png)

**Programs :**
![Programs ](https://zupimages.net/up/21/24/ct6y.png)
**Notes :**
![Notes ](https://zupimages.net/up/21/24/574s.png)

**Templates :**
![Templates ](https://zupimages.net/up/21/24/k1c7.png)

**Reports :**
![Reports](https://zupimages.net/up/21/24/fvrn.png)

**Invoices :**
![Invoices ](https://zupimages.net/up/21/24/odw2.png)