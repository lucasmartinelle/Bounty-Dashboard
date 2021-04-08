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

    $asset = "../../../assets/";
    $idPage = "notes";

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

    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1 class="h3 mb-1 text-gray-800 mb-3"><?= $lang->getTxt($idPage, "header"); ?></h1>
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#createNote" style="height: max-content;">
                <?= $lang->getTxt($idPage, "add-notes"); ?>
            </button>
        </div>
    </div>
    <p>
        <span class="badge badge-pill badge-info" style="font-size: 15px;"><?= $lang->getTxt($idPage, "program-name"); ?>
            <span class="badge badge-pill badge-light ml-2"><?= $program; ?></span>
        </span>
    </p>

    <div class="container-fluid">
        <div id="accordion">
            <?php foreach($notes as $note): ?>
                <div class="card">
                    <div class="card-header" id="heading<?= $note->id();?>">
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse<?= $note->id();?>" aria-expanded="false" aria-controls="collapse<?= $note->id();?>">
                                    <?= $note->titre(); ?>
                                </button>
                            </h5>
                            <div>
                                <i class="fas fa-trash text-danger" data-toggle="modal" data-url="<?= $routes->urlReplace("deleteNote", array($note->id())); ?>" data-target="#confirmDelete" id="deleteNote"></i>
                                <i class="fas fa-pen-square text-success" data-toggle="modal" data-url="<?= $routes->urlReplace("changeNote", array($note->id())); ?>" data-target="#updateNote" id="upNote"></i>
                            </div>
                        </div>
                    </div>
                    <div id="collapse<?= $note->id();?>" class="collapse" aria-labelledby="heading<?= $note->id();?>" data-parent="#accordion">
                        <div class="card-body">
                            <?= $note->text();?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="createNote" tabindex="-1" role="dialog" aria-labelledby="#createNoteLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createNoteLabel"><?= $lang->getTxt($idPage, "add-notes"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" class="eventForm mt-4">
                    <div class="form-row justify-content-center">
                        <div class="col-md-10 mb-3 mt-1">
                            <input type="text" name="title" class="form-control <?php if(isset($_SESSION['inputResponseTitle']) && !empty($_SESSION['inputResponseTitle'])){ echo htmlspecialchars($_SESSION['inputResponseTitle'], ENT_QUOTES); } ?>" value="<?php if(isset($_SESSION['inputValueTitle']) && !empty($_SESSION['inputValueTitle'])){ echo htmlspecialchars($_SESSION['inputValueTitle'], ENT_QUOTES); $_SESSION['inputValueTitle'] = ''; } ?>" id="title" placeholder="<?= $lang->getTxt($idPage, "title-placeholder"); ?>">
                            <!-- == If validation failed == -->
                            <?php if(isset($_SESSION['inputResponseTitle']) && !empty($_SESSION['inputResponseTitle']) && $_SESSION['inputResponseTitle'] == 'invalid'): ?>
                                <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseTitleMessage'], ENT_QUOTES); ?>"></i></span>
                            <?php endif; $_SESSION['inputResponseTitle'] = ''; $_SESSION['inputResponseTitleMessage'] = ''; ?> <!-- End of validation failed -->
                        </div>
                    </div>
                    <div class="form-row justify-content-center">
                        <div class="col-md-10 mb-3 mt-1">
                            <textarea type="text" name="message" class="form-control <?php if(isset($_SESSION['inputResponseMessage']) && !empty($_SESSION['inputResponseMessage'])){ echo htmlspecialchars($_SESSION['inputResponseMessage'], ENT_QUOTES); } ?>" id="message" placeholder="<?= $lang->getTxt($idPage, "message-placeholder"); ?>" rows="4"><?php if(isset($_SESSION['inputValueMessage']) && !empty($_SESSION['inputValueMessage'])){ echo htmlspecialchars($_SESSION['inputValueMessage'], ENT_QUOTES); $_SESSION['inputValueMessage'] = ''; } ?></textarea>
                            <!-- == If validation failed == -->
                            <?php if(isset($_SESSION['inputResponseMessage']) && !empty($_SESSION['inputResponseMessage']) && $_SESSION['inputResponseMessage'] == 'invalid'): ?>
                                <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseMessageMessage'], ENT_QUOTES); ?>"></i></span>
                            <?php endif; $_SESSION['inputResponseMessage'] = ''; $_SESSION['inputResponseMessageMessage'] = ''; ?> <!-- End of validation failed -->
                        </div>
                    </div>
                    <!-- == Captcha and crsf token == -->
                    <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
                    <input type="hidden" id="token" name="token" value="<?= $token ?>">
                    <!-- End Captcha and crsf token -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->getTxt($idPage, "modal-nav-close"); ?></button>
                        <button type="submit" class="btn btn-primary"><?= $lang->getTxt($idPage, "modal-nav-confirm"); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateNote" tabindex="-1" role="dialog" aria-labelledby="#updateNoteLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateNoteLabel"><?= $lang->getTxt($idPage, "update-note"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="" class="eventForm mt-4" id="formUpdate">
                    <div class="form-row justify-content-center">
                        <div class="col-md-10 mb-3 mt-1">
                            <input type="text" name="title" class="form-control <?php if(isset($_SESSION['inputResponseTitle2']) && !empty($_SESSION['inputResponseTitle2'])){ echo htmlspecialchars($_SESSION['inputResponseTitle2'], ENT_QUOTES); } ?>" value="<?php if(isset($_SESSION['inputValueTitle2']) && !empty($_SESSION['inputValueTitle2'])){ echo htmlspecialchars($_SESSION['inputValueTitle2'], ENT_QUOTES); $_SESSION['inputValueTitle2'] = ''; } ?>" id="title" placeholder="<?= $lang->getTxt($idPage, "title-placeholder"); ?>">
                            <!-- == If validation failed == -->
                            <?php if(isset($_SESSION['inputResponseTitle2']) && !empty($_SESSION['inputResponseTitle2']) && $_SESSION['inputResponseTitle2'] == 'invalid'): ?>
                                <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseTitleMessage2'], ENT_QUOTES); ?>"></i></span>
                            <?php endif; $_SESSION['inputResponseTitle2'] = ''; $_SESSION['inputResponseTitleMessage2'] = ''; ?> <!-- End of validation failed -->
                        </div>
                    </div>
                    <div class="form-row justify-content-center">
                        <div class="col-md-10 mb-3 mt-1">
                            <textarea type="text" name="message" class="form-control" id="message" placeholder="<?= $lang->getTxt($idPage, "message-placeholder"); ?>" rows="4"><?php if(isset($_SESSION['inputValueMessage2']) && !empty($_SESSION['inputValueMessage2'])){ echo htmlspecialchars($_SESSION['inputValueMessage2'], ENT_QUOTES); $_SESSION['inputValueMessage2'] = ''; } ?></textarea>
                        </div>
                    </div>
                    <!-- == Captcha and crsf token == -->
                    <input type="hidden" id="g-recaptcha-response-2" name="g-recaptcha-response">
                    <input type="hidden" id="token" name="token" value="<?= $token ?>">
                    <!-- End Captcha and crsf token -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->getTxt($idPage, "modal-nav-close"); ?></button>
                        <button type="submit" class="btn btn-primary"><?= $lang->getTxt($idPage, "modal-nav-confirm"); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteLabel"><?= $lang->getTxt($idPage, "delete-note"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p><?= $lang->getTxt($idPage, "confirmation-delete-note"); ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->getTxt($idPage, "modal-nav-close"); ?></button>
                    <a class="btn btn-primary" href="" id="deleteNoteLink"><?= $lang->getTxt($idPage, "modal-nav-confirm"); ?></a>
                </div>
            </div>
        </div>
    </div>

<?php 
    $content = ob_get_clean();
    ob_start();
?>

<script type="text/javascript">
    <?php if($pubkey != null): ?>
        grecaptcha.ready(function() {
            grecaptcha.execute('<?= $pubkey ?>', {action: 'homepage'}).then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
                document.getElementById('g-recaptcha-response-2').value = token;
            });
        });
    <?php endif; ?>
    $('[data-toggle="popover"]').popover();

    $('#confirmDelete').on('shown.bs.modal', function (e) {
        var button = $(e.relatedTarget);
        var url = button.data('url');
        var modal = $(this)
        modal.find('#deleteNoteLink').attr('href', url)
    });

    $('#updateNote').on('shown.bs.modal', function (e) {
        var button = $(e.relatedTarget);
        var url = button.data('url');
        var modal = $(this)
        modal.find('#formUpdate').attr('action', url)
    });
</script>

<?php
    $script = ob_get_clean();
?>