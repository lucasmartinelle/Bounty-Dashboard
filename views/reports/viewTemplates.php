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
    $idPage = "templates";
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
            <a href="<?= $routes->url('createTemplate'); ?>" class="btn btn-info" style="height: max-content;">
                <?= $lang->getTxt($idPage, "add-template"); ?>
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-list-templates"); ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?= $lang->getTxt($idPage, "title-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "description-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "endpoint-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "severity-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "action-table"); ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th><?= $lang->getTxt($idPage, "title-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "description-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "endpoint-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "severity-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "action-table"); ?></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php foreach($templates as $template): ?>
                            <tr>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-danger">' . $template->title() . '</span>'; ?></td>
                                <td class="text-center"><?= $template->description(); ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-warning">' . $template->endpoint() . '</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-success">' . $template->severity() . '</span>'; ?></td>
                                <td class="text-center">
                                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="<?= $routes->urlReplace("showTemplate",array($template->id())); ?>"><?= $lang->getTxt($idPage, "action-view"); ?></a>
                                        <a class="dropdown-item" href="<?= $routes->urlReplace("editTemplate",array($template->id())); ?>"><?= $lang->getTxt($idPage, "action-edit"); ?></a>
                                        <a class="dropdown-item" style="color: #3a3b45 !important;" data-toggle="modal" data-url="<?= $routes->urlReplace("deleteTemplate", array($template->id())); ?>" data-target="#confirmDelete" id="deleteTemplate"><?= $lang->getTxt($idPage, "action-delete"); ?></a>
                                    </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteLabel"><?= $lang->getTxt($idPage, "delete-template"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p><?= $lang->getTxt($idPage, "confirmation-delete-template"); ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->getTxt($idPage, "modal-nav-close"); ?></button>
                    <a class="btn btn-primary" href="" id="deleteTemplateLink"><?= $lang->getTxt($idPage, "modal-nav-confirm"); ?></a>
                </div>
            </div>
        </div>
    </div>
<?php 
    $content = ob_get_clean();
    ob_start();
?>

<link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
<link href="<?= $asset ?>dist/datepicker.css" rel="stylesheet">

<?php
    $css = ob_get_clean();
    ob_start();
?>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" 
crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>

<script type="text/javascript">
    $(function () {
        $('[data-toggle="popover"]').popover()
    });

    $( document ).ready(function() {
        <?php if(htmlspecialchars($_COOKIE['lang']) == 'EN'): ?>
            $('#dataTable').DataTable();
        <?php else: ?>
            $('#dataTable').DataTable( {
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
                }
            });
        <?php endif; ?>

        grecaptcha.ready(function() {
            grecaptcha.execute('<?php echo SITE_KEY; ?>', {action: 'homepage'}).then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
                document.getElementById('g-recaptcha-response-2').value = token;
                document.getElementById('g-recaptcha-response-3').value = token;
            });
        });
    });

    $('#confirmDelete').on('shown.bs.modal', function (e) {
        var button = $(e.relatedTarget);
        var url = button.data('url');
        var modal = $(this)
        modal.find('#deleteTemplateLink').attr('href', url)
    });
</script>

<?php
    $script = ob_get_clean();
?>