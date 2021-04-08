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
            <div>
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#createPlatform" style="height: max-content;">
                    <?= $lang->getTxt($idPage, "add-platform"); ?>
                </button>
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deletePlatform" style="height: max-content;">
                    <?= $lang->getTxt($idPage, "delete-platform"); ?>
                </button>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-bug-by-severity"); ?></h6>
                    </div>
                    <div class="card-body" style="width: 100%;">
                        <canvas id="bugBySeverity" width="100%" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <div class="d-flex justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-earning-per-month"); ?></h6>
                            <h6 class="m-0"><span class="badge badge-pill badge-primary" data-toggle="modal" data-target="#filterEarningPerMonth"><?= $lang->getTxt($idPage, "filters"); ?> <i class="fas fa-sort-down ml-2"></i></span></h6>
                        </div>
                    </div>
                    <div class="card-body" style="width: 100%;">
                        <canvas id="earningpermonth" width="100%" height="300"></canvas>
                    </div>
                    <hr>
                    <div class="container-fluid pb-3">
                        <span class="badge badge-pill badge-primary"><?= $lang->getTxt($idPage, "filter-year"); ?><span class="badge badge-pill badge-light ml-2">
                            <?php if(isset($informationFilterYear) && !empty(htmlspecialchars($informationFilterYear, ENT_QUOTES))){ 
                                echo htmlspecialchars($informationFilterYear, ENT_QUOTES); 
                            } else { 
                                echo 'all'; 
                            } ?>
                        </span></span>
                    </div>
                </div>
            </div>
        </div>
    </div> 

    

    <div class="card card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-list-platforms"); ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?= $lang->getTxt($idPage, "name-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "client-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "btw-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "address-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "email-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "date-table"); ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th><?= $lang->getTxt($idPage, "name-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "client-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "btw-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "address-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "email-table"); ?></th>
                            <th><?= $lang->getTxt($idPage, "date-table"); ?></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php foreach($platforms as $platform):
                            $client = (empty($platform->client()) or $platform->client() == NULL) ? '-' : $platform->client();
                            $BTW = (empty($platform->BTW()) or $platform->BTW() == NULL) ? '-' : $platform->BTW();
                            $address = (empty($platform->address()) or $platform->address() == NULL) ? '-' : $platform->address();
                            $email = (empty($platform->email()) or $platform->email() == NULL) ? '-' : $platform->email();
                            $date = (empty($platform->date()) or $platform->date() == NULL) ? '-' : $platform->date();
                            ?>
                            <tr>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-danger">' . $platform->name() . '</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-info">' . $client . '</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-danger">' . $BTW. '</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-success">' . $address . '</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-info">' . $email . '</span>'; ?></td>
                                <td class="text-center"><?= '<span class="badge badge-pill badge-warning">' . $date . '</span>'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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
                                <input type="text" name="name" class="form-control <?php if(isset($_SESSION['inputResponseName']) && !empty($_SESSION['inputResponseName'])){ echo htmlspecialchars($_SESSION['inputResponseName'], ENT_QUOTES); } ?>" value="<?php if(isset($_SESSION['inputValueName']) && !empty($_SESSION['inputValueName'])){ echo htmlspecialchars($_SESSION['inputValueName'], ENT_QUOTES); $_SESSION['inputValueName'] = ''; } ?>" id="name" placeholder="<?= $lang->getTxt($idPage, "name-placeholder"); ?>">
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseName']) && !empty($_SESSION['inputResponseName']) && $_SESSION['inputResponseName'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseNameMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseName'] = ''; $_SESSION['inputResponseNameMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>

                        <div class="form-row justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <input type="text" name="client" class="form-control <?php if(isset($_SESSION['inputResponseClient']) && !empty($_SESSION['inputResponseClient'])){ echo htmlspecialchars($_SESSION['inputResponseClient'], ENT_QUOTES); } ?>" value="<?php if(isset($_SESSION['inputValueClient']) && !empty($_SESSION['inputValueClient'])){ echo htmlspecialchars($_SESSION['inputValueClient'], ENT_QUOTES); $_SESSION['inputValueClient'] = ''; } ?>" id="client" placeholder="<?= $lang->getTxt($idPage, "client-placeholder"); ?>">
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseClient']) && !empty($_SESSION['inputResponseClient']) && $_SESSION['inputResponseClient'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseClientMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseClient'] = ''; $_SESSION['inputResponseClientMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>

                        <div class="form-row justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <input type="text" name="BTW" class="form-control <?php if(isset($_SESSION['inputResponseBTW']) && !empty($_SESSION['inputResponseBTW'])){ echo htmlspecialchars($_SESSION['inputResponseBTW'], ENT_QUOTES); } ?>" value="<?php if(isset($_SESSION['inputValueBTW']) && !empty($_SESSION['inputValueBTW'])){ echo htmlspecialchars($_SESSION['inputValueBTW'], ENT_QUOTES); $_SESSION['inputValueBTW'] = ''; } ?>" id="BTW" placeholder="<?= $lang->getTxt($idPage, "BTW-placeholder"); ?>">
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseBTW']) && !empty($_SESSION['inputResponseBTW']) && $_SESSION['inputResponseBTW'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseBTWMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseBTW'] = ''; $_SESSION['inputResponseBTWMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>

                        <div class="form-row justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <input type="text" name="address" class="form-control <?php if(isset($_SESSION['inputResponseAddress']) && !empty($_SESSION['inputResponseAddress'])){ echo htmlspecialchars($_SESSION['inputResponseAddress'], ENT_QUOTES); } ?>" value="<?php if(isset($_SESSION['inputValueAddress']) && !empty($_SESSION['inputValueAddress'])){ echo htmlspecialchars($_SESSION['inputValueAddress'], ENT_QUOTES); $_SESSION['inputValueAddress'] = ''; } ?>" id="address" placeholder="<?= $lang->getTxt($idPage, "address-placeholder"); ?>">
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseAddress']) && !empty($_SESSION['inputResponseAddress']) && $_SESSION['inputResponseAddress'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseAddressMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseAddress'] = ''; $_SESSION['inputResponseAddressMessage'] = ''; ?> <!-- End of validation failed -->
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
                                <input type="text" name="date" data-toggle="datepicker" class="form-control <?php if(isset($_SESSION['inputResponseDate']) && !empty($_SESSION['inputResponseDate'])){ echo htmlspecialchars($_SESSION['inputResponseDate'], ENT_QUOTES); } ?>" value="<?php if(isset($_SESSION['inputValueDate']) && !empty($_SESSION['inputValueDate'])){ echo htmlspecialchars($_SESSION['inputValueDate'], ENT_QUOTES); $_SESSION['inputValueDate'] = ''; } ?>" id="date" placeholder="<?= $lang->getTxt($idPage, "date-placeholder"); ?>">
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseDate']) && !empty($_SESSION['inputResponseDate']) && $_SESSION['inputResponseDate'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseDateMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseDate'] = ''; $_SESSION['inputResponseDateMessage'] = ''; ?> <!-- End of validation failed -->
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

    <div class="modal fade" id="deletePlatform" tabindex="-1" role="dialog" aria-labelledby="deletePlatformLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePlatformLabel"><?= $lang->getTxt($idPage, "delete-platform"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="<?= $routes->url('deletePlatform'); ?>">
                    <div class="modal-body">
                        <div class="form-row selectformrow justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <select class="form-control <?php if(isset($_SESSION['inputResponsePlatform']) && !empty($_SESSION['inputResponsePlatform'])){ echo htmlspecialchars($_SESSION['inputResponsePlatform'], ENT_QUOTES); } ?>" id="platform" name="platform">
                                    <option hidden selected value=""><?= $lang->getTxt($idPage, "header"); ?></option>
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
                        <!-- == Captcha and crsf token == -->
                        <input type="hidden" id="g-recaptcha-response-2" name="g-recaptcha-response">
                        <input type="hidden" id="token" name="token" value="<?= $token ?>">
                        <!-- End Captcha and crsf token -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->getTxt($idPage, "modal-nav-close"); ?></button>
                        <button type="submit" class="btn btn-primary"><?= $lang->getTxt($idPage, "modal-nav-confirm"); ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="filterEarningPerMonth" tabindex="-1" role="dialog" aria-labelledby="#filterEarningPerMonthLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterEarningPerMonthLabel"><?= $lang->getTxt($idPage, "apply-filter"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" class="eventForm" action="<?= $routes->url("filterEarningPerMonth");?>">
                    <div class="modal-body">
                        <div class="form-row selectformrow justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <label for="year"><?= $lang->getTxt($idPage, "filter-year"); ?></label>
                                <select class="form-control <?php if(isset($_SESSION['inputResponseYear']) && !empty($_SESSION['inputResponseYear'])){ echo htmlspecialchars($_SESSION['inputResponseYear'], ENT_QUOTES); } ?>" id="year" name="year">
                                    <option hidden selected value="all">all</option>
                                    <?php for($i=2020; $i <= (int) date('Y'); $i++): ?>
                                        <option value="<?= $i; ?>"><?= $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseYear']) && !empty($_SESSION['inputResponseYear']) && $_SESSION['inputResponseYear'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseYearMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseYear'] = ''; $_SESSION['inputResponseYearMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>
                    </div>

                    
                    <input type="hidden" id="g-recaptcha-response-3" name="g-recaptcha-response">
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
<link href="<?= $asset ?>dist/datepicker.css" rel="stylesheet">

<?php
    $css = ob_get_clean();
    ob_start();

    if($_COOKIE['lang'] == 'FR'){
        $jan = 'Janvier';
        $fev = 'Février';
        $mar = 'Mars';
        $apr = 'Avril';
        $may = 'Mai';
        $jun = 'Juin';
        $jul = 'Juillet';
        $aug = 'Août';
        $sep = 'Septembre';
        $oct = 'Octobre';
        $nov = 'Novembre';
        $dec = 'Décembre';
    } else {
        $jan = 'January';
        $fev = 'February';
        $mar = 'March';
        $apr = 'April';
        $may = 'May';
        $jun = 'June';
        $jul = 'July';
        $aug = 'August';
        $sep = 'September';
        $oct = 'October';
        $nov = 'November';
        $dec = 'December';
    }

    $values = '';
    foreach($earningpermonth as $key=>$value){
        $values .= "'" . $value . "',";
    }
    $values = substr($values, 0, -1);

    $keysSeverity = '';
    $valuesSeverity = '';
    foreach($severity as $key=>$value){
        $keysSeverity .= "'" . $key . "',";
        $valuesSeverity .= "'" . $value . "',";
    }
    $keysSeverity = substr($keysSeverity, 0,-1);
    $valuesSeverity = substr($valuesSeverity, 0, -1);
?>

<script src="<?= $asset ?>dist/chart.js/Chart.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<script src="<?= $asset ?>dist/datepicker.js"></script>
<script type="text/javascript">
    $(function () {
        $('[data-toggle="popover"]').popover();
        $('[data-toggle="datepicker"]').datepicker();

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

    <?php if($pubkey != null): ?>
        grecaptcha.ready(function() {
            grecaptcha.execute('<?= $pubkey ?>', {action: 'homepage'}).then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
                document.getElementById('g-recaptcha-response-2').value = token;
                document.getElementById('g-recaptcha-response-3').value = token;
            });
        });
    <?php endif; ?>

    var ctx = document.getElementById('earningpermonth');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['<?= $jan ?>', '<?= $fev ?>', '<?= $mar ?>', '<?= $apr ?>', '<?= $may ?>', '<?= $jun ?>', '<?= $jul ?>', '<?= $aug ?>', '<?= $sep ?>', '<?= $oct ?>', '<?= $nov ?>', '<?= $dec ?>'],
            datasets: [{
                data: [<?= $values ?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(165, 105, 189 , 0.2)',
                    'rgba(120, 40, 31, 0.2)',
                    'rgba(40, 55, 71, 0.2)',
                    'rgba(22, 160, 133 , 0.2)',
                    'rgba(93, 173, 226, 0.2)',
                    'rgba(108, 52, 131 , 0.2)',
                    'rgba(220, 118, 51, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(165, 105, 189 , 1)',
                    'rgba(120, 40, 31, 1)',
                    'rgba(40, 55, 71, 1)',
                    'rgba(22, 160, 133 , 1)',
                    'rgba(93, 173, 226, 1)',
                    'rgba(108, 52, 131 , 1)',
                    'rgba(220, 118, 51, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            legend: {
                display: false,
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
    ctx.height = 300;

    var ctxSeverity = document.getElementById("bugBySeverity");
    var bugBySeverity = new Chart(ctxSeverity, {
        type: 'doughnut',
        data: {
            labels: [<?= $keysSeverity ?>],
            datasets: [{
            data: [<?= $valuesSeverity ?>],
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#a4f542', '#ed3245', '#edbb45', '#a064fa' , '#45f1f7'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#95de3c', '#bd2232', '#d4a944', '#8f48fa', '#3dcfd4'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
            },
            legend: {
                display: true,
                position: 'bottom',
            },
            cutoutPercentage: 80,
        },
    });
</script>

<?php
    $script = ob_get_clean();
?>