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
    $idPage = "reports";
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
            <a href="<?= $routes->url('createReport'); ?>" class="btn btn-info" style="height: max-content;">
                <?= $lang->getTxt($idPage, "add-report"); ?>
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-list-reports"); ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?= $lang->getTxt($idPage, "title-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "severity-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "endpoint-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "identifiant-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "date-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "status-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "action-table"); ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th><?= $lang->getTxt($idPage, "title-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "severity-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "endpoint-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "identifiant-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "date-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "status-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "action-table"); ?></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php foreach($reports as $report): ?>
                            <tr>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-danger">' . $report->title() . '</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-success">' . $report->severity() . '</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-warning">' . $report->endpoint() . '</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-info">' . $report->identifiant() . '</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-info">' . $report->date() . '</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-danger" data-toggle="modal" data-id="'.$report->id().'" data-target="#changeStatus">' . $report->status() . ' <i class="fas fa-pen text-light ml-2"></i></span>'; ?></td>
                                <td class="text-center">
                                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="<?= $routes->urlReplace("showReport",array($report->id())); ?>"><?= $lang->getTxt($idPage, "action-view"); ?></a>
                                        <a class="dropdown-item" href="<?= $routes->urlReplace("editReport",array($report->id())); ?>"><?= $lang->getTxt($idPage, "action-edit"); ?></a>
                                        <a class="dropdown-item" href="<?= $routes->urlReplace("deleteReport",array($report->id())); ?>"><?= $lang->getTxt($idPage, "action-delete"); ?></a>
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
    <div class="modal fade" id="changeStatus" tabindex="-1" role="dialog" aria-labelledby="#changeStatusLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeStatusLabel"><?= $lang->getTxt($idPage, "change-status"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" class="eventForm">
                    <div class="modal-body">
                        <div class="form-row selectformrow justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <select class="form-control <?php if(isset($_SESSION['inputResponseStatus']) && !empty($_SESSION['inputResponseStatus'])){ echo htmlspecialchars($_SESSION['inputResponseStatus'], ENT_QUOTES); } ?>" id="status" name="status">
                                    <option hidden >Status</option>
                                    <option value="accepted"><?= $lang->getTxt($idPage, "status-accepted"); ?></option>
                                    <option value="resolved"><?= $lang->getTxt($idPage, "status-resolved"); ?></option>
                                    <option value="NA"><?= $lang->getTxt($idPage, "status-NA"); ?></option>
                                    <option value="OOS"><?= $lang->getTxt($idPage, "status-OOS"); ?></option>
                                    <option value="informative"><?= $lang->getTxt($idPage, "status-informative"); ?></option>
                                </select>
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseStatus']) && !empty($_SESSION['inputResponseStatus']) && $_SESSION['inputResponseStatus'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseStatusMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseStatus'] = ''; $_SESSION['inputResponseStatusMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>

                        <!-- == Captcha and crsf token == -->
                        <input type="hidden" id="idReport" name="idReport">
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
            });
        });
    });

    $('#changeStatus').on('shown.bs.modal', function (e) {
        var button = $(e.relatedTarget);
        var idReport = button.data('id');
        var modal = $(this)
        modal.find('#idReport').val(idReport)
    })
</script>

<?php
    $script = ob_get_clean();
?>