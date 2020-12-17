> platform to gather data for bugbounty services created by a beginner

![banner](https://zupimages.net/up/20/50/sfp6.png)

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

## Structure

### dashboard

inserts

* Bug opened
* bug accepted and fixed
* Total earnings
* critical bug found

pie charts (**canvasJS**)

* bugs founds by platform (default)
* bugs founds by severity
  * filter per platform
  * filter per program
* bugs founds per month
  * filter per platform

---

### platforms  **(auth required)**

-  Pie chart with the number of bugs found by severity
-  Diagram of earnings per month
-  Add / remove a platform

---

### Programs  **(auth required)**

- Display programs with **number of bugs found**, **gain**, **scope**, **status** and **tags**.

- Adding and deleting a program  **(auth required)**

  * **Scope** (1 or more URLs)

  - **start date**
  - **status** (open / close)
  - **Tags** (the user can add tags of his choice on the program)

- Pie chart with the number of bugs found by severity

---

### Reports  **(auth required)**

- List reports (filter by **platform** and/or **program** and/or **severity** and/or **status**)

- Button to change the status of a report (Accepted / Resolved / NA / OOS / Informative)

- Button to export a report in PDF/Markdown format

- Creating a report (open report by default)

  * Identifiant (optional)

  * Ability to apply a template
  
  - Title (Text)
  - Date
  - Severity (CVSS scale)
  - Endpoint (URL)
  - Steps to Reproduce (Markdown) / Impact (Markdown) / Mitigation (Markdown) / Ressources (Markdown)
  - Programs

---

### templates  **(auth required)**

- List existing templates
- Adding a template
* Title (Text) / Severity (CVSS scale) / Endpoint (URL) / Description (Markdown) / Steps to Reproduce (Markdown) / Impact (Markdown) /  Mitigation (Markdown) / Ressources (Markdown)
- Editing/deleting a template

---

### Profile  **(auth required)**

- Change username
- Change email / password
- The user must be able to check if he wants to activate or not the billing system.
  * If it is active, it can fill in the following fields
    * name / First name / Address / Phone number / Email / SIRET / VAT / BANK / IBAN / BIC

---

### Settings 

- Choice of language (FR/EN)

  * The texts must therefore be taken from a configuration file according to the chosen language.

- User management **(admin required)**

  * List users

  - Add / remove a user

    * Username

    - Password
    - Role (Hunter / Admin)

---

### Invoice

* Manually generated
  * The user selects the platform and the desired month.
    * it shows him the list of all reports (only those with a bounty) for the platform and the selected month, the user has the choice to uncheck some reports that will then not be in the PDF.
    * it generates a PDF according to the attached template
      * Some information in the PDF is automatically filled in depending on the platform (See attachment)
        * Project / client / BTW / Address / email / date (corresponds to the generation date)
        * Subtotal excl: Must be automatically calculated (VAT & TOTAL is the same amount)
* Automatically generated
  * Button grayed out for the moment

---

## Assets

* [canvasJS](https://canvasjs.com/jquery-charts/) for charts
* [PHP-MVC](https://github.com/lucasmartinelle/MVC-PHP) for MVC system
* [bootstrap](https://getbootstrap.com/) and [template](https://startbootstrap.com/theme/sb-admin-2) from [startbootstrap](https://startbootstrap.com/)
* [jquery](https://jquery.com/) and [datatables](https://datatables.net/)
* [icons8](https://icones8.fr/) and [fontawesome](https://fontawesome.com/)

### pie charts

The code below create a pie chart with the possibility to download the chart.

```html
<!-- DIV FOR PIE CHART -->
<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<!-- JQUERY + canvasJS -->
<!-- ... -->
<!-- CREATE PIE CHART -->
<script type="text/javascript">
window.onload = function() {
	var options = {
        exportEnabled: true,
        animationEnabled: true,
        title:{
            text: "Accounting"
        },
        legend:{
            horizontalAlign: "right",
            verticalAlign: "center"
        },
        data: [{
            type: "pie",
            showInLegend: true,
            toolTipContent: "<b>{name}</b>: ${y} (#percent%)",
            indexLabel: "{name}",
            legendText: "{name} (#percent%)",
            indexLabelPlacement: "inside",
            dataPoints: [
                { y: 6566.4, name: "Housing" },
                { y: 2599.2, name: "Food" },
                { y: 1231.2, name: "Fun" },
                { y: 1368, name: "Clothes" },
                { y: 684, name: "Others"},
                { y: 1231.2, name: "Utilities" }
            ]
        }]
	};
	$("#chartContainer").CanvasJSChart(options);
}
</script>
```

### result

![pie chart](https://zupimages.net/up/20/50/qelw.png)

---

## Global Informations:

- Use of an anti CSRF token

- If the user tries to log in more than 5 times with a wrong password, added a delay to the connection of 1s

  * For example via a "Bad count" and "Last bad count" field in the DB, as soon as Bad count reaches 5 then a delay of 1s is added to the connection and the field is reset to 0 after one hour.

  - Otherwise add a captcha on the connection but the admin must be able to add his reCaptcha key in the administration, if the value does not exist then the captcha is not active.

- password hashed in base with Argon2ID

- User IDs with GUIDv4

---

## Databases

### Reports

| Field            | Type         | Null | Key    | Default             | Extra |
| ---------------- | ------------ | ---- | ------ | ------------------- | ----- |
| id               | varchar(36)  | NO   | UNIQUE |                     |       |
| title            | varchar(200) | NO   | UNIQUE |                     |       |
| severity         | float(8,5)   | NO   |        |                     |       |
| endpoint         | text         | NO   |        |                     |       |
| identifiant      | varchar(200) | NO   | UNIQUE |                     |       |
| template_id      | varchar(36)  | NO   |        |                     |       |
| program_id       | varchar(36)  | NO   |        |                     |       |
| stepsToReproduce | longtext     | NO   |        |                     |       |
| impact           | longtext     | NO   |        |                     |       |
| mitigation       | longtext     | NO   |        |                     |       |
| resources        | longtext     | NO   |        |                     |       |
| created_at       | datetime     | YES  |        | current_timestamp() |       |

### programs

| Field       | Type        | Null | Key    | Default             | Extra |
| ----------- | ----------- | ---- | ------ | ------------------- | ----- |
| id          | varchar(36) | NO   | UNIQUE |                     |       |
| scope       | text        | NO   |        |                     |       |
| status      | varchar(5)  | YES  |        | 'open'              |       |
| platform_id | varchar(36) | NO   |        |                     |       |
| created_at  | datetime    | YES  |        | current_timestamp() |       |

### platforms

| Field | Type         | Null | Key    | Default | Extra |
| ----- | ------------ | ---- | ------ | ------- | ----- |
| id    | varchar(36)  | NO   | UNIQUE |         |       |
| name  | varchar(200) | NO   | UNIQUE |         |       |

### templates

| Field            | Type         | Null | Key    | Default             | Extra |
| ---------------- | ------------ | ---- | ------ | ------------------- | ----- |
| id               | varchar(36)  | NO   | UNIQUE |                     |       |
| creator_id       | varchar(36)  | NO   |        |                     |       |
| title            | varchar(200) | NO   |        |                     |       |
| severity         | float(8,5)   | NO   |        |                     |       |
| endpoint         | text         | NO   |        |                     |       |
| stepsToReproduce | longtext     | NO   |        |                     |       |
| impact           | longtext     | NO   |        |                     |       |
| mitigation       | longtext     | NO   |        |                     |       |
| resources        | longtext     | NO   |        |                     |       |
| created_at       | datetime     | YES  |        | current_timestamp() |       |

### users

| Field       | Type                 | Null | Key    | Default             | Extra |
| ----------- | -------------------- | ---- | ------ | ------------------- | ----- |
| id          | varchar(36)          | NO   | UNIQUE |                     |       |
| username    | varchar(200)         | NO   | UNIQUE |                     |       |
| email       | varchar(255)         | NO   | UNIQUE |                     |       |
| password    | text                 | NO   |        |                     |       |
| token       | text                 | NO   |        |                     |       |
| role        | varchar(6)           | YES  |        | 'hunter'            |       |
| active      | char(1)              | YES  |        | 'N'                 |       |
| created_at  | datetime             | YES  |        | current_timestamp() |       |
| updated_at  | datetime             | YES  |        | current_timestamp() |       |
| bad_attempt | smallint(1) unsigned | YES  |        | 0                   |       |
| last_failed | datetime             | YES  |        | NULL                |       |

