<?php
    // Load routing controller
    require_once("app/Routes.php");
    require_once("app/languages/languageManager.php");

    use app\Routes;
    use app\languages\languageManager;
    
    $routes = new Routes;
    $lang = new languageManager;

    $asset = "assets/";
    $idPage = "dashboard";

    require_once("models/captchaHandler.php");
    use Models\CaptchaHandler;
    $this->_captchaHandler = new CaptchaHandler;
    $pubkey = $this->_captchaHandler->getPubKey();
    ob_start();
?>

<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800"><?= $lang->getTxt($idPage, 'content-header'); ?></h1>

<div class="container-fluid">
    <!-- Content Row -->
    <div class="row">

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                <?= $lang->getTxt($idPage, 'card-bug-openned'); ?></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $new ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-lock-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <?= $lang->getTxt($idPage, 'card-bug-accepted-fixed'); ?></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $other ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-lock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <?= $lang->getTxt($idPage, 'card-total-earning'); ?></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $gain ?> <?php if($lang->getLang() == "FR"){ echo '€'; } else { echo '$'; } ?></div>
                        </div>
                        <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                <?= $lang->getTxt($idPage, 'card-critical-bugs-found'); ?></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $critical ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bug fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <div class="d-flex justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-bug-per-month"); ?></h6>
                            <h6 class="m-0"><span class="badge badge-pill badge-primary" data-toggle="modal" data-target="#filtersMonth"><?= $lang->getTxt($idPage, "filters"); ?> <i class="fas fa-sort-down ml-2"></i></span></h6>
                        </div>
                    </div>
                    <div class="card-body" style="width: 100%;">
                        <canvas id="bugPerMonth" width="100%" height="300px;"></canvas>
                    </div>
                    <hr>
                    <div class="container-fluid pb-3">
                        <span class="badge badge-pill badge-primary"><?= $lang->getTxt($idPage, "platforms"); ?><span class="badge badge-pill badge-light ml-2">
                            <?php if(isset($informationFilterPlatform2) && !empty(htmlspecialchars($informationFilterPlatform2, ENT_QUOTES))){ 
                                echo htmlspecialchars($informationFilterPlatform2, ENT_QUOTES); 
                            } else { 
                                echo 'all'; 
                            } ?>
                        </span></span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <div class="d-flex justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-bug-by-severity"); ?></h6>
                            <h6 class="m-0"><span class="badge badge-pill badge-primary" data-toggle="modal" data-target="#filtersSeverity"><?= $lang->getTxt($idPage, "filters"); ?> <i class="fas fa-sort-down ml-2"></i></span></h6>
                        </div>
                    </div>
                    <div class="card-body" style="width: 100%;">
                        <canvas id="bugBySeverity" width="100%" height="300px;"></canvas>
                    </div>
                    <hr>
                    <div class="container-fluid pb-3">
                        <span class="badge badge-pill badge-primary"><?= $lang->getTxt($idPage, "platforms"); ?><span class="badge badge-pill badge-light ml-2">
                            <?php if(isset($informationFilterPlatforms) && !empty(htmlspecialchars($informationFilterPlatforms, ENT_QUOTES))){ 
                                echo htmlspecialchars($informationFilterPlatforms, ENT_QUOTES); 
                            } else { 
                                echo 'all'; 
                            } ?>
                        </span></span>
                        <span class="badge badge-pill badge-primary"><?= $lang->getTxt($idPage, "programs"); ?><span class="badge badge-pill badge-light ml-2">
                            <?php if(isset($informationFilterPrograms) && !empty(htmlspecialchars($informationFilterPrograms, ENT_QUOTES))){ 
                                echo htmlspecialchars($informationFilterPrograms, ENT_QUOTES); 
                            } else { 
                                echo 'all'; 
                            } ?>
                        </span></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-bug-by-platform"); ?></h6>
                    </div>
                    <div class="card-body" style="width: 100%;">
                        <canvas id="bugByPlatform" width="100%" height="300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="filtersSeverity" tabindex="-1" role="dialog" aria-labelledby="#filtersSeverityLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filtersSeverityLabel"><?= $lang->getTxt($idPage, "apply-filter"); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" class="eventForm">
                <div class="modal-body">
                    <div class="form-row selectformrow justify-content-center">
                        <div class="col-md-10 mb-3 mt-2">
                            <label for="program">Program</label>
                            <select class="form-control <?php if(isset($_SESSION['inputResponseProgram']) && !empty($_SESSION['inputResponseProgram'])){ echo htmlspecialchars($_SESSION['inputResponseProgram'], ENT_QUOTES); } ?>" id="program" name="program">
                                <option hidden selected value=""><?= $lang->getTxt($idPage, "filter-program-header"); ?></option>
                                <?php foreach($programsFilter as $program): ?>
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
                            <label for="platform">Platform</label>
                            <select class="form-control <?php if(isset($_SESSION['inputResponsePlatform']) && !empty($_SESSION['inputResponsePlatform'])){ echo htmlspecialchars($_SESSION['inputResponsePlatform'], ENT_QUOTES); } ?>" id="platform" name="platform">
                                <option hidden selected value=""><?= $lang->getTxt($idPage, "filter-platform-header"); ?></option>
                                <?php foreach($platformsFilter as $platform): ?>
                                    <option value="<?= $platform->id(); ?>"><?= $platform->name(); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <!-- == If validation failed == -->
                            <?php if(isset($_SESSION['inputResponsePlatform']) && !empty($_SESSION['inputResponsePlatform']) && $_SESSION['inputResponsePlatform'] == 'invalid'): ?>
                                <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponsePlatformMessage'], ENT_QUOTES); ?>"></i></span>
                            <?php endif; $_SESSION['inputResponsePlatform'] = ''; $_SESSION['inputResponsePlatformMessage'] = ''; ?> <!-- End of validation failed -->
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

