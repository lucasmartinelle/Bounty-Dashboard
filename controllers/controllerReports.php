<?php
    namespace controllers;

    require_once("views/View.php");
    require_once("models/platformHandler.php");
    require_once("models/programHandler.php");
    require_once("models/reportHandler.php");
    require_once("models/templateHandler.php");
    require_once("utils/Validator.php");
    require_once("utils/Session.php");
    require_once("models/captchaHandler.php");
    require_once("app/Routes.php");
    require_once("app/languages/languageManager.php");

    use app\Routes;
    use Utils\Session;
    use Utils\Validator;
    use Models\CaptchaHandler;
    use Models\PlatformHandler;
    use Models\ProgramHandler;
    use Models\ReportHandler;
    use Models\TemplateHandler;
    use view\View;
    use app\languages\languageManager;

    class controllerReports {
        private $_view;
        private $_session;
        private $_validator;
        private $_routes;
        private $_captchaHandler;
        private $_lang;
        private $_platformHandler;
        private $_programHandler;
        private $_reportHandler;
        private $_templateHandler;
        
        public function __construct($label, $name, $view, $template, $data){
            if($label == "reports"){
                $this->reports($name, $view, $template);
            } elseif($label == "filterReports"){
                $this->filterReports($name, $view, $template);
            } elseif($label == "deleteReport"){
                $this->deleteReport($data);
            } elseif($label == "createReport"){
                $this->createReport($name, $view, $template);
            } elseif($label == "editReport"){
                $this->editReport($name, $view, $template, $data);
            } elseif($label == "showReport"){
                $this->showReport($name, $view, $template, $data);
            } elseif($label == "gainReport"){
                $this->gainReport();
            } elseif($label == "useTemplate"){
                $this->useTemplate($data);
            } elseif($label == "generateMarkdown"){
                $this->generateMarkdown();
            }
        }

        private function reports($name, $view, $template){
            $this->_session = new Session;
            $this->_routes = new Routes;
            if($_POST){
                $this->postreport();
            } else {
                if($this->_session->isAuth()){
                    $this->_reportHandler = new ReportHandler;
                    $this->_programHandler = new ProgramHandler;
                    $this->_platformHandler = new platformHandler;
                    $admin = $this->_session->isAdmin();
                    $token = $this->_session->getToken();
                    if(isset($_SESSION['watchState']) && !empty($_SESSION['watchState']) && $_SESSION['watchState'] == 'me'){ 
                        $reports = $this->_reportHandler->getReports(array('creator_id' => htmlspecialchars($_SESSION['id'], ENT_QUOTES)));
                    } else {
                        $reports = $this->_reportHandler->getReports();
                    }
                    $programs = $this->_programHandler->getPrograms();
                    $platforms = $this->_platformHandler->getPlatforms();
                    $this->_view = new View($view, $template);
                    $this->_view->generate(array("titre" => $name, "token" => $token, "admin" => $admin, "reports" => $reports, "programs" => $programs, "platforms" => $platforms));
                } else {
                    header('Location: ' . $this->_routes->url('login'));
                    exit;
                }
            }
        }

        private function postReport(){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            $last_token = $this->_session->getToken();
            if($this->_session->isAuth()){
                if($this->postDataValid($last_token)){
                    $token = $this->_session->updateToken();

                    $data = array(
                        array("idReport", $_POST['idReport'], 'required', 'max:36'),
                        array("status", $_POST['status'], 'required', "max:100", "equal|accepted|resolved|NA|OOS|informative")
                    );

                    $this->_validator = new Validator();
                    $response = $this->_validator->validator($data);

                    if($response['success'] == 'false'){
                        $_SESSION['inputResponseStatus'] = $response['status'];

                        if($response['idReport'] == 'invalid'){
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->url("reports"));
                            exit;
                        }

                        if($response['status'] == 'invalid'){
                            $_SESSION['inputResponseStatusMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['status'] as $e){
                                $_SESSION['inputResponseStatusMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseStatusMessage'] .= "</span>";
                        }

                        header('Location: ' . $this->_routes->url("reports"));
                        exit;
                    } else {
                        $id = htmlspecialchars($_POST['idReport'], ENT_QUOTES);
                        $status = htmlspecialchars($_POST['status'], ENT_QUOTES);  
                        $this->_reportHandler = new ReportHandler;
                        if($this->_reportHandler->updateReport(array("status" => $status), array("id" => $id))){
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "status-change");
                            $_SESSION['typeAlert'] = "success";
                            header('Location: ' . $this->_routes->url("reports"));
                            exit;
                        } else {
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->url("reports"));
                            exit;
                        }
                    }
                } else {
                    $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                    $_SESSION['typeAlert'] = "error";
                    header('Location: ' . $this->_routes->url("reports"));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function filterReports($name, $view, $template){
            $this->_session = new Session;
            $this->_routes = new Routes;
            if(!$_POST){
                header('Location: ' . $this->_routes->url("reports"));
                exit;
            } else {
                $this->_lang = new languageManager;
                $last_token = $this->_session->getToken();
                if($this->_session->isAuth()){
                    if($this->postDataValid($last_token)){
                        $token = $this->_session->updateToken();
                        $this->_programHandler = new ProgramHandler;
                        $programs = $this->_programHandler->getPrograms();
                        $this->_platformHandler = new platformHandler;
                        $platforms = $this->_platformHandler->getPlatforms();

                        $listPrograms = "";
                        foreach($programs as $program){
                            $listPrograms .= $program->id() . "|";
                        }
                        $listPrograms = substr($listPrograms, 0, -1);

                        $listPlatforms = "";
                        foreach($platforms as $platform){
                            $listPlatforms .= $platform->id() . "|";
                        }
                        $listPlatforms = substr($listPlatforms, 0, -1);

                        $data = array(
                            array("program", $_POST['program'], 'equal|'.$listPrograms),
                            array("platform", $_POST['platform'], 'equal|'.$listPlatforms),
                            array("status2", $_POST['status2'], "max:100", "equal|new|accepted|resolved|NA|OOS|informative"),
                            array("severitymin", $_POST['severitymin'], 'float'),
                            array("severitymax", $_POST['severitymax'], 'float')
                        );

                        $this->_validator = new Validator();
                        $response = $this->_validator->validator($data);

                        if($response['success'] == 'false'){
                            $_SESSION['inputResponseProgram'] = $response['program'];
                            $_SESSION['inputResponsePlatform'] = $response['platform'];
                            $_SESSION['inputResponseStatus2'] = $response['status2'];
                            $_SESSION['inputResponseSeveritymin'] = $response['severitymin'];
                            $_SESSION['inputResponseSeveritymax'] = $response['severitymax'];

                            if($response['program'] == 'invalid'){
                                $_SESSION['inputResponseProgramMessage'] = "<span class='text-danger'>";
                                foreach($response['message']['program'] as $e){
                                    $_SESSION['inputResponseProgramMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                }
                                $_SESSION['inputResponseProgramMessage'] .= "</span>";
                            }

                            if($response['platform'] == 'invalid'){
                                $_SESSION['inputResponsePlatformMessage'] = "<span class='text-danger'>";
                                foreach($response['message']['platform'] as $e){
                                    $_SESSION['inputResponsePlatformMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                }
                                $_SESSION['inputResponsePlatformMessage'] .= "</span>";
                            }

                            if($response['status2'] == 'invalid'){
                                $_SESSION['inputResponseStatusMessage2'] = "<span class='text-danger'>";
                                foreach($response['message']['status2'] as $e){
                                    $_SESSION['inputResponseStatusMessage2'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                }
                                $_SESSION['inputResponseStatusMessage2'] .= "</span>";
                            }

                            if($response['severitymin'] == 'invalid'){
                                $_SESSION['inputResponseSeverityminMessage'] = "<span class='text-danger'>";
                                foreach($response['message']['severitymin'] as $e){
                                    $_SESSION['inputResponseSeverityminMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                }
                                $_SESSION['inputResponseSeverityminMessage'] .= "</span>";
                            }

                            if($response['severitymax'] == 'invalid'){
                                $_SESSION['inputResponseSeveritymaxMessage'] = "<span class='text-danger'>";
                                foreach($response['message']['severitymax'] as $e){
                                    $_SESSION['inputResponseSeveritymaxMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                }
                                $_SESSION['inputResponseSeveritymaxMessage'] .= "</span>";
                            }
                            
                            header('Location: ' . $this->_routes->url("reports"));
                            exit;
                        } else {
                            $program = htmlspecialchars($_POST['program'], ENT_QUOTES);
                            if(empty($program) || !isset($program)){
                                $program = null;
                            }
                            $platform = htmlspecialchars($_POST['platform'], ENT_QUOTES);
                            if(empty($platform) || !isset($platform)){
                                $platform = null;
                            }
                            $status = htmlspecialchars($_POST['status2'], ENT_QUOTES);
                            if(empty($status) || !isset($status)){
                                $status = null;
                            }
                            $severitymin = htmlspecialchars($_POST['severitymin'], ENT_QUOTES);
                            if(empty($severitymin) || !isset($severitymin)){
                                $severitymin = null;
                            }
                            $severitymax = htmlspecialchars($_POST['severitymax'], ENT_QUOTES);
                            if(empty($severitymax) || !isset($severitymax)){
                                $severitymax = null;
                            }
                            $this->_reportHandler = new ReportHandler;
                            $reports = $this->_reportHandler->bugs(false, $program, $platform, $status, $severitymin, $severitymax);
                            $admin = $this->_session->isAdmin();
                            $token = $this->_session->getToken();
                            $this->_view = new View($view, $template);
                            $this->_view->generate(array("titre" => $name, "token" => $token, "admin" => $admin, "reports" => $reports, "programs" => $programs, "platforms" => $platforms));
                        }
                    } else {
                        $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                        $_SESSION['typeAlert'] = "error";
                        header('Location: ' . $this->_routes->url("reports"));
                        exit;
                    }
                } else {
                    header('Location: ' . $this->_routes->url('login'));
                    exit;
                }
            }
        }

        private function deleteReport($data){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            if($this->_session->isAuth()){
                $this->_reportHandler = new ReportHandler;
                $id = htmlspecialchars($data[2], ENT_QUOTES);
                $reports = $this->_reportHandler->getReports(array("id" => $id));
                foreach($reports as $report){
                    if($this->_reportHandler->deleteReport(array("id" => $id))){
                        $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "report-delete");
                        $_SESSION['typeAlert'] = "success";
                        header('Location: ' . $this->_routes->url('reports'));
                        exit;
                    } else {
                        $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                        $_SESSION['typeAlert'] = "error";
                        header('Location: ' . $this->_routes->url('reports'));
                        exit;
                    }
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function gainReport(){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            $last_token = $this->_session->getToken();
            if($this->_session->isAuth()){
                if($this->postDataValid($last_token)){
                    $_SESSION['inputValueGain'] = htmlspecialchars($_POST['gain'], ENT_QUOTES);

                    $token = $this->_session->updateToken();

                    $data = array(
                        array("idReport", $_POST['idReport'], 'required', 'max:36'),
                        array("gain", $_POST['gain'], 'required', "max:3", "onlyNumber"),
                    );

                    $this->_validator = new Validator();
                    $response = $this->_validator->validator($data);

                    if($response['success'] == 'false'){
                        $_SESSION['inputResponseGain'] = $response['gain'];

                        if($response['idReport'] == 'invalid'){
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->url("reports"));
                            exit;
                        }

                        if($response['gain'] == 'invalid'){
                            $_SESSION['inputResponseGainMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['gain'] as $e){
                                $_SESSION['inputResponseGainMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseGainMessage'] .= "</span>";
                        }

                        header('Location: ' . $this->_routes->url("reports"));
                        exit;
                    } else {
                        $this->_reportHandler = new ReportHandler;
                        $id = htmlspecialchars($_POST['idReport'], ENT_QUOTES);
                        $gain = htmlspecialchars($_POST['gain'], ENT_QUOTES);  
                        if($this->_reportHandler->updateReport(array("gain" => $gain), array("id" => $id))){
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "gain-change");
                            $_SESSION['typeAlert'] = "success";
                            header('Location: ' . $this->_routes->url("reports"));
                            exit;
                        } else {
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->url("reports"));
                            exit;
                        }
                    }
                } else {
                    $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                    $_SESSION['typeAlert'] = "error";
                    header('Location: ' . $this->_routes->url('reports'));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function editReport($name, $view, $template, $data){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_programHandler = new ProgramHandler;
            $this->_reportHandler = new ReportHandler;
            $id = htmlspecialchars($data[2], ENT_QUOTES);
            $exist = false;
            $reports = $this->_reportHandler->getReports(array("id" => $id));
            foreach($reports as $report){
                $exist = true;
                $programs = $this->_programHandler->getPrograms();
                if($_POST){
                    $this->postEditReport($programs, $id, $report);
                } else {
                    if($this->_session->isAuth()){
                        $admin = $this->_session->isAdmin();
                        $token = $this->_session->getToken();
                        $this->_view = new View($view, $template);
                        $this->_view->generate(array("titre" => $name, "token" => $token, "admin" => $admin, "programs" => $programs, "report" => $report));
                    } else {
                        header('Location: ' . $this->_routes->url('login'));
                        exit;
                    }
                }
            }
            if(!$exist){
                $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                $_SESSION['typeAlert'] = "error";
                header('Location: ' . $this->_routes->url('reports'));
                exit;
            }
        }

        private function postEditReport($programs, $id, $report){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            $last_token = $this->_session->getToken();
            if($this->_session->isAuth()){
                if($this->postDataValid($last_token)){
                    $listPrograms = "";
                    foreach($programs as $program){
                        $listPrograms .= $program->id() . "|";
                    }
                    $listPrograms = substr($listPrograms, 0, -1);
                    $_SESSION['inputValueTitle'] = htmlspecialchars($_POST['title'], ENT_QUOTES);
                    $_SESSION['inputValueIdentifiant'] = htmlspecialchars($_POST['identifiant'], ENT_QUOTES);
                    $_SESSION['inputValueDate'] = htmlspecialchars($_POST['date'], ENT_QUOTES);
                    $_SESSION['inputValueSeverity'] = htmlspecialchars($_POST['severity'], ENT_QUOTES);
                    $_SESSION['inputValueEndpoint'] = htmlspecialchars($_POST['endpoint'], ENT_QUOTES);
                    $_SESSION['inputValueImpact'] = htmlspecialchars($_POST['impact'], ENT_QUOTES);
                    $_SESSION['inputValueRessources'] = htmlspecialchars($_POST['ressources'], ENT_QUOTES);
                    $_SESSION['inputValueStepstoreproduce'] = htmlspecialchars($_POST['stepstoreproduce'], ENT_QUOTES);
                    $_SESSION['inputValueMitigation'] = htmlspecialchars($_POST['mitigation'], ENT_QUOTES);

                    $token = $this->_session->updateToken();

                    $data = array(
                        array("title", $_POST['title'], 'required', "max:200", "unique|reports|title:".$report->title()),
                        array("identifiant", $_POST['identifiant'], 'required', 'max:200', "unique|reports|identifiant:".$report->identifiant()),
                        array("date", $_POST['date'], 'required', 'date'),
                        array("severity", $_POST['severity'], 'required', 'float'),
                        array("endpoint", $_POST['endpoint'], 'required', 'text'),
                        array("program", $_POST['program'], 'required', 'equal|'.$listPrograms),
                        array("impact", $_POST['impact'], 'required'),
                        array("ressources", $_POST['ressources'], 'required'),
                        array("stepstoreproduce", $_POST['stepstoreproduce'], 'required'),
                        array("mitigation", $_POST['mitigation'], 'required'),
                    );

                    $this->_validator = new Validator();
                    $response = $this->_validator->validator($data);

                    if($response['success'] == 'false'){
                        $_SESSION['inputResponseTitle'] = $response['title'];
                        $_SESSION['inputResponseIdentifiant'] = $response['identifiant'];
                        $_SESSION['inputResponseDate'] = $response['date'];
                        $_SESSION['inputResponseSeverity'] = $response['severity'];
                        $_SESSION['inputResponseEndpoint'] = $response['endpoint'];
                        $_SESSION['inputResponseProgram'] = $response['program'];
                        $_SESSION['inputResponseImpact'] = $response['impact'];
                        $_SESSION['inputResponseRessources'] = $response['ressources'];
                        $_SESSION['inputResponseStepstoreproduce'] = $response['stepstoreproduce'];
                        $_SESSION['inputResponseMitigation'] = $response['mitigation'];

                        if($response['title'] == 'invalid'){
                            $_SESSION['inputResponseTitleMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['title'] as $e){
                                $_SESSION['inputResponseTitleMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseTitleMessage'] .= "</span>";
                        }

                        if($response['unique']['title'] == 'false'){
                            $_SESSION['inputResponseTitleMessage'] = "<span class='text-danger'><i class='fas fa-circle' style='font-size: 8px;'></i> " . $this->_lang->getTxt('controllerReports', "title-taken") . " </span>";
                        }

                        if($response['identifiant'] == 'invalid'){
                            $_SESSION['inputResponseIdentifiantMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['identifiant'] as $e){
                                $_SESSION['inputResponseIdentifiantMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseIdentifiantMessage'] .= "</span>";
                        }

                        if($response['unique']['identifiant'] == 'false'){
                            $_SESSION['inputResponseIdentifiantMessage'] = "<span class='text-danger'><i class='fas fa-circle' style='font-size: 8px;'></i> " . $this->_lang->getTxt('controllerReports', "identifiant-taken") . " </span>";
                        }

                        if($response['date'] == 'invalid'){
                            $_SESSION['inputResponseDateMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['date'] as $e){
                                $_SESSION['inputResponseDateMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseDateMessage'] .= "</span>";
                        }

                        if($response['severity'] == 'invalid'){
                            $_SESSION['inputResponseSeverityMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['severity'] as $e){
                                $_SESSION['inputResponseSeverityMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseSeverityMessage'] .= "</span>";
                        }

                        if($response['endpoint'] == 'invalid'){
                            $_SESSION['inputResponseEndpointMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['endpoint'] as $e){
                                $_SESSION['inputResponseEndpointMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseEndpointMessage'] .= "</span>";
                        }

                        if($response['program'] == 'invalid'){
                            $_SESSION['inputResponseProgramMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['program'] as $e){
                                $_SESSION['inputResponseProgramMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseProgramMessage'] .= "</span>";
                        }

                        if($response['impact'] == 'invalid'){
                            $_SESSION['inputResponseImpactMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['impact'] as $e){
                                $_SESSION['inputResponseImpactMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseImpactMessage'] .= "</span>";
                        }

                        if($response['ressources'] == 'invalid'){
                            $_SESSION['inputResponseRessourcesMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['ressources'] as $e){
                                $_SESSION['inputResponseRessourcesMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseRessourcesMessage'] .= "</span>";
                        }

                        if($response['stepstoreproduce'] == 'invalid'){
                            $_SESSION['inputResponseStepstoreproduceMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['stepstoreproduce'] as $e){
                                $_SESSION['inputResponseStepstoreproduceMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseStepstoreproduceMessage'] .= "</span>";
                        }

                        if($response['mitigation'] == 'invalid'){
                            $_SESSION['inputResponseMitigationMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['mitigation'] as $e){
                                $_SESSION['inputResponseMitigationMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseMitigationMessage'] .= "</span>";
                        }

                        header('Location: ' . $this->_routes->url("editReport"));
                        exit;
                    } else {
                        $title = htmlspecialchars($_POST['title'], ENT_QUOTES);
                        $identifiant = htmlspecialchars($_POST['identifiant'], ENT_QUOTES);
                        $date = htmlspecialchars($_POST['date'], ENT_QUOTES);
                        $date = strtotime($date);
                        $date = date('Y-m-d H:i:s', $date);
                        $severity = htmlspecialchars($_POST['severity'], ENT_QUOTES);
                        $endpoint = htmlspecialchars($_POST['endpoint'], ENT_QUOTES);
                        $program = htmlspecialchars($_POST['program'], ENT_QUOTES);
                        $impact = htmlspecialchars($_POST['impact'], ENT_QUOTES);
                        $ressources = htmlspecialchars($_POST['ressources'], ENT_QUOTES);
                        $stepstoreproduce = htmlspecialchars($_POST['stepstoreproduce'], ENT_QUOTES);
                        $mitigation = htmlspecialchars($_POST['mitigation'], ENT_QUOTES);
                        $this->_reportHandler = new ReportHandler;
                        if($this->_reportHandler->updateReport(array("title" => $title, "severity" => $severity, "endpoint" => $endpoint, "identifiant" => $identifiant, "date" => $date, "program_id" => $program, "stepstoreproduce" => $stepstoreproduce, "impact" => $impact, "mitigation" => $mitigation, "resources" => $ressources), array("id" => $id))){
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "report-edit");
                            $_SESSION['typeAlert'] = "success";
                            header('Location: ' . $this->_routes->url("reports"));
                            exit;
                        } else {
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->url("reports"));
                            exit;
                        }
                    }
                } else {
                    $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                    $_SESSION['typeAlert'] = "error";
                    header('Location: ' . $this->_routes->url("reports"));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function showReport($name, $view, $template, $data){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_programHandler = new ProgramHandler;
            $this->_reportHandler = new ReportHandler;
            $id = htmlspecialchars($data[2], ENT_QUOTES);
            $exist = false;
            $reports = $this->_reportHandler->getReports(array("id" => $id));
            foreach($reports as $report){
                $exist = true;
                $programs = $this->_programHandler->getPrograms(array("id" => $report->programid()));
                foreach($programs as $program){
                    if($this->_session->isAuth()){
                        $admin = $this->_session->isAdmin();
                        $token = $this->_session->getToken();
                        $this->_view = new View($view, $template);
                        $this->_view->generate(array("titre" => $name, "token" => $token, "admin" => $admin, "program" => $program, "report" => $report));
                    } else {
                        header('Location: ' . $this->_routes->url('login'));
                        exit;
                    }
                }
            }
            if(!$exist){
                $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                $_SESSION['typeAlert'] = "error";
                header('Location: ' . $this->_routes->url('reports'));
                exit;
            }
        }

        private function generateMarkdown(){
            $this->_programHandler = new ProgramHandler;
            $programs = $this->_programHandler->getPrograms();
            $listPrograms = "";
            foreach($programs as $program){
                $listPrograms .= $program->id() . "|";
            }
            $listPrograms = substr($listPrograms, 0, -1);
            $data = array(
                array("title", $_POST['title'], 'required', "max:200"),
                array("date", $_POST['date'], 'required', 'date'),
                array("severity", $_POST['severity'], 'required', 'float|0.0|10.0'),
                array("endpoint", $_POST['endpoint'], 'required', 'text'),
                array("program", $_POST['program'], 'required', 'equal|'.$listPrograms),
                array("impact", $_POST['impact'], 'required'),
                array("ressources", $_POST['ressources'], 'required'),
                array("stepstoreproduce", $_POST['stepstoreproduce'], 'required'),
                array("mitigation", $_POST['mitigation'], 'required'),
            );

            $this->_validator = new Validator();
            $response = $this->_validator->validator($data);

            if($response['success'] == 'true'){
                $id = $this->GUIDv4();
                $title = $_POST['title'];
                $date = $_POST['date'];
                $severity = $_POST['severity'];
                $endpoint = $_POST['endpoint'];
                $program = $_POST['program'];
                $impact = $_POST['impact'];
                $ressources = $_POST['ressources'];
                $stepstoreproduce = $_POST['stepstoreproduce'];
                $mitigation = $_POST['mitigation'];

                $markdown = '# '.$title.'

---

**date**: '.$date.'

**severity (CVSS Scale)**: '.$severity.'

**endpoint**: '.$endpoint.'

**program**: '.$program.'

---

## Impact

'.$impact.'

---

## Steps to reproduce

'.$stepstoreproduce.'

---

## Ressources

'.$ressources.'

---

## Mitigation

'.$mitigation . '

---';

                $newName = bin2hex(openssl_random_pseudo_bytes(5));
                $path = WEBSITE_PATH."assets/markdown/".$newName.".md";
                $myfile = fopen($path, "w") or die("Unable to open file!");
                fwrite($myfile, $markdown);
                fclose($myfile);
                echo $newName;
            } else {
                echo 'invalid';
            }
        }

        private function createReport($name, $view, $template){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_programHandler = new ProgramHandler;
            $programs = $this->_programHandler->getPrograms();
            if($_POST){
                $this->postCreateReport($programs);
            } else {
                if($this->_session->isAuth()){
                    $this->_templateHandler = new TemplateHandler;
                    $templates = $this->_templateHandler->getTemplates();
                    $admin = $this->_session->isAdmin();
                    $token = $this->_session->getToken();
                    $this->_view = new View($view, $template);
                    $this->_view->generate(array("titre" => $name, "token" => $token, "admin" => $admin, "programs" => $programs, "templates" => $templates));
                } else {
                    header('Location: ' . $this->_routes->url('login'));
                    exit;
                }
            }
        }

        private function postCreateReport($programs){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            $last_token = $this->_session->getToken();
            if($this->_session->isAuth()){
                if($this->postDataValid($last_token)){
                    $listPrograms = "";
                    foreach($programs as $program){
                        $listPrograms .= $program->id() . "|";
                    }
                    $listPrograms = substr($listPrograms, 0, -1);
                    $_SESSION['inputValueTitle'] = htmlspecialchars($_POST['title'], ENT_QUOTES);
                    $_SESSION['inputValueIdentifiant'] = htmlspecialchars($_POST['identifiant'], ENT_QUOTES);
                    $_SESSION['inputValueDate'] = htmlspecialchars($_POST['date'], ENT_QUOTES);
                    $_SESSION['inputValueSeverity'] = htmlspecialchars($_POST['severity'], ENT_QUOTES);
                    $_SESSION['inputValueEndpoint'] = htmlspecialchars($_POST['endpoint'], ENT_QUOTES);
                    $_SESSION['inputValueImpact'] = htmlspecialchars($_POST['impact'], ENT_QUOTES);
                    $_SESSION['inputValueRessources'] = htmlspecialchars($_POST['ressources'], ENT_QUOTES);
                    $_SESSION['inputValueStepstoreproduce'] = htmlspecialchars($_POST['stepstoreproduce'], ENT_QUOTES);
                    $_SESSION['inputValueMitigation'] = htmlspecialchars($_POST['mitigation'], ENT_QUOTES);

                    $token = $this->_session->updateToken();

                    $data = array(
                        array("title", $_POST['title'], 'required', "max:200", "unique|reports|title"),
                        array("identifiant", $_POST['identifiant'], 'required', 'max:200', "unique|reports|identifiant"),
                        array("date", $_POST['date'], 'required', 'date'),
                        array("severity", $_POST['severity'], 'required', 'float|0.0|10.0'),
                        array("endpoint", $_POST['endpoint'], 'required', 'text'),
                        array("program", $_POST['program'], 'required', 'equal|'.$listPrograms),
                        array("impact", $_POST['impact'], 'required'),
                        array("ressources", $_POST['ressources'], 'required'),
                        array("stepstoreproduce", $_POST['stepstoreproduce'], 'required'),
                        array("mitigation", $_POST['mitigation'], 'required'),
                    );

                    $this->_validator = new Validator();
                    $response = $this->_validator->validator($data);

                    if($response['success'] == 'false'){
                        $_SESSION['inputResponseTitle'] = $response['title'];
                        $_SESSION['inputResponseIdentifiant'] = $response['identifiant'];
                        $_SESSION['inputResponseDate'] = $response['date'];
                        $_SESSION['inputResponseSeverity'] = $response['severity'];
                        $_SESSION['inputResponseEndpoint'] = $response['endpoint'];
                        $_SESSION['inputResponseProgram'] = $response['program'];
                        $_SESSION['inputResponseImpact'] = $response['impact'];
                        $_SESSION['inputResponseRessources'] = $response['ressources'];
                        $_SESSION['inputResponseStepstoreproduce'] = $response['stepstoreproduce'];
                        $_SESSION['inputResponseMitigation'] = $response['mitigation'];

                        if($response['title'] == 'invalid'){
                            $_SESSION['inputResponseTitleMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['title'] as $e){
                                $_SESSION['inputResponseTitleMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseTitleMessage'] .= "</span>";
                        }

                        if($response['unique']['title'] == 'false'){
                            $_SESSION['inputResponseTitleMessage'] = "<span class='text-danger'><i class='fas fa-circle' style='font-size: 8px;'></i> " . $this->_lang->getTxt('controllerReports', "title-taken") . " </span>";
                        }

                        if($response['identifiant'] == 'invalid'){
                            $_SESSION['inputResponseIdentifiantMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['identifiant'] as $e){
                                $_SESSION['inputResponseIdentifiantMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseIdentifiantMessage'] .= "</span>";
                        }

                        if($response['unique']['identifiant'] == 'false'){
                            $_SESSION['inputResponseIdentifiantMessage'] = "<span class='text-danger'><i class='fas fa-circle' style='font-size: 8px;'></i> " . $this->_lang->getTxt('controllerReports', "identifiant-taken") . " </span>";
                        }

                        if($response['date'] == 'invalid'){
                            $_SESSION['inputResponseDateMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['date'] as $e){
                                $_SESSION['inputResponseDateMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseDateMessage'] .= "</span>";
                        }

                        if($response['severity'] == 'invalid'){
                            $_SESSION['inputResponseSeverityMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['severity'] as $e){
                                $_SESSION['inputResponseSeverityMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseSeverityMessage'] .= "</span>";
                        }

                        if($response['endpoint'] == 'invalid'){
                            $_SESSION['inputResponseEndpointMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['endpoint'] as $e){
                                $_SESSION['inputResponseEndpointMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseEndpointMessage'] .= "</span>";
                        }

                        if($response['program'] == 'invalid'){
                            $_SESSION['inputResponseProgramMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['program'] as $e){
                                $_SESSION['inputResponseProgramMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseProgramMessage'] .= "</span>";
                        }

                        if($response['impact'] == 'invalid'){
                            $_SESSION['inputResponseImpactMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['impact'] as $e){
                                $_SESSION['inputResponseImpactMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseImpactMessage'] .= "</span>";
                        }

                        if($response['ressources'] == 'invalid'){
                            $_SESSION['inputResponseRessourcesMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['ressources'] as $e){
                                $_SESSION['inputResponseRessourcesMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseRessourcesMessage'] .= "</span>";
                        }

                        if($response['stepstoreproduce'] == 'invalid'){
                            $_SESSION['inputResponseStepstoreproduceMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['stepstoreproduce'] as $e){
                                $_SESSION['inputResponseStepstoreproduceMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseStepstoreproduceMessage'] .= "</span>";
                        }

                        if($response['mitigation'] == 'invalid'){
                            $_SESSION['inputResponseMitigationMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['mitigation'] as $e){
                                $_SESSION['inputResponseMitigationMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseMitigationMessage'] .= "</span>";
                        }

                        header('Location: ' . $this->_routes->url("createReport"));
                        exit;
                    } else {
                        $id = $this->GUIDv4();
                        $creator_id = htmlspecialchars($_SESSION['id'], ENT_QUOTES);
                        $title = htmlspecialchars($_POST['title'], ENT_QUOTES);
                        $identifiant = htmlspecialchars($_POST['identifiant'], ENT_QUOTES);
                        $date = htmlspecialchars($_POST['date'], ENT_QUOTES);
                        $date = strtotime($date);
                        $date = date('Y-m-d H:i:s', $date);
                        $severity = htmlspecialchars($_POST['severity'], ENT_QUOTES);
                        $endpoint = htmlspecialchars($_POST['endpoint'], ENT_QUOTES);
                        $program = htmlspecialchars($_POST['program'], ENT_QUOTES);
                        $impact = htmlspecialchars($_POST['impact'], ENT_QUOTES);
                        $ressources = htmlspecialchars($_POST['ressources'], ENT_QUOTES);
                        $stepstoreproduce = htmlspecialchars($_POST['stepstoreproduce'], ENT_QUOTES);
                        $mitigation = htmlspecialchars($_POST['mitigation'], ENT_QUOTES);
                        $this->_reportHandler = new ReportHandler;
                        if($this->_reportHandler->newReport(array($id,$creator_id,$title,$severity, $date, $endpoint, $identifiant,$program,$stepstoreproduce,$impact,$mitigation,$ressources))){
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "report-create");
                            $_SESSION['typeAlert'] = "success";
                            header('Location: ' . $this->_routes->url("createReport"));
                            exit;
                        } else {
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->url("createReport"));
                            exit;
                        }
                    }
                } else {
                    $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                    $_SESSION['typeAlert'] = "error";
                    header('Location: ' . $this->_routes->url("createReport"));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        public function useTemplate(){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            $last_token = $this->_session->getToken();
            if($this->_session->isAuth()){
                if($this->postDataValid($last_token)){
                    $this->_templateHandler = new TemplateHandler;
                    $templates = $this->_templateHandler->getTemplates();
                    $listTemplates = "";
                    foreach($templates as $template){
                        $listTemplates .= $template->id() . "|";
                    }
                    $listTemplates = substr($listTemplates, 0, -1);
                    $token = $this->_session->updateToken();

                    $data = array(
                        array("template", $_POST['template'], 'required', 'equal|'.$listTemplates)
                    );

                    $this->_validator = new Validator();
                    $response = $this->_validator->validator($data);

                    if($response['success'] == 'false'){
                        $_SESSION['inputResponseTemplate'] = $response['template'];

                        if($response['template'] == 'invalid'){
                            $_SESSION['inputResponseTemplateMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['template'] as $e){
                                $_SESSION['inputResponseTemplateMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseTemplateMessage'] .= "</span>";
                        }

                        header('Location: ' . $this->_routes->url("createReport"));
                        exit;
                    } else {
                        $tlt = htmlspecialchars($_POST['template'], ENT_QUOTES);
                        $templates = $this->_templateHandler->getTemplates(array("id" => $tlt));
                        foreach($templates as $template){
                            $_SESSION['inputValueTitle'] = $template->title();
                            $_SESSION['inputValueSeverity'] = $template->severity();
                            $_SESSION['inputValueEndpoint'] = $template->endpoint();
                            $_SESSION['inputValueImpact'] = $template->impact();
                            $_SESSION['inputValueRessources'] = $template->resources();
                            $_SESSION['inputValueStepstoreproduce'] = $template->stepstoreproduce();
                            $_SESSION['inputValueMitigation'] = $template->mitigation();
                            
                            header('Location: ' . $this->_routes->url("createReport"));
                            exit;
                        }
                    }
                } else {
                    $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                    $_SESSION['typeAlert'] = "error";
                    header('Location: ' . $this->_routes->url('createReport'));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function postDataValid($token) {
            if($token != null){
                $this->_session = new Session;
                if(isset($_POST['token'])){
                    $postToken = htmlspecialchars($_POST['token'], ENT_QUOTES);
                    if($token == $postToken){
                        $this->_captchaHandler = new CaptchaHandler;
                        if($this->_captchaHandler->getPubKey() != null){
                            $ReCaptchaValid = $this->_captchaHandler->verifyCaptcha($_POST['g-recaptcha-response']);
                            if($ReCaptchaValid == true){
                                return true;
                            }
                        } else {
                            return true;
                        }
                    }
                }
                return false;
            } else {
                $this->_session = new Session;
                if(isset($_POST['token'])){
                    $postToken = htmlspecialchars($_POST['token'], ENT_QUOTES);
                    $sessionToken = $this->_session->getToken();
                    if($sessionToken == $postToken){
                        $this->_captchaHandler = new CaptchaHandler;
                        if($this->_captchaHandler->getPubKey() != null){
                            $ReCaptchaValid = $this->_captchaHandler->verifyCaptcha($_POST['g-recaptcha-response']);
                            if($ReCaptchaValid == true){
                                return true;
                            }
                        } else {
                            return true;
                        }
                    }
                }
                return false;
            }
        }

        private function GUIDv4 ($trim = true){
            // Windows
            if (function_exists('com_create_guid') === true) {
                if ($trim === true)
                    return trim(com_create_guid(), '{}');
                else
                    return com_create_guid();
            }

            // OSX/Linux
            if (function_exists('openssl_random_pseudo_bytes') === true) {
                $data = openssl_random_pseudo_bytes(16);
                $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
                $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
                return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
            }

            // Fallback (PHP 4.2+)
            mt_srand((double)microtime() * 10000);
            $charid = strtolower(md5(uniqid(rand(), true)));
            $hyphen = chr(45);                  // "-"
            $lbrace = $trim ? "" : chr(123);    // "{"
            $rbrace = $trim ? "" : chr(125);    // "}"
            $guidv4 = $lbrace.
                    substr($charid,  0,  8).$hyphen.
                    substr($charid,  8,  4).$hyphen.
                    substr($charid, 12,  4).$hyphen.
                    substr($charid, 16,  4).$hyphen.
                    substr($charid, 20, 12).
                    $rbrace;
            return $guidv4;
        }
    }
?>