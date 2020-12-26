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
    $idPage = "settings";
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

<?php if($admin): ?>
    <div class="card card-listuser shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-list-user"); ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>username</th>
                            <th>email</th>
                            <th>role</th>
                            <th><?= $lang->getTxt($idPage, "table-active"); ?></th>
                            <th>action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>username</th>
                            <th>email</th>
                            <th>role</th>
                            <th><?= $lang->getTxt($idPage, "table-active"); ?></th>
                            <th>action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php foreach($users as $user): ?>
                            <tr>
                                <td><?= '<span class="badge badge-pill badge-warning">' . $user->username() . '</span>'; ?></td>
                                <td><?= '<span class="badge badge-pill badge-warning">' . $user->email() . '</span>'; ?></td>
                                <td><?php if($user->role() == "admin"){ 
                                    echo '<span class="badge badge-pill badge-danger">' . $user->role() . '</span>'; 
                                } else { 
                                    echo '<span class="badge badge-pill badge-secondary">' . $user->role() . '</span>'; 
                                } ?></td>
                                <td><?= $user->active(); ?></td>
                                <td class="text-center">
                                    <?php if($user->role() == "hunter"): ?>
                                        <a href="<?= $routes->urlReplace("removeuser", array($user->id())); ?>" class="text-danger"><i class="fas fa-trash"></i></a>
                                    <?php else: ?>
                                        <i class="fas fa-trash"></i>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-change-language"); ?></h6>
                </div>
                <div class="wrapper-form">
                    <form method="post" class="mt-3">
                        <div class="form-row justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <select name="language" class="custom-select <?php if(!empty(htmlspecialchars($_SESSION['inputResponseLanguage'], ENT_QUOTES))) { echo htmlspecialchars($_SESSION['inputResponseLanguage'], ENT_QUOTES); } ?>">
                                    <option hidden><?= $lang->getTxt($idPage, "language-option"); ?></option>
                                    <option value="en">EN</option>
                                    <option value="fr">FR</option>
                                </select>
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseLanguage']) && !empty($_SESSION['inputResponseLanguage']) && $_SESSION['inputResponseLanguage'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseLanguageMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseLanguage'] = ''; $_SESSION['inputResponseLanguageMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>

                        <div class="form-row justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <button class="btn btn-info w-100" type="submit"><?= $lang->getTxt($idPage, "submit"); ?></button>
                            </div>
                        </div>
                        
                        <!-- == Captcha and crsf token == -->
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
                        <input type="hidden" id="token" name="token" value="<?= $token ?>">
                        <!-- End Captcha and crsf token -->
                    </form>
                </div>
            </div>
        </div>
        <?php if($admin): ?>
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-add-user"); ?></h6>
                    </div>
                    <div class="wrapper-form">
                        <form method="post" action="<?= $routes->url('adduser'); ?>">
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
                                    <input type="text" name="email" class="form-control <?php if(isset($_SESSION['inputResponseEmail']) && !empty($_SESSION['inputResponseEmail'])){ echo htmlspecialchars($_SESSION['inputResponseEmail'], ENT_QUOTES); } ?>" value="<?php if(isset($_SESSION['inputValueEmail']) && !empty($_SESSION['inputValueEmail'])){ echo htmlspecialchars($_SESSION['inputValueEmail'], ENT_QUOTES); $_SESSION['inputValueEmail'] = ''; } ?>" id="email" placeholder="<?= $lang->getTxt($idPage, "email-placeholder"); ?>">
                                    <!-- == If validation failed == -->
                                    <?php if(isset($_SESSION['inputResponseEmail']) && !empty($_SESSION['inputResponseEmail']) && $_SESSION['inputResponseEmail'] == 'invalid'): ?>
                                        <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseEmailMessage'], ENT_QUOTES); ?>"></i></span>
                                    <?php endif; $_SESSION['inputResponseEmail'] = ''; $_SESSION['inputResponseEmailMessage'] = ''; ?> <!-- End of validation failed -->
                                </div>
                            </div>
                            <div class="form-row justify-content-center">
                                <div class="col-md-10 mb-3 mt-2">
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
                                    <select name="role" class="custom-select <?php if(!empty(htmlspecialchars($_SESSION['inputResponseRole'], ENT_QUOTES))) { echo htmlspecialchars($_SESSION['inputResponseRole'], ENT_QUOTES); } ?>">
                                        <option hidden>role</option>
                                        <option value="admin"><?= $lang->getTxt($idPage, "role-admin"); ?></option>
                                        <option value="hunter"><?= $lang->getTxt($idPage, "role-hunter"); ?></option>
                                    </select>
                                    <!-- == If validation failed == -->
                                    <?php if(isset($_SESSION['inputResponseRole']) && !empty($_SESSION['inputResponseRole']) && $_SESSION['inputResponseRole'] == 'invalid'): ?>
                                        <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseRoleMessage'], ENT_QUOTES); ?>"></i></span>
                                    <?php endif; $_SESSION['inputResponseRole'] = ''; $_SESSION['inputResponseRoleMessage'] = ''; ?> <!-- End of validation failed -->
                                </div>
                            </div>
                            <div class="form-row justify-content-center">
                                <div class="col-md-10 mb-3 mt-2">
                                    <button class="btn btn-info w-100" type="submit"><?= $lang->getTxt($idPage, "submit"); ?></button>
                                </div>
                            </div>
                            <!-- == Captcha and crsf token == -->
                            <input type="hidden" id="g-recaptcha-response-1" name="g-recaptcha-response">
                            <input type="hidden" id="token" name="token" value="<?= $token ?>">
                            <!-- End Captcha and crsf token -->
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
    $content = ob_get_clean();
    ob_start();
?>

<link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />

<?php
    $css = ob_get_clean();
    ob_start();
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function() {
        <?php if(htmlspecialchars($_COOKIE['lang']) == 'EN'): ?>
            $('#dataTable').DataTable();
        <?php else: ?>
            $('#dataTable').DataTable( {
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
                }
            });
        <?php endif; ?>
    });

    $(function () {
        $('[data-toggle="popover"]').popover()
    })

    grecaptcha.ready(function() {
        grecaptcha.execute('<?php echo SITE_KEY; ?>', {action: 'homepage'}).then(function(token) {
            document.getElementById('g-recaptcha-response').value = token;
            document.getElementById('g-recaptcha-response-1').value = token;
        });
    });
</script>

<?php
    $script = ob_get_clean();
?>