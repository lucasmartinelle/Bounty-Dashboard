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
                        <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-earning-per-month"); ?></h6>
                    </div>
                    <div class="card-body" style="width: 100%;">
                        <canvas id="earningpermonth" width="100%" height="300"></canvas>
                    </div>
                </div>
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

<?php 
    $content = ob_get_clean();
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
<script type="text/javascript">
    $(function () {
        $('[data-toggle="popover"]').popover()
    })

    <?php if($pubkey != null): ?>
        grecaptcha.ready(function() {
            grecaptcha.execute('<?= $pubkey ?>', {action: 'homepage'}).then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
                document.getElementById('g-recaptcha-response-2').value = token;
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