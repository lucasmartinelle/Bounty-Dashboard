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
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#createPlatform" style="height: max-content;">
                <?= $lang->getTxt($idPage, "add-platform"); ?>
            </button>
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

    <?php foreach($platforms as $platform): ?>
    <div class="container sh-dark bd-callout bd-callout-info py-3" style="margin-bottom: 50px;">
        <div class="card" style="background: transparent; border: 0;">
            <div class="row">
                <div class="col-lg-2 col-md-3 col-sm-4 mb-4 mb-sm-0">
                    <img src="<?= $asset; ?>uploads/<?= $platform->logo(); ?>" class="w-100">
                </div>
                <div class="col-lg-10 col-md-9 col-sm-8 px-3">
                    <div class="card-block px-3">
                        <h4 class="card-title badge badge-pill badge-warning" style="font-size: 20px;"><?= $platform->name(); ?></h4>
                        <p class="card-text"><?= $platform->description(); ?></p>
                        <a href="#" class="btn btn-primary">Read More</a>
                        <?php if($admin): ?>
                            <a href="<?= $routes->urlReplace("platformDelete", array($platform->id())); ?>" class="btn btn-danger">Delete</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>


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
                                <span class="input-group-btn">
                                    <span class="btn btn-default btn-file">
                                        Logo <input name="file" type="file" id="imgInp">
                                    </span>
                                </span>
                                <input type="text" class="form-control <?php if(isset($_SESSION['inputResponseFile']) && !empty($_SESSION['inputResponseFile'])){ echo htmlspecialchars($_SESSION['inputResponseFile'], ENT_QUOTES); } ?>" id="filename" readonly>
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseFile']) && !empty($_SESSION['inputResponseFile']) && $_SESSION['inputResponseFile'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseFileMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseFile'] = ''; $_SESSION['inputResponseFileMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>
                        <div class="form-row justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <input type="text" name="name" class="form-control <?php if(isset($_SESSION['inputResponseName']) && !empty($_SESSION['inputResponseName'])){ echo htmlspecialchars($_SESSION['inputResponseName'], ENT_QUOTES); } ?>" value="<?php if(isset($_SESSION['inputValueName']) && !empty($_SESSION['inputValueName'])){ echo htmlspecialchars($_SESSION['inputValueName'], ENT_QUOTES); $_SESSION['inputValueName'] = ''; } ?>" id="name" placeholder="<?= $lang->getTxt($idPage, "name-placeholder"); ?>">
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseName']) && !empty($_SESSION['inputResponseName']) && $_SESSION['inputResponseName'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseNameMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseName'] = ''; $_SESSION['inputResponseNameMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>
                        <div class="form-row justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <textarea name="description" id="description" class="form-control <?php if(isset($_SESSION['inputResponseDescription']) && !empty($_SESSION['inputResponseDescription'])){ echo htmlspecialchars($_SESSION['inputResponseDescription'], ENT_QUOTES); } ?>" id="description" rows="3" placeholder="<?= $lang->getTxt($idPage, "description-placeholder"); ?>"><?php if(isset($_SESSION['inputValueDescription']) && !empty($_SESSION['inputValueDescription'])){ echo htmlspecialchars($_SESSION['inputValueDescription'], ENT_QUOTES); $_SESSION['inputValueDescription'] = ''; } ?></textarea>
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseDescription']) && !empty($_SESSION['inputResponseDescription']) && $_SESSION['inputResponseDescription'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseDescriptionMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseDescription'] = ''; $_SESSION['inputResponseDescriptionMessage'] = ''; ?> <!-- End of validation failed -->
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

<?php 
    $content = ob_get_clean();
    ob_start();
?>

<script type="text/javascript">
    $(document).ready( function() {
    	$(document).on('change', '.btn-file :file', function() {
		    var input = $(this),
			label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		    input.trigger('fileselect', [label]);
		});

		$('.btn-file :file').on('fileselect', function(event, label) {
		    $('#filename').val(label);
		});	
	});

    $(function () {
        $('[data-toggle="popover"]').popover()
    })

    grecaptcha.ready(function() {
        grecaptcha.execute('<?php echo SITE_KEY; ?>', {action: 'homepage'}).then(function(token) {
            document.getElementById('g-recaptcha-response').value = token;
        });
    });
</script>

<?php
    $script = ob_get_clean();
?>