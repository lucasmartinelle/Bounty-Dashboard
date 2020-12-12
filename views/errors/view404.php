<?php
    // Load routing controller
    require_once("app/Routes.php");
    use app\Routes;
    $routes = new Routes;

    $asset = "assets/";
    $idPage = "page404";
    ob_start();
?>

<!-- 404 Error Text -->
<div class="text-center">
    <div class="error mx-auto" data-text="404">404</div>
    <p class="lead text-gray-800 mb-5">Page Not Found</p>
    <p class="text-gray-500 mb-0">It looks like you found a glitch in the matrix...</p>
    <a href="<?= $routes->url(DEFAULT_PAGE); ?>">&larr; Back to Dashboard</a>
</div>

<?php $content = ob_get_clean(); ?>