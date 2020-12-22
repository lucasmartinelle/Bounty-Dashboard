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
    $idPage = "programs";
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
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#createProgram" style="height: max-content;">
                <?= $lang->getTxt($idPage, "add-program"); ?>
            </button>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><?= $lang->getTxt($idPage, "header-bug-by-severity"); ?></h6>
                    </div>
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
    </div> 

    <!-- Modal -->
    <div class="modal fade" id="createProgram" tabindex="-1" role="dialog" aria-labelledby="#createProgramLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createProgramLabel"><?= $lang->getTxt($idPage, "add-program"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" class="eventForm">
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
                        <div class="form-row addformrow justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <div class="input-group">
                                    <input type="text" class="form-control <?php if(isset($_SESSION['inputResponseScope']) && !empty($_SESSION['inputResponseScope'])){ echo htmlspecialchars($_SESSION['inputResponseScope'], ENT_QUOTES); } ?>" id="scope" placeholder="<?= $lang->getTxt($idPage, "scope-placeholder"); ?>" aria-describedby="addbutton">
                                    <div class="input-group-prepend">
                                        <button type="button" class="input-group-text bg-primary" id="addbutton" style="border: 0;">
                                            <span class="badge badge-light">+</span>
                                        </button>
                                    </div>
                                    <input type="hidden" id="scopehide" name="scope" value="<?php if(isset($_SESSION['inputValueScope']) && !empty($_SESSION['inputValueScope'])){ echo htmlspecialchars($_SESSION['inputValueScope'], ENT_QUOTES); $_SESSION['inputValueScope'] = ''; } ?>"></input>
                                </div>
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseScope']) && !empty($_SESSION['inputResponseScope']) && $_SESSION['inputResponseScope'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseScopeMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseScope'] = ''; $_SESSION['inputResponseScopeMessage'] = ''; ?> <!-- End of validation failed -->
                                <span id="scopetags"></span>
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
                        <div class="form-row selectformrow justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <select class="form-control <?php if(isset($_SESSION['inputResponseStatus']) && !empty($_SESSION['inputResponseStatus'])){ echo htmlspecialchars($_SESSION['inputResponseStatus'], ENT_QUOTES); } ?>" id="status" name="status">
                                    <option hidden >Status</option>
                                    <option value="open"><?= $lang->getTxt($idPage, "status-open"); ?></option>
                                    <option value="close"><?= $lang->getTxt($idPage, "status-close"); ?></option>
                                </select>
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseStatus']) && !empty($_SESSION['inputResponseStatus']) && $_SESSION['inputResponseStatus'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseStatusMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseStatus'] = ''; $_SESSION['inputResponseStatusMessage'] = ''; ?> <!-- End of validation failed -->
                            </div>
                        </div>
                        <div class="form-row addformrow justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <div class="input-group">
                                    <input type="text" class="form-control <?php if(isset($_SESSION['inputResponseTags']) && !empty($_SESSION['inputResponseTags'])){ echo htmlspecialchars($_SESSION['inputResponseTags'], ENT_QUOTES); } ?>" id="tags" placeholder="<?= $lang->getTxt($idPage, "tags-placeholder"); ?>" aria-describedby="addbuttontags">
                                    <div class="input-group-prepend">
                                        <button type="button" class="input-group-text bg-primary" id="addbuttontags" style="border: 0;">
                                            <span class="badge badge-light">+</span>
                                        </button>
                                    </div>
                                    <input type="hidden" id="tagshide" name="tags" value="<?php if(isset($_SESSION['inputValueTags']) && !empty($_SESSION['inputValueTags'])){ echo htmlspecialchars($_SESSION['inputValueTags'], ENT_QUOTES); $_SESSION['inputValueTags'] = ''; } ?>"></input>
                                </div>
                                <!-- == If validation failed == -->
                                <?php if(isset($_SESSION['inputResponseTags']) && !empty($_SESSION['inputResponseTags']) && $_SESSION['inputResponseTags'] == 'invalid'): ?>
                                    <span><i class="fas fa-info-circle text-danger" tabindex="0" data-html=true data-toggle="popover" data-trigger="hover" title="<span class='text-danger' style='font-size: 18px; font-weight: 500;'><?= $lang->getTxt($idPage, "invalid-input"); ?></span>" data-content="<?= htmlspecialchars($_SESSION['inputResponseTagsMessage'], ENT_QUOTES); ?>"></i></span>
                                <?php endif; $_SESSION['inputResponseTags'] = ''; $_SESSION['inputResponseTagsMessage'] = ''; ?> <!-- End of validation failed -->
                                <span id="tagstags"></span>
                            </div>
                        </div>
                        <div class="form-row selectformrow justify-content-center">
                            <div class="col-md-10 mb-3 mt-2">
                                <select class="form-control <?php if(isset($_SESSION['inputResponsePlatform']) && !empty($_SESSION['inputResponsePlatform'])){ echo htmlspecialchars($_SESSION['inputResponsePlatform'], ENT_QUOTES); } ?>" id="platform" name="platform">
                                    <option hidden ><?= $lang->getTxt($idPage, "platform"); ?></option>
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

<link href="<?= $asset ?>dist/datepicker.css" rel="stylesheet">
<?php
    $css = ob_get_clean();
    ob_start();
?>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" 
crossorigin="anonymous"></script>
<script src="<?= $asset ?>dist/datepicker.js"></script>

<script type="text/javascript">
    $(function () {
        $('[data-toggle="popover"]').popover()
    });

    $( document ).ready(function() {
        var scope = $("#scopehide").val();
        var arr = scope.split('|');
        $.each( arr, function( index, value ) {
            $('#scopetags').append('<span class="badge badge-pill badge-success m-r-xs">'+value+'</span>');
        });

        var tags = $("#tagshide").val();
        var arr = tags.split('|');
        $.each( arr, function( index, value ) {
            $('#tagstags').append('<span class="badge badge-pill badge-success m-r-xs">'+value+'</span>');
        });
    });

    $('#scope').autocomplete({
        source: '<?= $routes->url("scope"); ?>',
        minLength: 0,
        blankItem: true
    }).focus(function(){            
        $(this).data("uiAutocomplete").search($(this).val());
    });

    $('#tags').autocomplete({
        source: '<?= $routes->url("scope"); ?>',
        minLength: 0,
        blankItem: true
    }).focus(function(){            
        $(this).data("uiAutocomplete").search($(this).val());
    });

    $('#scope').autocomplete("option","appendTo",".eventForm");

    $('#tags').autocomplete("option","appendTo",".eventForm");

    $('#addbutton').click(function(){
        var tag = $('#scope').val();
        if(tag.trim() != ""){
            $('#scopehide').val($('#scopehide').val()+tag+"|");
            $('#scopetags').append('<span class="badge badge-pill badge-success m-r-xs">'+tag+'</span>');
            $('#scope').val("");
        }
    });

    $('#addbuttontags').click(function(){
        var tag = $('#tags').val();
        if(tag.trim() != ""){
            $('#tagshide').val($('#tagshide').val()+tag+"|");
            $('#tagstags').append('<span class="badge badge-pill badge-success m-r-xs">'+tag+'</span>');
            $('#tags').val("");
        }
    });

    grecaptcha.ready(function() {
        grecaptcha.execute('<?php echo SITE_KEY; ?>', {action: 'homepage'}).then(function(token) {
            document.getElementById('g-recaptcha-response').value = token;
        });
    });

    $('[data-toggle="datepicker"]').datepicker();
</script>

<?php
    $script = ob_get_clean();
?>