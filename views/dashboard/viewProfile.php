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
    $idPage = "profile";
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

<h1 class="h3 mb-1 text-gray-800 mb-3"><?= $lang->getTxt($idPage, "header"); ?></h1>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-change-username"); ?></h6>
                </div>
                <div class="wrapper-form">
                    <form method="post" action="<?= $routes->url('changeUsername'); ?>">
                        <div class="form-row justify-content-center">
                            <div class="col-md-10 mb-3 mt-4">
                                <input type="text" name="username" class="form-control <?php if(isset($_SESSION['inputResponseUsername']) && !empty($_SESSION['inputResponseUsername'])){ echo htmlspecialchars($_SESSION['inputResponseUsername'], ENT_QUOTES); } ?>" value="<?php if(isset($_SESSION['inputValueUsername']) && !empty($_SESSION['inputValueUsername'])){ echo htmlspecialchars($_SESSION['inputValueUsername'], ENT_QUOTES); $_SESSION['inputValueUsername'] = ''; } ?>" id="username" placeholder="<?= $lang->getTxt($idPage, "username-placeholder"); ?>">
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseUsername']) && !empty($_SESSION['inputResponseUsername']) && $_SESSION['inputResponseUsername'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseUsernameMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseUsername'] = ''; $_SESSION['inputResponseUsernameMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>
                        <div class="form-row justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <button class="btn btn-dark w-100" type="button" data-toggle="modal" data-target="#saveUsername"><?= $lang->getTxt($idPage, "submit"); ?></button>
                            </div>
                        </div>
                        <!-- == Captcha and crsf token == -->
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
                        <input type="hidden" id="token" name="token" value="<?= $token ?>">
                        <!-- End Captcha and crsf token -->

                        <!-- Modal change username -->
                        <div class="modal fade" id="saveUsername" tabindex="-1" role="dialog" aria-labelledby="saveUsernameLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="saveUsernameLabel">Change your username</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <p>Are you sure you want to change your username by <span class="text-danger" id="usernameValueModal" style="position: static;"></span> ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-change-email"); ?></h6>
                </div>
                <div class="wrapper-form">
                    <form method="post" action="<?= $routes->url('changeEmail'); ?>">
                        <div class="form-row justify-content-center">
                            <div class="col-md-10 mb-3 mt-4">
                                <input type="text" name="email" class="form-control <?php if(isset($_SESSION['inputResponseEmail']) && !empty($_SESSION['inputResponseEmail'])){ echo htmlspecialchars($_SESSION['inputResponseEmail'], ENT_QUOTES); } ?>" value="<?php if(isset($_SESSION['inputValueEmail']) && !empty($_SESSION['inputValueEmail'])){ echo htmlspecialchars($_SESSION['inputValueEmail'], ENT_QUOTES); $_SESSION['inputValueEmail'] = ''; } ?>" id="email" placeholder="<?= $lang->getTxt($idPage, "email-placeholder"); ?>">
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseEmail']) && !empty($_SESSION['inputResponseEmail']) && $_SESSION['inputResponseEmail'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseEmailMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseEmail'] = ''; $_SESSION['inputResponseEmailMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>
                        <div class="form-row justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <button class="btn btn-dark w-100" type="button" data-toggle="modal" data-target="#saveEmail"><?= $lang->getTxt($idPage, "submit"); ?></button>
                            </div>
                        </div>
                        <!-- == Captcha and crsf token == -->
                        <input type="hidden" id="g-recaptcha-response-1" name="g-recaptcha-response">
                        <input type="hidden" id="token" name="token" value="<?= $token ?>">
                        <!-- End Captcha and crsf token -->

                        <!-- Modal change email -->
                        <div class="modal fade" id="saveEmail" tabindex="-1" role="dialog" aria-labelledby="saveEmailLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="saveEmailLabel">Change your email</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <p>Are you sure you want to change your email by <span class="text-danger" id="emailValueModal" style="position: static;"></span> ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-change-password"); ?></h6>
                </div>
                <div class="wrapper-form">
                    <form method="post" action="<?= $routes->url('changePassword'); ?>">
                        <div class="form-row justify-content-center">
                            <div class="col-md-10 mb-3 mt-4">
                                <input type="password" name="password" class="form-control <?php if(!empty(htmlspecialchars($_SESSION['inputResponsePassword'], ENT_QUOTES)) && $_SESSION['inputResponsePassword'] == 'invalid') { echo htmlspecialchars($_SESSION['inputResponsePassword'], ENT_QUOTES); } elseif(isset($_SESSION['inputResponsePassword']) && !empty($_SESSION['inputResponsePassword']) && $_SESSION['inputResponsePassword'] == 'valid' && isset($_SESSION['inputResponseCPassword']) && !empty($_SESSION['inputResponseCPassword']) && $_SESSION['inputResponseCPassword'] == 'invalid') { echo 'invalid'; } else { echo htmlspecialchars($_SESSION['inputResponsePassword'], ENT_QUOTES); }?>" id="password" placeholder="<?= $lang->getTxt($idPage, "password-placeholder"); ?>">
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponsePassword']) && !empty($_SESSION['inputResponsePassword']) && $_SESSION['inputResponsePassword'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponsePasswordMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php elseif(isset($_SESSION['inputResponsePassword']) && !empty($_SESSION['inputResponsePassword']) && $_SESSION['inputResponsePassword'] == 'valid' && isset($_SESSION['inputResponseCPassword']) && !empty($_SESSION['inputResponseCPassword']) && $_SESSION['inputResponseCPassword'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseCPasswordMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponsePassword'] = ''; $_SESSION['inputResponsePasswordMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>
                        <div class="form-row justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <input type="password" name="cpassword" class="form-control <?php if(!empty(htmlspecialchars($_SESSION['inputResponseCPassword'], ENT_QUOTES))) { echo htmlspecialchars($_SESSION['inputResponseCPassword'], ENT_QUOTES); } ?>" id="cpassword" placeholder="<?= $lang->getTxt($idPage, "cpassword-placeholder"); ?>">
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseCPassword']) && !empty($_SESSION['inputResponseCPassword']) && $_SESSION['inputResponseCPassword'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseCPasswordMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseCPassword'] = ''; $_SESSION['inputResponseCPasswordMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>
                        <div class="form-row justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <button class="btn btn-dark w-100" type="button" data-toggle="modal" data-target="#savePassword"><?= $lang->getTxt($idPage, "submit"); ?></button>
                            </div>
                        </div>
                        <!-- == Captcha and crsf token == -->
                        <input type="hidden" id="g-recaptcha-response-2" name="g-recaptcha-response">
                        <input type="hidden" id="token" name="token" value="<?= $token ?>">
                        <!-- End Captcha and crsf token -->

                        <!-- Modal change email -->
                        <div class="modal fade" id="savePassword" tabindex="-1" role="dialog" aria-labelledby="savePasswordLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="savePasswordLabel">Change your password</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <p>Are you sure you want to change your password ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    $content = ob_get_clean();
    ob_start();
?>
<script type="text/javascript">
    $("#username").on("change paste keyup", function() {
        $('#usernameValueModal').html($('#username').val());
    });

    $("#email").on("change paste keyup", function() {
        $('#emailValueModal').html($('#email').val());
    });

    $(function () {
        $('[data-toggle="popover"]').popover()
    })

    grecaptcha.ready(function() {
        grecaptcha.execute('<?php echo SITE_KEY; ?>', {action: 'homepage'}).then(function(token) {
            document.getElementById('g-recaptcha-response').value = token;
            document.getElementById('g-recaptcha-response-1').value = token;
            document.getElementById('g-recaptcha-response-2').value = token;
        });
    });
</script>
<?php
    $script = ob_get_clean();
?>