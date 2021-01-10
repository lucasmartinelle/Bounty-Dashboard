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

    $asset = "../../assets/";
    $idPage = "showTemplate";

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

    <h1 class="h3 mb-1 text-gray-800 mb-3"><?= $lang->getTxt($idPage, "header"); ?></h1>

    <p>
        <span class="badge badge-pill badge-info" style="font-size: 15px;"><?= $lang->getTxt($idPage, "title-placeholder"); ?>
            <span class="badge badge-pill badge-light ml-2"><?= $template->title(); ?></span>
        </span>
        <span class="badge badge-pill badge-info" style="font-size: 15px;"><?= $lang->getTxt($idPage, "severity-placeholder"); ?>
            <span class="badge badge-pill badge-light ml-2"><?= $template->severity(); ?></span>
        </span>
        <span class="badge badge-pill badge-info" style="font-size: 15px;"><?= $lang->getTxt($idPage, "endpoint-placeholder"); ?>
            <span class="badge badge-pill badge-light ml-2"><?= $template->endpoint(); ?></span>
        </span>
        <span class="badge badge-pill badge-info" style="font-size: 15px;"><?= $lang->getTxt($idPage, "description-placeholder"); ?>
            <span class="badge badge-pill badge-light ml-2"><?= $template->description(); ?></span>
        </span>
    </p>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6 mt-2">
                <div class="card cm shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-impact"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div id="impact" class="w-100">
                            <textarea style="display:none;" name="impact"><?= $template->impact(); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="card cm shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-ressources"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div id="ressources" class="w-100">
                            <textarea style="display:none;" name="ressources"><?= $template->resources(); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-2">
                <div class="card cm shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-steps-to-reproduce"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div id="stepstoreproduce" class="w-100">
                            <textarea style="display:none;" name="stepstoreproduce"><?= $template->stepstoreproduce(); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="card cm shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-mitigation"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div id="mitigation" class="w-100">
                            <textarea style="display:none;" name="mitigation"><?= $template->mitigation(); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
    $content = ob_get_clean();
    ob_start();
?>

<link rel="stylesheet" href="<?= $asset ?>dist/editormd/css/editormd.css" />
<link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />

<?php
    $css = ob_get_clean();
    ob_start();
?>

<script src="<?= $asset ?>dist/editormd/editormd.min.js"></script>
<script src="<?= $asset ?>dist/editormd/languages/en.js"></script>
<script type="text/javascript">
    var stepstoreproduce, impact, mitigation, ressources;
    // Default export is a4 paper, portrait, using millimeters for units

    $(function() {
        stepstoreproduce = editormd("stepstoreproduce", {
            height: '500px',
            path   : "<?= $asset ?>dist/editormd/lib/",
            toolbarAutoFixed : false,
            readOnly : true,
            saveHTMLToTextarea : true,
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
            },
            onload : function() {
                this.previewing();
            }
        });
        
        impact = editormd("impact", {
            height: '500px',
            path   : "<?= $asset ?>dist/editormd/lib/",
            toolbarAutoFixed : false,
            readOnly : true, 
            toolbarIcons : function() {
                // Or return editormd.toolbarModes[name]; // full, simple, mini
                // Using "||" set icons align right.
                return ["undo", "redo", "|", "bold", "del", "italic", "quote", "uppercase", "lowercase", "|", "h1", "h2", "h3", "h4", "h5", "h6", "|", "list-ul", "list-ol", "hr", "|", "fullscreen", "watch", "preview"]
            }
        });

        mitigation = editormd("mitigation", {
            height: '500px',
            path   : "<?= $asset ?>dist/editormd/lib/",
            toolbarAutoFixed : false,
            readOnly : true, 
            toolbarIcons : function() {
                // Or return editormd.toolbarModes[name]; // full, simple, mini
                // Using "||" set icons align right.
                return ["undo", "redo", "|", "bold", "del", "italic", "quote", "uppercase", "lowercase", "|", "h1", "h2", "h3", "h4", "h5", "h6", "|", "list-ul", "list-ol", "hr", "|", "fullscreen", "watch", "preview"]
            }
        });

        ressources = editormd("ressources ", {
            height: '500px',
            path   : "<?= $asset ?>dist/editormd/lib/",
            toolbarAutoFixed : false,
            readOnly : true, 
            toolbarIcons : function() {
                // Or return editormd.toolbarModes[name]; // full, simple, mini
                // Using "||" set icons align right.
                return ["undo", "redo", "|", "bold", "del", "italic", "quote", "uppercase", "lowercase", "|", "h1", "h2", "h3", "h4", "h5", "h6", "|", "list-ul", "list-ol", "hr", "|", "fullscreen", "watch", "preview"]
            }
        });

        stepstoreproduce.previewing();

        $('[data-toggle="popover"]').popover();

        <?php if($pubkey != null): ?>
            grecaptcha.ready(function() {
                grecaptcha.execute('<?= $pubkey ?>', {action: 'homepage'}).then(function(token) {
                    document.getElementById('g-recaptcha-response').value = token;
                });
            });
        <?php endif; ?>
    });
</script>

<?php
    $script = ob_get_clean();
?>