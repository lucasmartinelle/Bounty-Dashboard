# Bounty Dashboard

> platform to gather data for bugbounty services created by a beginner

![banner](https://zupimages.net/up/20/50/sfp6.png)

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

- ​	Display programs with **number of bugs found**, **gain**, **scope**, **status** and **tags**.

- ​	Adding and deleting a program  **(auth required)**

- - ​		**Scope** (1 or more URLs)
  - ​		**start date**
  - ​		**status** (open / close)
  - ​		**Tags** (the user can add tags of his choice on the program)

- ​	Pie chart with the number of bugs found by severity

-     Platform

---

### Reports  **(auth required)**

- ​	List reports (filter by **platform** and/or **program** and/or **severity** and/or **status**)

- ​    Button to change the status of a report (Accepted / Resolved / NA / OOS / Informative)

- ​	Button to export a report in PDF/Markdown format

- ​	Creating a report (open report by default)

- - ​		Ability to apply a template
  - ​		Title (Text)
  - ​		Date
  - ​		Severity (CVSS scale)
  - ​		Endpoint (URL)
  - ​		Steps to Reproduce (Markdown) / Impact (Markdown) / Mitigation (Markdown) / Ressources (Markdown)
  -         Programs

---

### templates  **(auth required)**

- ​	List existing templates

- ​	Adding a template

- - ​		Title (Text) / Severity (CVSS scale) / Endpoint (URL) / Description (Markdown) / Steps to Reproduce (Markdown) / Impact (Markdown) /  Mitigation (Markdown) / Ressources (Markdown)

- ​	Editing/deleting a template

---

### Profile  **(auth required)**

- ​	Change username
- ​	Change email / password

---

### Settings 

- ​	Choice of language (FR/EN)

- - ​		The texts must therefore be taken from a configuration file according to the chosen language.

- ​	User management **(admin required)**

- - ​		List users

  - ​		Add / remove a user

  - - ​			Username
    - ​            Password
    - ​            Role (Hunter / Admin)

---

## Modules

use of [canvasJS](https://canvasjs.com/jquery-charts/)

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

- - ​	For example via a "Bad count" and "Last bad count" field in the DB, as soon as Bad count reaches 5 then a delay of 1s is added to the connection and the field is reset to 0 after one hour.
  - ​	Otherwise add a captcha on the connection but the admin must be able to add his reCaptcha key in the administration, if the value does not exist then the captcha is not active.

- password hashed in base with Argon2ID

- User IDs with GUIDv4

---

## Databases

### Reports

| Field            | Type                 | Null | Key     | Default             | Extra          |
| ---------------- | -------------------- | ---- | ------- | ------------------- | -------------- |
| id               | smallint(7) unsigned | NO   | PRIMARY | NULL                | auto_increment |
| created_at       | datetime             | YES  |         | current_timestamp() |                |
| title            | varchar(200)         | NO   | UNIQUE  | NULL                |                |
| severity         | float(8,5)           | NO   |         | NULL                |                |
| endpoint         | text                 | NO   |         | NULL                |                |
| template_id      | smallint(7) unsigned | NO   |         | NULL                |                |
| program_id       | smallint(7) unsigned | NO   |         | NULL                |                |
| stepsToReproduce | longtext             | NO   |         | NULL                |                |
| impact           | longtext             | NO   |         | NULL                |                |
| mitigation       | longtext             | NO   |         | NULL                |                |
| resources        | longtext             | NO   |         | NULL                |                |

### programs

| Field       | Type                 | Null | Key     | Default             | Extra          |
| ----------- | -------------------- | ---- | ------- | ------------------- | -------------- |
| id          | smallint(7) unsigned | NO   | PRIMARY | NULL                | auto_increment |
| scope       | text                 | NO   |         | NULL                |                |
| date        | datetime             | YES  |         | current_timestamp() |                |
| status      | varchar(5)           | YES  |         | 'open'              |                |
| platform_id | smallint(7) unsigned | NO   |         | NULL                |                |

### platforms

| Field | Type                 | Null | Key     | Default | Extra          |
| ----- | -------------------- | ---- | ------- | ------- | -------------- |
| id    | smallint(7) unsigned | NO   | PRIMARY | NULL    | auto_increment |
| name  | varchar(200)         | NO   | UNIQUE  | NULL    |                |

### templates

| Field            | Type                 | Null | Key     | Default             | Extra          |
| ---------------- | -------------------- | ---- | ------- | ------------------- | -------------- |
| id               | smallint(7) unsigned | NO   | PRIMARY | NULL                | auto_increment |
| created_at       | datetime             | YES  |         | current_timestamp() |                |
| title            | varchar(200)         | NO   | UNIQUE  | NULL                |                |
| severity         | float(8,5)           | NO   |         | NULL                |                |
| endpoint         | text                 | NO   |         | NULL                |                |
| stepsToReproduce | longtext             | NO   |         | NULL                |                |
| impact           | longtext             | NO   |         | NULL                |                |
| mitigation       | longtext             | NO   |         | NULL                |                |
| resources        | longtext             | NO   |         | NULL                |                |

### users

| Field      | Type                 | Null | Key     | Default             | Extra          |
| ---------- | -------------------- | ---- | ------- | ------------------- | -------------- |
| id         | smallint(7) unsigned | NO   | PRIMARY | NULL                | auto_increment |
| username   | varchar(200)         | NO   | UNIQUE  | NULL                |                |
| email      | varchar(255)         | NO   | UNIQUE  | NULL                |                |
| password   | text                 | NO   |         | NULL                |                |
| token      | text                 | NO   |         | NULL                |                |
| role       | varchar(6)           | YES  |         | 'hunter'            |                |
| active     | char(1)              | YES  |         | 'N'                 |                |
| created_at | datetime             | YES  |         | current_timestamp() |                |
| updated_at | datetime             | YES  |         | current_timestamp() |                |

