<?php
    // Load routing controller
    require_once("app/Routes.php");
    require_once("app/languages/languageManager.php");

    use app\Routes;
    use app\languages\languageManager;
    
    $routes = new Routes;
    $lang = new languageManager(LANGUAGE);

    $asset = "../../assets/";
    $idPage = "emailSent";
    ob_start();
?>

<div class="card m-auto">
    <div class="text-header">
        <h1 class="text-center mt-3 text-dark"><?= $lang->getTxt($idPage, "content-header"); ?></h1>
    </div>
    <div class="card-body text-center sh-dark mt-4">
        <img src="https://img.icons8.com/nolan/200/send-mass-email.png"/>
        <p><?php if($id == "forgot"){ echo $lang->getTxt($idPage, "forgot-sent"); }else{ echo $lang->getTxt($idPage, "registration-sent"); }?></p>
    </div>
</div>

<?php $content = ob_get_clean(); ?>