<div class="modal fade" id="filtersMonth" tabindex="-1" role="dialog" aria-labelledby="#filtersMonthLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filtersMonthLabel"><?= $lang->getTxt($idPage, "apply-filter"); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" class="eventForm">
                <div class="modal-body">
                    <div class="form-row selectformrow justify-content-center">
                        <div class="col-md-10 mb-3 mt-2">
                            <label for="program">Platform</label>
                            <select class="form-control <?php if(isset($_SESSION['inputResponsePlatform2']) && !empty($_SESSION['inputResponsePlatform2'])){ echo htmlspecialchars($_SESSION['inputResponsePlatform2'], ENT_QUOTES); } ?>" id="platform2" name="platform2">
                                <option hidden selected value=""><?= $lang->getTxt($idPage, "filter-platform-header"); ?></option>
                                <?php foreach($platformsFilter as $platform): ?>
                                    <option value="<?= $platform->id(); ?>"><?= $platform->name(); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <!-- == If validation failed == -->
                            <?php if(isset($_SESSION['inputResponsePlatform2']) && !empty($_SESSION['inputResponsePlatform2']) && $_SESSION['inputResponsePlatform2'] == 'invalid'): ?>
                                <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponsePlatformMessage2'], ENT_QUOTES); ?>"></i></span>
                            <?php endif; $_SESSION['inputResponsePlatform2'] = ''; $_SESSION['inputResponsePlatformMessage2'] = ''; ?> <!-- End of validation failed -->
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

<?php 
    $content = ob_get_clean(); 
    ob_start();
?>

<script src="<?= $asset ?>dist/chart.js/Chart.min.js"></script>

<?php 
    $keysPlatforms = '';
    $valuesPlatforms = '';
    foreach($platforms as $key=>$value){
        $keysPlatforms .= "'" . $key . "',";
        $valuesPlatforms .= "'" . $value . "',";
    }
    $keysPlatforms = substr($keysPlatforms, 0,-1);
    $valuesPlatforms = substr($valuesPlatforms, 0, -1);

    $keysSeverity = '';
    $valuesSeverity = '';
    foreach($severity as $key=>$value){
        $keysSeverity .= "'" . $key . "',";
        $valuesSeverity .= "'" . $value . "',";
    }
    $keysSeverity = substr($keysSeverity, 0,-1);
    $valuesSeverity = substr($valuesSeverity, 0, -1);

    $keysDate = '';
    $valuesDate = '';
    foreach($dates as $key=>$value){
        $keysDate .= "'" . $key . "',";
        $valuesDate .= "'" . $value . "',";
    }
    $keysDate = substr($keysDate, 0,-1);
    $valuesDate = substr($valuesDate, 0, -1);
?>

<script type="text/javascript">
    $(function () {
        $('[data-toggle="popover"]').popover()

        Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#858796';

        var ctxPlatform = document.getElementById("bugByPlatform");
        var bugByPlatform = new Chart(ctxPlatform, {
            type: 'doughnut',
            data: {
                labels: [<?= $keysPlatforms ?>],
                datasets: [{
                data: [<?= $valuesPlatforms ?>],
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

        var ctxDate = document.getElementById("bugPerMonth");
        var bugPerMonth = new Chart(ctxDate, {
            type: 'doughnut',
            data: {
                labels: [<?= $keysDate ?>],
                datasets: [{
                data: [<?= $valuesDate ?>],
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

        <?php if($pubkey != null): ?>
            grecaptcha.ready(function() {
                grecaptcha.execute('<?= $pubkey ?>', {action: 'homepage'}).then(function(token) {
                    document.getElementById('g-recaptcha-response').value = token;
                    document.getElementById('g-recaptcha-response-2').value = token;
                });
            });
        <?php endif; ?>
    });

</script>

<?php
    $script = ob_get_clean();
?>