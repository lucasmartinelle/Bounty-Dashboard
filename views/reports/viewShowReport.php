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
    $idPage = "showReport";
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

    <div class="d-flex justify-content-between" id="test">
        <h1 class="h3 mb-1 text-gray-800 mb-3"><?= $lang->getTxt($idPage, "header"); ?></h1>
        <div class="bt">
            <button type="button" class="btn btn-info" style="height: max-content;" id="generateReportHtml">
                <?= $lang->getTxt($idPage, "generate-report-html"); ?>
            </button>
            <button type="button" class="btn btn-warning" style="height: max-content;" id="generateReportMarkdown">
                <?= $lang->getTxt($idPage, "generate-report-markdown"); ?>
            </button>
        </div>
    </div>

    <p>
        <span class="badge badge-pill badge-info" style="font-size: 15px;"><?= $lang->getTxt($idPage, "title-placeholder"); ?>
            <span class="badge badge-pill badge-light ml-2"><?= $report->title(); ?></span>
        </span>
        <span class="badge badge-pill badge-info" style="font-size: 15px;"><?= $lang->getTxt($idPage, "date-placeholder"); ?>
            <span class="badge badge-pill badge-light ml-2"><?= $report->date(); ?></span>
        </span>
        <span class="badge badge-pill badge-info" style="font-size: 15px;"><?= $lang->getTxt($idPage, "severity-placeholder"); ?>
            <span class="badge badge-pill badge-light ml-2"><?= $report->severity(); ?></span>
        </span>
        <span class="badge badge-pill badge-info" style="font-size: 15px;"><?= $lang->getTxt($idPage, "endpoint-placeholder"); ?>
            <span class="badge badge-pill badge-light ml-2"><?= $report->endpoint(); ?></span>
        </span>
        <span class="badge badge-pill badge-info" style="font-size: 15px;"><?= $lang->getTxt($idPage, "program-placeholder"); ?>
            <span class="badge badge-pill badge-light ml-2"><?= $program->name(); ?></span>
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
                            <textarea style="display:none;" name="impact"><?= $report->impact(); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="card cm shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-ressources"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div id="ressources" class="w-100">
                            <textarea style="display:none;" name="ressources"><?= $report->resources(); ?></textarea>
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
                            <textarea style="display:none;" name="stepstoreproduce"><?= $report->stepstoreproduce(); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="card cm shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-mitigation"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div id="mitigation" class="w-100">
                            <textarea style="display:none;" name="mitigation"><?= $report->mitigation(); ?></textarea>
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
<link href="<?= $asset ?>dist/datepicker.css" rel="stylesheet">

<?php
    $css = ob_get_clean();
    ob_start();
?>

<script src="<?= $asset ?>dist/editormd/editormd.min.js"></script>
<script src="<?= $asset ?>dist/editormd/languages/en.js"></script>
<script src="<?= $asset ?>dist/html2pdf.bundle.min.js"></script>
<script src="<?= $asset ?>dist/datepicker.js"></script>
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
                grecaptcha.execute('<?php echo SITE_KEY; ?>', {action: 'homepage'}).then(function(token) {
                    document.getElementById('g-recaptcha-response').value = token;
                });
            });
        <?php endif; ?>
    });

    
    $('#generateReportHtml').click(function(){
        var impactHTML = impact.getHTML();
        var stepstoreproduceHTML = stepstoreproduce.getHTML();
        var ressourcesHTML = ressources.getHTML();
        var mitigationHTML = mitigation.getHTML();
        var html = "<div class='container-fluid bg-primary'><p class='navbar-brand d-flex align-items-center justify-content-center text-light'><img src='<?= base64_encode('https://img.icons8.com/cute-clipart/64/000000/bug.png'); ?>' width='48' height='48'> Bugbounty</p></div></div><div class='wrapper p-4'><div class='text-center'><h1 class='text-bold text-danger'><?= $report->title(); ?></h1><p style='margin-top: 30px; font-style: italic; color: #404040;'>Created at: <span class='badge badge-pill badge-primary'><?= $report->date(); ?></span><br>Severity: <span class='badge badge-pill badge-primary'><?= $report->severity(); ?></span><br>Endoint: <span class='badge badge-pill badge-primary'><?= $report->endpoint(); ?></span><br>Program: <span class='badge badge-pill badge-primary'><?= $program->name(); ?></span></p></div><div style='margin-top: 30px; page-break-inside: avoid;' class='card sh-dark'><div class='card-header py-3'><h6 class='text-bold text-primary'>Impact</h6></div><div class='card-body'>" + impactHTML + "</div></div><div style='margin-top: 30px; page-break-inside: avoid;' class='card sh-dark'><div class='card-header py-3'><h6 class='text-bold text-primary'>Steps to reproduce</h6></div><div class='card-body'>" + stepstoreproduceHTML + "</div></div><div style='margin-top: 30px; page-break-inside: avoid;' class='card sh-dark'><div class='card-header py-3'><h6 class='text-bold text-primary'>Ressources</h6></div><div class='card-body'>" + ressourcesHTML + "</div></div><div style='margin-top: 30px; page-break-inside: avoid;' class='card sh-dark'><div class='card-header py-3'><h6 class='text-bold text-primary'>Mitigation</h6></div><div class='card-body'>" + mitigationHTML + "</div></div></div>";

        var opt = {
            margin:       0,
            filename:     'myfile.pdf',
            image:        { type: 'jpeg', quality: 1.05 },
            html2canvas:  { scale: 2 },
            pagebreak:    { mode: 'css' },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };

        // New Promise-based usage:
        html2pdf().set(opt).from(html).save();
    });

    $('#generateReportMarkdown').on('click', function() {
        impactMd = impact.getMarkdown();
        stepstoreproduceMd = stepstoreproduce.getMarkdown();
        ressourcesMd = ressources.getMarkdown();
        mitigationMd = mitigation.getMarkdown();
        var formData = {title:"<?= $report->title(); ?>", date:"<?= $report->date(); ?>", severity:"<?= $report->severity(); ?>", endpoint:"<?= $report->endpoint(); ?>", program:"<?= $program->id(); ?>",impact:impactMd,stepstoreproduce:stepstoreproduceMd,ressources:ressourcesMd,mitigation:mitigationMd}; 

        $.ajax({
            url: '<?= $routes->url('generateMarkdown') ?>',
            data: formData,                         
            type: 'POST',
            async: false,
            success: function(response, textStatus, jqXHR) {
                window.location.href = '<?= $asset; ?>markdown/'+response+'.md';
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    });
</script>

<?php
    $script = ob_get_clean();
?>