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
    $idPage = "createReport";
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

    <div class="d-flex justify-content-between">
        <h1 class="h3 mb-1 text-gray-800 mb-3"><?= $lang->getTxt($idPage, "header"); ?></h1>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#useTemplate" style="height: max-content;">
            Use a template
        </button>
    </div>

    <div class="container-fluid">
        <form method="post">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-create-report"); ?></h6>
                        </div>
                        <div class="card-body">
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
                                    <input type="text" name="identifiant" class="form-control <?php if(isset($_SESSION['inputResponseIdentifiant']) && !empty($_SESSION['inputResponseIdentifiant'])){ echo htmlspecialchars($_SESSION['inputResponseIdentifiant'], ENT_QUOTES); } ?>" value="<?php if(isset($_SESSION['inputValueIdentifiant']) && !empty($_SESSION['inputValueIdentifiant'])){ echo htmlspecialchars($_SESSION['inputValueIdentifiant'], ENT_QUOTES); $_SESSION['inputValueIdentifiant'] = ''; } ?>" id="identifiant" placeholder="<?= $lang->getTxt($idPage, "identifiant-placeholder"); ?>">
                                    <!-- == If validation failed == -->
                                    <?php if(isset($_SESSION['inputResponseIdentifiant']) && !empty($_SESSION['inputResponseIdentifiant']) && $_SESSION['inputResponseIdentifiant'] == 'invalid'): ?>
                                        <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseIdentifiantMessage'], ENT_QUOTES); ?>"></i></span>
                                    <?php endif; $_SESSION['inputResponseIdentifiant'] = ''; $_SESSION['inputResponseIdentifiantMessage'] = ''; ?> <!-- End of validation failed -->
                                </div>
                            </div>
                            <div class="form-row justify-content-center">
                                <div class="col-md-10 mb-3 mt-2">
                                    <input type="text" name="date" id="datepicker" data-toggle="datepicker" class="form-control <?php if(isset($_SESSION['inputResponseDate']) && !empty($_SESSION['inputResponseDate'])){ echo htmlspecialchars($_SESSION['inputResponseDate'], ENT_QUOTES); } ?>" placeholder="<?= $lang->getTxt($idPage, "date-placeholder"); ?>" value="<?php if(isset($_SESSION['inputValueDate']) && !empty($_SESSION['inputValueDate'])){ echo htmlspecialchars($_SESSION['inputValueDate'], ENT_QUOTES); $_SESSION['inputValueDate'] = ''; } ?>">
                                    <!-- == If validation failed == -->
                                    <?php if(isset($_SESSION['inputResponseDate']) && !empty($_SESSION['inputResponseDate']) && $_SESSION['inputResponseDate'] == 'invalid'): ?>
                                        <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseDateMessage'], ENT_QUOTES); ?>"></i></span>
                                    <?php endif; $_SESSION['inputResponseDate'] = ''; $_SESSION['inputResponseDateMessage'] = ''; ?> <!-- End of validation failed -->
                                </div>
                            </div>
                            <div class="form-row justify-content-center">
                                <div class="col-md-10 mb-3 mt-2">
                                    <input type="text" name="severity" id="severity" class="form-control <?php if(isset($_SESSION['inputResponseSeverity']) && !empty($_SESSION['inputResponseSeverity'])){ echo htmlspecialchars($_SESSION['inputResponseSeverity'], ENT_QUOTES); } ?>" placeholder="<?= $lang->getTxt($idPage, "severity-placeholder"); ?>" value="<?php if(isset($_SESSION['inputValueSeverity']) && !empty($_SESSION['inputValueSeverity'])){ echo htmlspecialchars($_SESSION['inputValueSeverity'], ENT_QUOTES); $_SESSION['inputValueSeverity'] = ''; } ?>">
                                    <!-- == If validation failed == -->
                                    <?php if(isset($_SESSION['inputResponseSeverity']) && !empty($_SESSION['inputResponseSeverity']) && $_SESSION['inputResponseSeverity'] == 'invalid'): ?>
                                        <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseSeverityMessage'], ENT_QUOTES); ?>"></i></span>
                                    <?php endif; $_SESSION['inputResponseSeverity'] = ''; $_SESSION['inputResponseSeverityMessage'] = ''; ?> <!-- End of validation failed -->
                                </div>
                            </div>
                            <div class="form-row justify-content-center">
                                <div class="col-md-10 mb-3 mt-2">
                                    <input type="text" name="endpoint" id="endpoint" class="form-control <?php if(isset($_SESSION['inputResponseEndpoint']) && !empty($_SESSION['inputResponseEndpoint'])){ echo htmlspecialchars($_SESSION['inputResponseEndpoint'], ENT_QUOTES); } ?>" placeholder="<?= $lang->getTxt($idPage, "endpoint-placeholder"); ?>" value="<?php if(isset($_SESSION['inputValueEndpoint']) && !empty($_SESSION['inputValueEndpoint'])){ echo htmlspecialchars($_SESSION['inputValueEndpoint'], ENT_QUOTES); $_SESSION['inputValueEndpoint'] = ''; } ?>">
                                    <!-- == If validation failed == -->
                                    <?php if(isset($_SESSION['inputResponseEndpoint']) && !empty($_SESSION['inputResponseEndpoint']) && $_SESSION['inputResponseEndpoint'] == 'invalid'): ?>
                                        <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseEndpointMessage'], ENT_QUOTES); ?>"></i></span>
                                    <?php endif; $_SESSION['inputResponseEndpoint'] = ''; $_SESSION['inputResponseEndpointMessage'] = ''; ?> <!-- End of validation failed -->
                                </div>
                            </div>
                            <div class="form-row selectformrow justify-content-center">
                                <div class="col-md-10 mb-3 mt-2">
                                    <select class="form-control <?php if(isset($_SESSION['inputResponseProgram']) && !empty($_SESSION['inputResponseProgram'])){ echo htmlspecialchars($_SESSION['inputResponseProgram'], ENT_QUOTES); } ?>" id="program" name="program">
                                        <option hidden ><?= $lang->getTxt($idPage, "program-placeholder"); ?></option>
                                        <?php foreach($programs as $program): ?>
                                            <option value="<?= $program->id(); ?>"><?= $program->name(); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <!-- == If validation failed == -->
                                    <?php if(isset($_SESSION['inputResponseProgram']) && !empty($_SESSION['inputResponseProgram']) && $_SESSION['inputResponseProgram'] == 'invalid'): ?>
                                        <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseProgramMessage'], ENT_QUOTES); ?>"></i></span>
                                    <?php endif; $_SESSION['inputResponseProgram'] = ''; $_SESSION['inputResponseProgramMessage'] = ''; ?> <!-- End of validation failed -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card cm shadow mb-4">
                        <?php if(isset($_SESSION['inputResponseImpact']) && !empty($_SESSION['inputResponseImpact']) && htmlspecialchars($_SESSION['inputResponseImpact'], ENT_QUOTES) == "invalid"): ?>
                            <div class="card-header bg-danger py-3">
                                <div class="d-flex justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-light"><?= $lang->getTxt($idPage, "header-impact"); ?></h6>
                                    <!-- == If validation failed == -->
                                    <?php if(isset($_SESSION['inputResponseImpact']) && !empty($_SESSION['inputResponseImpact']) && $_SESSION['inputResponseImpact'] == 'invalid'): ?>
                                        <span><i class="fas fa-info-circle text-light me" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseImpactMessage'], ENT_QUOTES); ?>"></i></span>
                                    <?php endif; $_SESSION['inputResponseImpact'] = ''; $_SESSION['inputResponseImpactMessage'] = ''; ?> <!-- End of validation failed -->
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-impact"); ?></h6>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <div id="impact" class="w-100">
                                <textarea style="display:none;" name="impact"><?php if(isset($_SESSION['inputValueImpact']) && !empty($_SESSION['inputValueImpact'])){ echo htmlspecialchars_decode($_SESSION['inputValueImpact'], ENT_QUOTES); $_SESSION['inputValueImpact'] = ''; } ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card cm shadow mb-4">
                        <?php if(isset($_SESSION['inputResponseRessources']) && !empty($_SESSION['inputResponseRessources']) && htmlspecialchars($_SESSION['inputResponseRessources'], ENT_QUOTES) == "invalid"): ?>
                            <div class="card-header bg-danger py-3">
                                <div class="d-flex justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-light"><?= $lang->getTxt($idPage, "header-ressources"); ?></h6>
                                    <!-- == If validation failed == -->
                                    <?php if(isset($_SESSION['inputResponseRessources']) && !empty($_SESSION['inputResponseRessources']) && $_SESSION['inputResponseRessources'] == 'invalid'): ?>
                                        <span><i class="fas fa-info-circle text-light me" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseRessourcesMessage'], ENT_QUOTES); ?>"></i></span>
                                    <?php endif; $_SESSION['inputResponseRessources'] = ''; $_SESSION['inputResponseRessourcesMessage'] = ''; ?> <!-- End of validation failed -->
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-ressources"); ?></h6>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <div id="ressources" class="w-100">
                                <textarea style="display:none;" name="ressources"><?php if(isset($_SESSION['inputValueRessources']) && !empty($_SESSION['inputValueRessources'])){ echo htmlspecialchars_decode($_SESSION['inputValueRessources'], ENT_QUOTES); $_SESSION['inputValueRessources'] = ''; } ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card cm shadow mb-4">
                        <?php if(isset($_SESSION['inputResponseStepstoreproduce']) && !empty($_SESSION['inputResponseStepstoreproduce']) && htmlspecialchars($_SESSION['inputResponseStepstoreproduce'], ENT_QUOTES) == "invalid"): ?>
                            <div class="card-header bg-danger py-3">
                                <div class="d-flex justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-light"><?= $lang->getTxt($idPage, "header-steps-to-reproduce"); ?></h6>
                                    <!-- == If validation failed == -->
                                    <?php if(isset($_SESSION['inputResponseStepstoreproduce']) && !empty($_SESSION['inputResponseStepstoreproduce']) && $_SESSION['inputResponseStepstoreproduce'] == 'invalid'): ?>
                                        <span><i class="fas fa-info-circle text-light me" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseStepstoreproduceMessage'], ENT_QUOTES); ?>"></i></span>
                                    <?php endif; $_SESSION['inputResponseStepstoreproduce'] = ''; $_SESSION['inputResponseStepstoreproduceMessage'] = ''; ?> <!-- End of validation failed -->
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-steps-to-reproduce"); ?></h6>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <div id="stepstoreproduce" class="w-100">
                                <textarea style="display:none;" name="stepstoreproduce"><?php if(isset($_SESSION['inputValueStepstoreproduce']) && !empty($_SESSION['inputValueStepstoreproduce'])){ echo htmlspecialchars_decode($_SESSION['inputValueStepstoreproduce'], ENT_QUOTES); $_SESSION['inputValueStepstoreproduce'] = ''; } ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card cm shadow mb-4">
                        <?php if(isset($_SESSION['inputResponseMitigation']) && !empty($_SESSION['inputResponseMitigation']) && htmlspecialchars($_SESSION['inputResponseMitigation'], ENT_QUOTES) == "invalid"): ?>
                            <div class="card-header bg-danger py-3">
                                <div class="d-flex justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-light"><?= $lang->getTxt($idPage, "header-mitigation"); ?></h6>
                                    <!-- == If validation failed == -->
                                    <?php if(isset($_SESSION['inputResponseMitigation']) && !empty($_SESSION['inputResponseMitigation']) && $_SESSION['inputResponseMitigation'] == 'invalid'): ?>
                                        <span><i class="fas fa-info-circle text-light me" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseMitigationMessage'], ENT_QUOTES); ?>"></i></span>
                                    <?php endif; $_SESSION['inputResponseMitigation'] = ''; $_SESSION['inputResponseMitigationMessage'] = ''; ?> <!-- End of validation failed -->
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-mitigation"); ?></h6>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <div id="mitigation" class="w-100">
                                <textarea style="display:none;" name="mitigation"><?php if(isset($_SESSION['inputValueMitigation']) && !empty($_SESSION['inputValueMitigation'])){ echo htmlspecialchars_decode($_SESSION['inputValueMitigation'], ENT_QUOTES); $_SESSION['inputValueMitigation'] = ''; } ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="container m-auto btndiv">
                        <button class="btn btn-info w-100" type="submit" style="height: 50px;"><?= $lang->getTxt($idPage, "submit"); ?></button>
                    </div>
                </div>
            </div>

            <!-- == Captcha and crsf token == -->
            <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
            <input type="hidden" id="token" name="token" value="<?= $token ?>">
            <!-- End Captcha and crsf token -->
        </form>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="useTemplate" tabindex="-1" role="dialog" aria-labelledby="useTemplateLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="post" action="<?= $routes->url('useTemplate'); ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="useTemplateLabel"><?= $lang->getTxt($idPage, "header-use-template"); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row selectformrow justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <select class="form-control <?php if(isset($_SESSION['inputResponseTemplate']) && !empty($_SESSION['inputResponseTemplate'])){ echo htmlspecialchars($_SESSION['inputResponseTemplate'], ENT_QUOTES); } ?>" id="template" name="template">
                                    <option hidden selected value=""><?= $lang->getTxt($idPage, "template-placeholder"); ?></option>
                                    <?php foreach($templates as $template): ?>
                                        <option value="<?= $template->id(); ?>"><?= $template->title(); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseTemplate']) && !empty($_SESSION['inputResponseTemplate']) && $_SESSION['inputResponseTemplate'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseTemplateMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseTemplate'] = ''; $_SESSION['inputResponseTemplateMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->getTxt($idPage, "modal-nav-close"); ?></button>
                        <button type="submit" class="btn btn-primary"><?= $lang->getTxt($idPage, "modal-nav-confirm"); ?></button>
                    </div>
                    <!-- == Captcha and crsf token == -->
                    <input type="hidden" id="g-recaptcha-response-2" name="g-recaptcha-response">
                    <input type="hidden" id="token" name="token" value="<?= $token ?>">
                    <!-- End Captcha and crsf token -->
                </form>
            </div>
        </div>
    </div>

<?php
    $content = ob_get_clean();
    ob_start();
?>

<link rel="stylesheet" href="<?= $asset ?>dist/editormd/css/editormd.css" />
<link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
<link href="<?= $asset ?>dist/datepicker.css" rel="stylesheet">

<?php
    $css = ob_get_clean();
    ob_start();
?>

<script src="<?= $asset ?>dist/editormd/editormd.min.js"></script>
<script src="<?= $asset ?>dist/editormd/languages/en.js"></script>
<script src="<?= $asset ?>dist/datepicker.js"></script>
<script type="text/javascript">

    var stepstoreproduce, impact, mitigation, ressources;

    $(function() {
        $('[data-toggle="datepicker"]').datepicker();
        
        var stepstoreproduce = editormd("stepstoreproduce", {
            height: '500px',
            path   : "<?= $asset ?>dist/editormd/lib/",
            toolbarAutoFixed : false,
            toolbarIcons : function() {
                // Or return editormd.toolbarModes[name]; // full, simple, mini
                // Using "||" set icons align right.
                return ["undo", "redo", "|", "bold", "del", "italic", "quote", "uppercase", "lowercase", "|", "h1", "h2", "h3", "h4", "h5", "h6", "|", "list-ul", "list-ol", "hr", "|", "fullscreen", "watch", "preview"]
            },
            onfullscreen : function() {
                if($('#stepstoreproduce').hasClass("editormd-fullscreen")){
                    $('#stepstoreproduce').css("z-index", "5000");
                } else if($('#impact').hasClass("editormd-fullscreen")){
                    $('#impact').css("z-index", "5000");
                } else if($('#mitigation').hasClass("editormd-fullscreen")){
                    $('#mitigation').css("z-index", "5000");
                } else if($('#ressources').hasClass("editormd-fullscreen")){
                    $('#ressources').css("z-index", "5000");
                }
            },
            onfullscreenExit : function() {
                if(!$('#stepstoreproduce').hasClass("editormd-fullscreen")){
                    $('#stepstoreproduce').css("z-index", "unset");
                }
                if(!$('#impact').hasClass("editormd-fullscreen")){
                    $('#impact').css("z-index", "unset");
                }
                if(!$('#mitigation').hasClass("editormd-fullscreen")){
                    $('#mitigation').css("z-index", "unset");
                }
                if(!$('#ressources').hasClass("editormd-fullscreen")){
                    $('#ressources').css("z-index", "unset");
                }
            }
        });
        
        var impact = editormd("impact", {
            height: '500px',
            path   : "<?= $asset ?>dist/editormd/lib/",
            toolbarAutoFixed : false,
            toolbarIcons : function() {
                // Or return editormd.toolbarModes[name]; // full, simple, mini
                // Using "||" set icons align right.
                return ["undo", "redo", "|", "bold", "del", "italic", "quote", "uppercase", "lowercase", "|", "h1", "h2", "h3", "h4", "h5", "h6", "|", "list-ul", "list-ol", "hr", "|", "fullscreen", "watch", "preview"]
            }
        });

        var mitigation = editormd("mitigation", {
            height: '500px',
            path   : "<?= $asset ?>dist/editormd/lib/",
            toolbarAutoFixed : false,
            toolbarIcons : function() {
                // Or return editormd.toolbarModes[name]; // full, simple, mini
                // Using "||" set icons align right.
                return ["undo", "redo", "|", "bold", "del", "italic", "quote", "uppercase", "lowercase", "|", "h1", "h2", "h3", "h4", "h5", "h6", "|", "list-ul", "list-ol", "hr", "|", "fullscreen", "watch", "preview"]
            }
        });

        var ressources = editormd("ressources ", {
            height: '500px',
            path   : "<?= $asset ?>dist/editormd/lib/",
            toolbarAutoFixed : false,
            toolbarIcons : function() {
                // Or return editormd.toolbarModes[name]; // full, simple, mini
                // Using "||" set icons align right.
                return ["undo", "redo", "|", "bold", "del", "italic", "quote", "uppercase", "lowercase", "|", "h1", "h2", "h3", "h4", "h5", "h6", "|", "list-ul", "list-ol", "hr", "|", "fullscreen", "watch", "preview"]
            }
        });

        $(function () {
            $('[data-toggle="popover"]').popover()
        })

        grecaptcha.ready(function() {
            grecaptcha.execute('<?php echo SITE_KEY; ?>', {action: 'homepage'}).then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
                document.getElementById('g-recaptcha-response-2').value = token;
            });
        });
    });
</script>

<?php
    $script = ob_get_clean();
?>