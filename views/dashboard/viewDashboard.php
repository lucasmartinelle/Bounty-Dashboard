<?php
    // Load routing controller
    require_once("app/Routes.php");
    require_once("app/languages/languageManager.php");

    use app\Routes;
    use app\languages\languageManager;
    
    $routes = new Routes;
    $lang = new languageManager(LANGUAGE);

    $asset = "assets/";
    $idPage = "dashboard";
    ob_start();
?>

<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800"><?= $lang->getTxt($idPage, 'content-header'); ?></h1>

<?php $content = ob_get_clean(); ?>