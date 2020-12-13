<?php
    // Load routing controller
    require_once("app/Routes.php");
    require_once("app/languages/languageManager.php");

    use app\Routes;
    use app\languages\languageManager;
    
    $routes = new Routes;
    $lang = new languageManager(LANGUAGE);

    $asset = "../../../assets/";
    $idPage = "validationForgot";
    ob_start();
?>

<div class="card m-auto">
    <div class="text-header">
        <h1 class="text-center mt-3 text-dark"><?= $lang->getTxt($idPage, "content-header"); ?></h1>
    </div>
    <form class="needs-validation" novalidate>
        <div class="form-row justify-content-center">
            <div class="col-md-10 mb-3 mt-2">
                <input type="text" class="form-control" id="password" placeholder="<?= $lang->getTxt($idPage, "password-placeholder"); ?>" required>
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="col-md-10 mb-3 mt-2">
                <input type="text" class="form-control" id="cpassword" placeholder="<?= $lang->getTxt($idPage, "cpassword-placeholder"); ?>" required>
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="col-md-10 mb-3 mt-2">
                <button class="btn btn-dark w-100" type="submit"><?= $lang->getTxt($idPage, "submit"); ?></button>
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="col-md-10 mb-3 mt-1">
                <p class="text-dark text-center"><?= $lang->getTxt($idPage, "txt-to-signin"); ?> <a href="<?= $routes->url('login'); ?>"><?= $lang->getTxt($idPage, "link-to-signin"); ?></a></button>
            </div>
        </div>
    </form>
</div>

<?php $content = ob_get_clean(); ?>