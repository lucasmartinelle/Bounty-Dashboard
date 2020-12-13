<?php
    // Load routing controller
    require_once("app/Routes.php");
    require_once("app/languages/languageManager.php");

    use app\Routes;
    use app\languages\languageManager;

    $routes = new Routes;
    $lang = new languageManager(LANGUAGE);

    $asset = "assets/";
    $idPage = "page404";
    ob_start();
?>

<!-- 404 Error Text -->
<div class="text-center">
    <div class="error mx-auto" data-text="404">404</div>
    <p class="lead text-gray-800 mb-5"><?= $lang->getTxt($idPage, 'content-header'); ?></p>
    <p class="text-gray-500 mb-0"><?= $lang->getTxt($idPage, 'content-subheader'); ?></p>
    <a href="<?= $routes->url(DEFAULT_PAGE); ?>">&larr; <?= $lang->getTxt($idPage, 'link-to-home'); ?></a>
</div>

<?php $content = ob_get_clean(); ?>