<?php
    // Load routing controller
    require_once("app/Routes.php");
    require_once("app/languages/languageManager.php");

    use app\Routes;
    use app\languages\languageManager;
    
    $routes = new Routes;
    $lang = new languageManager(LANGUAGE);

    $asset = "../assets/";
    $idPage = "login";
    ob_start();
?>

<div class="card m-auto">
    <div class="text-header">
        <h1 class="text-center mt-3 text-dark"><?= $lang->getTxt($idPage, "content-header"); ?></h1>
    </div>
    <form class="needs-validation" novalidate>
        <div class="form-row justify-content-center">
            <div class="col-md-10 mb-3 mt-2">
                <input type="text" class="form-control" id="email" placeholder="<?= $lang->getTxt($idPage, "email-placeholder"); ?>" required>
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="col-md-10 mb-3 mt-2">
                <input type="text" class="form-control" id="password" placeholder="<?= $lang->getTxt($idPage, "password-placeholder"); ?>" required>
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="col-md-10 mb-3 mt-2">
                <div class="d-flex justify-content-between">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember"><?= $lang->getTxt($idPage, "remember-me"); ?></label>
                    </div>
                    <a class="text-right" href="<?= $routes->url('forgot'); ?>"><?= $lang->getTxt($idPage, "forgot-password"); ?></a>
                </div>
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="col-md-10 mb-3 mt-2">
                <button class="btn btn-dark w-100" type="submit"><?= $lang->getTxt($idPage, "submit"); ?></button>
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="col-md-10 mb-3 mt-1">
                <p class="text-dark text-center"><?= $lang->getTxt($idPage, "txt-to-signup"); ?> <a href="<?= $routes->url('registration'); ?>"><?= $lang->getTxt($idPage, "link-to-signup"); ?></a></button>
            </div>
        </div>
    </form>
</div>

<?php $content = ob_get_clean(); ?>