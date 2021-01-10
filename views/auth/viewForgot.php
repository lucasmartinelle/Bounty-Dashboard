<?php
    // Load routing controller
    require_once("app/Routes.php");
    require_once("app/languages/languageManager.php");

    use app\Routes;
    use app\languages\languageManager;
    
    $routes = new Routes;
    $lang = new languageManager;

    $asset = "../assets/";
    $idPage = "forgot";

    require_once("models/captchaHandler.php");
    use Models\CaptchaHandler;
    $this->_captchaHandler = new CaptchaHandler;
    $pubkey = $this->_captchaHandler->getPubKey();
    ob_start();
?>

<!-- == Global alert == -->
<?php if(isset($_SESSION['alert']) && isset($_SESSION['typeAlert']) && !empty($_SESSION['alert']) && !empty($_SESSION['typeAlert'])): 
    if(htmlspecialchars($_SESSION['typeAlert'], ENT_QUOTES) == 'error'): ?>
        <div class="alert alert-danger">
            <p style="margin-bottom: 0;"><i class="fas fa-exclamation-triangle m-r-xs"></i> <?= htmlspecialchars($_SESSION['alert'], ENT_QUOTES); ?></p>
        </div>
    <?php elseif(htmlspecialchars($_SESSION['typeAlert'], ENT_QUOTES) == 'success'): ?>
        <div class="alert alert-success">
            <p style="margin-bottom: 0;"><i class="fas fa-check m-r-xs"></i> <?= htmlspecialchars($_SESSION['alert'], ENT_QUOTES); ?></p>
        </div>
<?php endif; $_SESSION['alert'] = ''; $_SESSION['typeAlert'] = ''; endif; ?><!-- end global alert -->

<div class="card m-auto">
    <div class="text-header">
        <h1 class="text-center mt-3 text-dark"><?= $lang->getTxt($idPage, "content-header"); ?></h1>
    </div>
    <form method="post">
        <div class="form-row justify-content-center">
            <div class="col-md-10 mb-3 mt-2">
                <input type="text" name="email" class="form-control <?php if(isset($_SESSION['inputResponseEmail']) && !empty($_SESSION['inputResponseEmail'])){ echo htmlspecialchars($_SESSION['inputResponseEmail'], ENT_QUOTES); } ?>" value="<?php if(isset($_SESSION['inputValueEmail']) && !empty($_SESSION['inputValueEmail'])){ echo htmlspecialchars($_SESSION['inputValueEmail'], ENT_QUOTES); $_SESSION['inputValueEmail'] = ''; } ?>" id="email" placeholder="<?= $lang->getTxt($idPage, "email-placeholder"); ?>">
                <!-- == If validation failed == -->
                <?php if(isset($_SESSION['inputResponseEmail']) && !empty($_SESSION['inputResponseEmail']) && $_SESSION['inputResponseEmail'] == 'invalid'): ?>
                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseEmailMessage'], ENT_QUOTES); ?>"></i></span>
                <?php endif; $_SESSION['inputResponseEmail'] = ''; $_SESSION['inputResponseEmailMessage'] = ''; ?> <!-- End of validation failed -->
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

        <!-- == Captcha and crsf token == -->
        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
        <input type="hidden" id="token" name="token" value="<?= $token ?>">
        <!-- End Captcha and crsf token -->
    </form>
</div>

<?php
    $content = ob_get_clean();
    ob_start();
?>

<script>
    $(function () {
        $('[data-toggle="popover"]').popover()
    })

    <?php if($pubkey != null): ?>
        grecaptcha.ready(function() {
            grecaptcha.execute('<?= $pubkey; ?>', {action: 'homepage'}).then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
            });
        });
    <?php endif; ?>
</script>

<?php
    $script = ob_get_clean();
?>