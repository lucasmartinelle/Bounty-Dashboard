<?php
    // Load routing controller
    require_once("app/Routes.php");
    require_once("app/languages/languageManager.php");
    require_once("utils/Session.php");

    use app\Routes;
    use app\languages\languageManager;
    use Utils\Session;
    
    $routes = new Routes;
    $session = new Session;
    $lang = new languageManager;

    $asset = "../assets/";
    $idPage = "platforms";
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

    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1 class="h3 mb-1 text-gray-800 mb-3"><?= $lang->getTxt($idPage, "header"); ?></h1>
            <div>
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#createPlatform" style="height: max-content;">
                    <?= $lang->getTxt($idPage, "add-platform"); ?>
                </button>
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deletePlatform" style="height: max-content;">
                    <?= $lang->getTxt($idPage, "delete-platform"); ?>
                </button>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-bug-by-severity"); ?></h6>
                    </div>
                    <div class="card-body">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-bug-by-severity"); ?></h6>
                    </div>
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
    </div> 

    <!-- Modal -->
    <div class="modal fade" id="createPlatform" tabindex="-1" role="dialog" aria-labelledby="#createPlatformLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createPlatformLabel"><?= $lang->getTxt($idPage, "add-platform"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-row justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <input type="text" name="name" class="form-control <?php if(isset($_SESSION['inputResponseName']) && !empty($_SESSION['inputResponseName'])){ echo htmlspecialchars($_SESSION['inputResponseName'], ENT_QUOTES); } ?>" value="<?php if(isset($_SESSION['inputValueName']) && !empty($_SESSION['inputValueName'])){ echo htmlspecialchars($_SESSION['inputValueName'], ENT_QUOTES); $_SESSION['inputValueName'] = ''; } ?>" id="name" placeholder="<?= $lang->getTxt($idPage, "name-placeholder"); ?>">
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseName']) && !empty($_SESSION['inputResponseName']) && $_SESSION['inputResponseName'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseNameMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseName'] = ''; $_SESSION['inputResponseNameMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>

                        <!-- == Captcha and crsf token == -->
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
                        <input type="hidden" id="token" name="token" value="<?= $token ?>">
                        <!-- End Captcha and crsf token -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->getTxt($idPage, "modal-nav-close"); ?></button>
                        <button type="submit" class="btn btn-primary"><?= $lang->getTxt($idPage, "modal-nav-confirm"); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deletePlatform" tabindex="-1" role="dialog" aria-labelledby="deletePlatformLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePlatformLabel"><?= $lang->getTxt($idPage, "delete-platform"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="<?= $routes->url('deletePlatform'); ?>">
                    <div class="modal-body">
                        <div class="form-row selectformrow justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <select class="form-control <?php if(isset($_SESSION['inputResponsePlatform']) && !empty($_SESSION['inputResponsePlatform'])){ echo htmlspecialchars($_SESSION['inputResponsePlatform'], ENT_QUOTES); } ?>" id="platform" name="platform">
                                    <option hidden selected value=""><?= $lang->getTxt($idPage, "header"); ?></option>
                                    <?php foreach($platforms as $platform): ?>
                                        <option value="<?= $platform->id(); ?>"><?= $platform->name(); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponsePlatform']) && !empty($_SESSION['inputResponsePlatform']) && $_SESSION['inputResponsePlatform'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponsePlatformMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponsePlatform'] = ''; $_SESSION['inputResponsePlatformMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>
                        <!-- == Captcha and crsf token == -->
                        <input type="hidden" id="g-recaptcha-response-2" name="g-recaptcha-response">
                        <input type="hidden" id="token" name="token" value="<?= $token ?>">
                        <!-- End Captcha and crsf token -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->getTxt($idPage, "modal-nav-close"); ?></button>
                        <button type="submit" class="btn btn-primary"><?= $lang->getTxt($idPage, "modal-nav-confirm"); ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php 
    $content = ob_get_clean();
    ob_start();
?>

<script type="text/javascript">
    $(function () {
        $('[data-toggle="popover"]').popover()
    })

    grecaptcha.ready(function() {
        grecaptcha.execute('<?php echo SITE_KEY; ?>', {action: 'homepage'}).then(function(token) {
            document.getElementById('g-recaptcha-response').value = token;
            document.getElementById('g-recaptcha-response-2').value = token;
        });
    });
</script>

<?php
    $script = ob_get_clean();
?>