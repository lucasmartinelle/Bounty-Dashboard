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
            <a href="<?= $routes->url('createReport'); ?>" class="btn btn-info" style="height: max-content;">
                <?= $lang->getTxt($idPage, "add-report"); ?>
            </a>
        </div>
        <a href="<?= $routes->url('changeWatchState'); ?>" class="btn btn-primary mb-3">
            <?php if(isset($_SESSION['watchState']) && !empty($_SESSION['watchState']) && $_SESSION['watchState'] == 'all'): ?>
                <?= $lang->getTxt($idPage, "watchmy"); ?>
            <?php elseif(isset($_SESSION['watchState']) && !empty($_SESSION['watchState']) && $_SESSION['watchState'] == 'me'): ?>
                <?= $lang->getTxt($idPage, "watchall"); ?>
            <?php endif; ?>
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-list-reports"); ?></h6>
                <h6 class="m-0"><span class="badge badge-pill badge-primary" data-toggle="modal" data-target="#filters"><?= $lang->getTxt($idPage, "filters"); ?> <i class="fas fa-sort-down ml-2"></i></span></h6>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?= $lang->getTxt($idPage, "title-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "severity-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "endpoint-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "gain-table"); ?></th>
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
                            <th><?= $lang->getTxt($idPage, "gain-table"); ?></th>
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
                                <td class="text-center"><?= '<span class="badge badge-pill badge-success" data-toggle="modal" data-id="'.$report->id().'" data-target="#changeGain">' . $report->gain() . ' €'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-info">' . $report->identifiant() . '</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-info">' . $report->date() . '</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-danger" data-toggle="modal" data-id="'.$report->id().'" data-target="#changeStatus">' . $report->status(); ?></td>
                                <td class="text-center">
                                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="<?= $routes->urlReplace("showReport",array($report->id())); ?>"><?= $lang->getTxt($idPage, "action-view"); ?></a>
                                        <a class="dropdown-item" href="<?= $routes->urlReplace("editReport",array($report->id())); ?>"><?= $lang->getTxt($idPage, "action-edit"); ?></a>
                                        <a class="dropdown-item" style="color: #3a3b45 !important;" data-toggle="modal" data-url="<?= $routes->urlReplace("deleteReport", array($report->id())); ?>" data-target="#confirmDelete" id="deleteReport"><?= $lang->getTxt($idPage, "action-delete"); ?></a>
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

    <div class="modal fade" id="filters" tabindex="-1" role="dialog" aria-labelledby="#filtersLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filtersLabel"><?= $lang->getTxt($idPage, "apply-filter"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" class="eventForm" action="<?= $routes->url("filterReports"); ?>">
                    <div class="modal-body">
                        <div class="form-row selectformrow justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <select class="form-control <?php if(isset($_SESSION['inputResponseProgram']) && !empty($_SESSION['inputResponseProgram'])){ echo htmlspecialchars($_SESSION['inputResponseProgram'], ENT_QUOTES); } ?>" id="program" name="program">
                                    <option hidden selected value=""><?= $lang->getTxt($idPage, "filter-program-header"); ?></option>
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
                        <div class="form-row selectformrow justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <select class="form-control <?php if(isset($_SESSION['inputResponsePlatform']) && !empty($_SESSION['inputResponsePlatform'])){ echo htmlspecialchars($_SESSION['inputResponsePlatform'], ENT_QUOTES); } ?>" id="platform" name="platform">
                                    <option hidden selected value=""><?= $lang->getTxt($idPage, "filter-platform-header"); ?></option>
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
                        <div class="form-row selectformrow justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <select class="form-control <?php if(isset($_SESSION['inputResponseStatus2']) && !empty($_SESSION['inputResponseStatus2'])){ echo htmlspecialchars($_SESSION['inputResponseStatus2'], ENT_QUOTES); } ?>" id="status2" name="status2">
                                    <option hidden selected value=""><?= $lang->getTxt($idPage, "filter-status-header"); ?></option>
                                    <option value="new"><?= $lang->getTxt($idPage, "status-new"); ?></option>
                                    <option value="accepted"><?= $lang->getTxt($idPage, "status-accepted"); ?></option>
                                    <option value="resolved"><?= $lang->getTxt($idPage, "status-resolved"); ?></option>
                                    <option value="NA"><?= $lang->getTxt($idPage, "status-NA"); ?></option>
                                    <option value="OOS"><?= $lang->getTxt($idPage, "status-OOS"); ?></option>
                                    <option value="informative"><?= $lang->getTxt($idPage, "status-informative"); ?></option>
                                </select>
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseStatus2']) && !empty($_SESSION['inputResponseStatus2']) && $_SESSION['inputResponseStatus2'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseStatusMessage2'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseStatus2'] = ''; $_SESSION['inputResponseStatusMessage2'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>
                        <div class="form-row justify-content-center">
                                <div class="col-md-10 mb-3 mt-2">
                                <input type="text" name="severitymin" id="severitymin" class="form-control <?php if(isset($_SESSION['inputResponseSeveritymin']) && !empty($_SESSION['inputResponseSeveritymin'])){ echo htmlspecialchars($_SESSION['inputResponseSeveritymin'], ENT_QUOTES); } ?>" placeholder="<?= $lang->getTxt($idPage, "severitymin-placeholder"); ?>" value="<?php if(isset($_SESSION['inputValueSeveritymin']) && !empty($_SESSION['inputValueSeveritymin'])){ echo htmlspecialchars($_SESSION['inputValueSeveritymin'], ENT_QUOTES); $_SESSION['inputValueSeveritymin'] = ''; } ?>">
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseSeveritymin']) && !empty($_SESSION['inputResponseSeveritymin']) && $_SESSION['inputResponseSeveritymin'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseSeverityminMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseSeveritymin'] = ''; $_SESSION['inputResponseSeverityminMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>
                        <div class="form-row justify-content-center">
                                <div class="col-md-10 mb-3 mt-2">
                                <input type="text" name="severitymax" id="severitymax" class="form-control <?php if(isset($_SESSION['inputResponseSeveritymax']) && !empty($_SESSION['inputResponseSeveritymax'])){ echo htmlspecialchars($_SESSION['inputResponseSeveritymax'], ENT_QUOTES); } ?>" placeholder="<?= $lang->getTxt($idPage, "severitymax-placeholder"); ?>" value="<?php if(isset($_SESSION['inputValueSeveritymax']) && !empty($_SESSION['inputValueSeveritymax'])){ echo htmlspecialchars($_SESSION['inputValueSeveritymax'], ENT_QUOTES); $_SESSION['inputValueSeveritymax'] = ''; } ?>">
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseSeveritymax']) && !empty($_SESSION['inputResponseSeveritymax']) && $_SESSION['inputResponseSeveritymax'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseSeveritymaxMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseSeveritymax'] = ''; $_SESSION['inputResponseSeveritymaxMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>
                    </div>

                    
                    <input type="hidden" id="g-recaptcha-response-2" name="g-recaptcha-response">
                    <input type="hidden" id="token" name="token" value="<?= $token ?>">

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->getTxt($idPage, "modal-nav-close"); ?></button>
                        <button type="submit" class="btn btn-primary"><?= $lang->getTxt($idPage, "modal-nav-confirm"); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteLabel"><?= $lang->getTxt($idPage, "delete-report"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p><?= $lang->getTxt($idPage, "confirmation-delete-report"); ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->getTxt($idPage, "modal-nav-close"); ?></button>
                    <a class="btn btn-primary" href="" id="deleteReportLink"><?= $lang->getTxt($idPage, "modal-nav-confirm"); ?></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="changeGain" tabindex="-1" role="dialog" aria-labelledby="#changeGainLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeGainLabel"><?= $lang->getTxt($idPage, "change-gain"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" class="eventForm" action="<?= $routes->url('gainReport'); ?>">
                    <div class="modal-body">
                        
                        <div class="form-row justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <input type="text" name="gain" id="gain" class="form-control <?php if(isset($_SESSION['inputResponseGain']) && !empty($_SESSION['inputResponseGain'])){ echo htmlspecialchars($_SESSION['inputResponseGain'], ENT_QUOTES); } ?>" placeholder="<?= $lang->getTxt($idPage, "gain-placeholder"); ?>" value="<?php if(isset($_SESSION['inputValueGain']) && !empty($_SESSION['inputValueGain'])){ echo htmlspecialchars($_SESSION['inputValueGain'], ENT_QUOTES); $_SESSION['inputValueGain'] = ''; } ?>">
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseGain']) && !empty($_SESSION['inputResponseGain']) && $_SESSION['inputResponseGain'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseGainMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseGain'] = ''; $_SESSION['inputResponseGainMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>
                        <!-- == Captcha and crsf token == -->
                        <input type="hidden" id="idReportGain" name="idReport">
                        <input type="hidden" id="g-recaptcha-response-3" name="g-recaptcha-response">
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

        <?php if($pubkey != null): ?>
            grecaptcha.ready(function() {
                grecaptcha.execute('<?= $pubkey ?>', {action: 'homepage'}).then(function(token) {
                    document.getElementById('g-recaptcha-response').value = token;
                    document.getElementById('g-recaptcha-response-2').value = token;
                    document.getElementById('g-recaptcha-response-3').value = token;
                });
            });
        <?php endif; ?>
    });

    $('#changeStatus').on('shown.bs.modal', function (e) {
        var button = $(e.relatedTarget);
        var idReport = button.data('id');
        var modal = $(this)
        modal.find('#idReport').val(idReport)
    });

    $('#confirmDelete').on('shown.bs.modal', function (e) {
        var button = $(e.relatedTarget);
        var url = button.data('url');
        var modal = $(this)
        modal.find('#deleteReportLink').attr('href', url)
    });

    $('#changeGain').on('shown.bs.modal', function (e) {
        var button = $(e.relatedTarget);
        var idReport = button.data('id');
        var modal = $(this)
        modal.find('#idReportGain').val(idReport)
    });
</script>

<?php
    $script = ob_get_clean();
?>