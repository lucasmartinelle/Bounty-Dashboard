<?php
    // Load routing controller
    require_once("app/Routes.php");
    use app\Routes;
    $routes = new Routes;

    $asset = "assets/";
    $idPage = "dashboard";
    ob_start();
?>

<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800">Blank Page</h1>

<?php $content = ob_get_clean(); ?>