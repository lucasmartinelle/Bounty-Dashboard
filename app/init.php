<?php
    define('ACTIVE_DEBUG', false); // false to disable debug, true to enable it
    $protocole = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    define('URL', $protocole . $_SERVER['HTTP_HOST']);
    define('DEFAULT_PAGE', 'dashboard'); // label of default page
    define('TITLE', 'BugBounty'); // Global title
    define('WEBSITE_PATH', '/var/www/html/Bounty-Dashboard/'); // website path. Need a "/" at the end of the path.
    define('LANGUAGE', 'EN');

    // make sure to enable PDO drivers and download php-mysql extensions
    define('DB_HOST', '127.0.0.1'); // host MYSQL
    define('DB_USERNAME', 'bugbounty'); // username MYSQL
    define('DB_PASSWORD', '29ani6ibuKzyayWvCrLBQuTXp674R5hy'); // password MYSQL
    define('DB_DATABASE', 'bugbounty'); // Databse MYSQL

    // Email variables (default configuration for gmail account).
    // https://www.siteground.com/kb/google_free_smtp_server/
    // To send emails from your gmail address, you need to disable dual authentication and enable the "Less secure application access" option.
    // https://myaccount.google.com/ -> "security" -> "Login to Google" -> "Two-step validation" -> disable it
    // https://myaccount.google.com/ -> "security" -> "Less secure application access" -> disable it
    define('EMAIL_HOST', 'smtp.gmail.com'); // SMTP host server
    define('EMAIL_PORT', 587); // SMTP port server
    define('EMAIL_SMTPSECURE', 'tls'); // SMTP security active (false to disable it)
    define('EMAIL_SMTP_ACTIVE_AUTH', true); // Should SMTP authenticate ?
    define('EMAIL_DEBUG', false); // false or 1/2/3/4 (The bigger the number, the more detailed the debug will be, false disable it)
    define('EMAIL', 'something@gmail.com'); // EMAIL SENDER
    define('EMAIL_PASSWORD', 'password'); // PASSWORD EMAIL SENDER
    define('EMAIL_FROM', 'sender'); // NAME OF SENDER
?>