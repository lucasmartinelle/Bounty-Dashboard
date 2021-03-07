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
    $idPage = "invoices";
    if($filterMonth != 'none'){
        $dateObj   = DateTime::createFromFormat('!m', $filterMonth);
        $monthName = $dateObj->format('F');
    } else {
        $monthName = 'none';
    }

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

    <div class="container-fluid mb-4">
        <div class="row justify-content-between">
            <div>
                <h1 class="h3 mb-1 text-gray-800 mb-3"><?= $lang->getTxt($idPage, "header"); ?></h1>
                <span class="badge badge-pill badge-primary"><?= $lang->getTxt($idPage, "filter-platform-header"); ?><span class="badge badge-pill badge-light ml-2"><?= $filterInvoice ?></span></span>
                <span class="badge badge-pill badge-primary"><?= $lang->getTxt($idPage, "filter-month-header"); ?><span class="badge badge-pill badge-light ml-2"><?= $monthName; ?></span></span>
            </div>
            <button class="btn btn-info" style="height: max-content;" data-toggle="modal" data-target="#filters">
                <?= $lang->getTxt($idPage, "select-filters"); ?>
            </button>
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
                            <th><?= $lang->getTxt($idPage, "gain-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "identifiant-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "date-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "status-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "select-table"); ?></th>
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
                            <th><?= $lang->getTxt($idPage, "select-table"); ?></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php foreach($reports as $report): ?>
                            <tr>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-danger">' . $report->title() . '</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-success">' . $report->severity() . '</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-warning">' . $report->endpoint() . '</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-success">' . $report->gain() . ' €</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-info">' . $report->identifiant() . '</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-info">' . $report->date() . '</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-danger">' . $report->status() . '</span>'; ?></td>
                                <td>
                                    <div class="form-check text-center">
                                        <input class="form-check-input position-static" type="checkbox" data-title='<?= $report->title(); ?>' data-date='<?= $report->date(); ?>' data-gain='<?= $report->gain(); ?>' data-identifiant='<?= $report->identifiant(); ?>'>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="container text-center" style="margin-top: 60px !important;">
        <button id="generateInvoice" type="button" class="btn btn-info" style="width: 40%; height: 50px;"><?= $lang->getTxt($idPage, "generate-invoice"); ?></button>
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
                <form method="post" class="eventForm">
                    <div class="modal-body">
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
                                <select class="form-control <?php if(isset($_SESSION['inputResponseMonth']) && !empty($_SESSION['inputResponseMonth'])){ echo htmlspecialchars($_SESSION['inputResponseMonth'], ENT_QUOTES); } ?>" id="month" name="month">
                                    <option hidden selected value=""><?= $lang->getTxt($idPage, "filter-month-header"); ?></option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseMonth']) && !empty($_SESSION['inputResponseMonth']) && $_SESSION['inputResponseMonth'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseMonthMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseMonth'] = ''; $_SESSION['inputResponseMonthMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>
                    </div>

                    
                    <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
                    <input type="hidden" id="token" name="token" value="<?= $token ?>">

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
<script src="<?= $asset ?>dist/html2pdf.bundle.min.js"></script>

<script type="text/javascript">
    var reportsSelected = [];

    $(function () {
        $('[data-toggle="popover"]').popover()

        <?php if($filterMonth == 'none' || $filterInvoice == 'none'): ?>
            $('#filters').modal('show');
        <?php endif; ?>
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
                });
            });
        <?php endif; ?>
    });

    $('input[type="checkbox"]').change(function() {
        var info = [$(this).data('title'), $(this).data('date'), $(this).data('gain'), $(this).data('identifiant')]
        if(this.checked) {
            reportsSelected.push(info);
        } else {
            if($.inArray(info, reportsSelected)){
                reportsSelected.splice(reportsSelected.indexOf(info), 1);
            }
        }
    });

    $('#generateInvoice').click(function(){
        var html = '';
        var title = 'invoice';
        var formData = {reports: reportsSelected, month: '<?= $filterMonth; ?>', platform: '<?= $filterInvoice; ?>'};
        $.ajax({
            url: '<?= $routes->url('generateInvoice') ?>',
            data: formData,                         
            type: 'POST',
            async: false,
            success: function(response, textStatus, jqXHR) {
                var json = JSON.parse(response);
                html = json.html;
                title += json.title;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });

        if(html != 'nofilters' && html != 'none'){
            var opt = {
                margin:       [0.5,1,0.5,1],
                filename:     title + '.pdf',
                image:        { type: 'jpeg', quality: 1.05 },
                html2canvas:  { scale: 2 },
                pagebreak:    { mode: 'css' },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            // New Promise-based usage:
            html2pdf().set(opt).from(html).save();
        } else if (html == "none") {
            alert('Select some reports');
        } else {
            $('#filters').modal('show');
        }
    });
</script>

<?php
    $script = ob_get_clean();
?>