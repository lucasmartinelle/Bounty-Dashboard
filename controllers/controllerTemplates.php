<?php
    namespace controllers;

    require_once("views/View.php");
    require_once("models/platformHandler.php");
    require_once("models/programHandler.php");
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
    use Models\TemplateHandler;
    use view\View;
    use app\languages\languageManager;

    class controllerTemplates {
        private $_view;
        private $_session;
        private $_validator;
        private $_routes;
        private $_captchaHandler;
        private $_lang;
        private $_platformHandler;
        private $_programHandler;
        private $_templateHandler;
        
        public function __construct($label, $name, $view, $template, $data){
            if($label == "templates"){
                $this->templates($name, $view, $template);
            } elseif($label == "createTemplate"){
                $this->createTemplate($name, $view, $template);
            } elseif($label == "deleteTemplate"){
                $this->deleteTemplate($data);
            } elseif($label == "editTemplate"){
                $this->editTemplate($name, $view, $template, $data);
            } elseif($label == "showTemplate"){
                $this->showTemplate($name, $view, $template, $data);
            }
        }

        private function templates($name, $view, $template){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            if($_POST){
                $this->postTemplates();
            } else {
                if($this->_session->isAuth()){
                    $this->_templateHandler = new TemplateHandler;
                    $this->_programHandler = new ProgramHandler;
                    $this->_platformHandler = new platformHandler;
                    $admin = $this->_session->isAdmin();
                    $token = $this->_session->getToken();
                    if(isset($_SESSION['watchState']) && !empty($_SESSION['watchState']) && $_SESSION['watchState'] == 'me'){ 
                        $templates = $this->_templateHandler->getTemplates(array('creator_id' => htmlspecialchars($_SESSION['id'], ENT_QUOTES)));
                    } else {
                        $templates = $this->_templateHandler->getTemplates();
                    }
                    $programs = $this->_programHandler->getPrograms();
                    $platforms = $this->_platformHandler->getPlatforms();
                    $this->_view = new View($view, $template);
                    $this->_view->generate(array("titre" => $name, "token" => $token, "admin" => $admin, "templates" => $templates, "programs" => $programs, "platforms" => $platforms));
                } else {
                    header('Location: ' . $this->_routes->url('login'));
                    exit;
                }
            }
        }

        private function createTemplate($name, $view, $template){
            $this->_session = new Session;
            $this->_routes = new Routes;
            if($_POST){
                $this->postCreateTemplate();
            } else {
                if($this->_session->isAuth()){
                    $admin = $this->_session->isAdmin();
                    $token = $this->_session->getToken();
                    $this->_view = new View($view, $template);
                    $this->_view->generate(array("titre" => $name, "token" => $token, "admin" => $admin));
                } else {
                    header('Location: ' . $this->_routes->url('login'));
                    exit;
                }
            }
        }

        private function postCreateTemplate(){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            $last_token = $this->_session->getToken();
            if($this->_session->isAuth()){
                if($this->postDataValid($last_token)){
                    $_SESSION['inputValueTitle'] = htmlspecialchars($_POST['title'], ENT_QUOTES);
                    $_SESSION['inputValueDescription'] = htmlspecialchars($_POST['description'], ENT_QUOTES);
                    $_SESSION['inputValueImpact'] = htmlspecialchars($_POST['impact'], ENT_QUOTES);
                    $_SESSION['inputValueRessources'] = htmlspecialchars($_POST['ressources'], ENT_QUOTES);
                    $_SESSION['inputValueStepstoreproduce'] = htmlspecialchars($_POST['stepstoreproduce'], ENT_QUOTES);
                    $_SESSION['inputValueMitigation'] = htmlspecialchars($_POST['mitigation'], ENT_QUOTES);

                    $token = $this->_session->updateToken();

                    $data = array(
                        array("title", $_POST['title'], 'required', "max:200", "unique|templates|title"),
                    );

                    $this->_validator = new Validator();
                    $response = $this->_validator->validator($data);

                    if($response['success'] == 'false'){
                        $_SESSION['inputResponseTitle'] = $response['title'];

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

                        header('Location: ' . $this->_routes->url("createTemplate"));
                        exit;
                    } else {
                        $id = $this->GUIDv4();
                        $creator_id = htmlspecialchars($_SESSION['id'], ENT_QUOTES);
                        $title = htmlspecialchars($_POST['title'], ENT_QUOTES);
                        $columns = array("id", "creator_id", "title");
                        $values = array($id, $creator_id, $title);
                        if(isset($_POST['description']) && !empty($_POST['description'])){
                            array_push($columns, 'description');
                            array_push($values, htmlspecialchars($_POST['description'], ENT_QUOTES));
                        }
                        if(isset($_POST['impact']) && !empty($_POST['impact'])){
                            array_push($columns, 'impact');
                            array_push($values, htmlspecialchars($_POST['impact'], ENT_QUOTES));
                        }
                        if(isset($_POST['ressources']) && !empty($_POST['ressources'])){
                            array_push($columns, 'resources');
                            array_push($values, htmlspecialchars($_POST['ressources'], ENT_QUOTES));
                        }
                        if(isset($_POST['stepstoreproduce']) && !empty($_POST['stepstoreproduce'])){
                            array_push($columns, 'stepsToReproduce');
                            array_push($values, htmlspecialchars($_POST['stepstoreproduce'], ENT_QUOTES));
                        }
                        if(isset($_POST['mitigation']) && !empty($_POST['mitigation'])){
                            array_push($columns, 'mitigation');
                            array_push($values, htmlspecialchars($_POST['mitigation'], ENT_QUOTES));
                        }
                        $this->_templateHandler = new TemplateHandler;
                        if($this->_templateHandler->newTemplate($columns, $values)){
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "template-create");
                            $_SESSION['typeAlert'] = "success";
                            header('Location: ' . $this->_routes->url("createTemplate"));
                            exit;
                        } else {
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->url("createTemplate"));
                            exit;
                        }
                    }
                } else {
                    $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                    $_SESSION['typeAlert'] = "error";
                    header('Location: ' . $this->_routes->url("createTemplate"));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function deleteTemplate($data){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            if($this->_session->isAuth()){
                $this->_templateHandler = new TemplateHandler;
                $id = htmlspecialchars($data[2], ENT_QUOTES);
                $templates = $this->_templateHandler->getTemplates(array("id" => $id));
                foreach($templates as $template){
                    if($this->_templateHandler->deleteTemplate(array("id" => $id))){
                        $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "template-delete");
                        $_SESSION['typeAlert'] = "success";
                        header('Location: ' . $this->_routes->url('templates'));
                        exit;
                    } else {
                        $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                        $_SESSION['typeAlert'] = "error";
                        header('Location: ' . $this->_routes->url('templates'));
                        exit;
                    }
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function editTemplate($name, $view, $template, $data){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_templateHandler = new TemplateHandler;
            $id = htmlspecialchars($data[2], ENT_QUOTES);
            $exist = false;
            $templates = $this->_templateHandler->getTemplates(array("id" => $id));
            foreach($templates as $rtemplate){
                $exist = true;
                $programs = $this->_templateHandler->getTemplates();
                if($_POST){
                    $this->postEditTemplate($id, $rtemplate);
                } else {
                    if($this->_session->isAuth()){
                        $admin = $this->_session->isAdmin();
                        $token = $this->_session->getToken();
                        $this->_view = new View($view, $template);
                        $this->_view->generate(array("titre" => $name, "token" => $token, "admin" => $admin, "template" => $rtemplate));
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

        private function postEditTemplate($id, $template){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            $last_token = $this->_session->getToken();
            if($this->_session->isAuth()){
                if($this->postDataValid($last_token)){
                    $_SESSION['inputValueTitle'] = htmlspecialchars($_POST['title'], ENT_QUOTES);
                    $_SESSION['inputValueDescription'] = htmlspecialchars($_POST['description'], ENT_QUOTES);
                    $_SESSION['inputValueImpact'] = htmlspecialchars($_POST['impact'], ENT_QUOTES);
                    $_SESSION['inputValueRessources'] = htmlspecialchars($_POST['ressources'], ENT_QUOTES);
                    $_SESSION['inputValueStepstoreproduce'] = htmlspecialchars($_POST['stepstoreproduce'], ENT_QUOTES);
                    $_SESSION['inputValueMitigation'] = htmlspecialchars($_POST['mitigation'], ENT_QUOTES);

                    $token = $this->_session->updateToken();

                    $data = array(
                        array("title", $_POST['title'], "max:200", "unique|templates|title:".$template->title()),
                    );

                    $this->_validator = new Validator();
                    $response = $this->_validator->validator($data);

                    if($response['success'] == 'false'){
                        $_SESSION['inputResponseTitle'] = $response['title'];

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

                        header('Location: ' . $this->_routes->url("editTemplate"));
                        exit;
                    } else {
                        $title = (!isset($_POST['title']) && empty($_POST['title'])) ? NULL : htmlspecialchars($_POST['title'], ENT_QUOTES);
                        $description = (!isset($_POST['description']) && empty($_POST['description'])) ? NULL : htmlspecialchars($_POST['description'], ENT_QUOTES);
                        $impact = (!isset($_POST['impact']) && empty($_POST['impact'])) ? NULL : htmlspecialchars($_POST['impact'], ENT_QUOTES);
                        $ressources = (!isset($_POST['ressources']) && empty($_POST['ressources'])) ? NULL : htmlspecialchars($_POST['ressources'], ENT_QUOTES);
                        $stepstoreproduce = (!isset($_POST['stepstoreproduce']) && empty($_POST['stepstoreproduce'])) ? NULL : htmlspecialchars($_POST['stepstoreproduce'], ENT_QUOTES);
                        $mitigation = (!isset($_POST['mitigation']) && empty($_POST['mitigation'])) ? NULL : htmlspecialchars($_POST['mitigation'], ENT_QUOTES);
                        $this->_templateHandler = new TemplateHandler;
                        if($this->_templateHandler->updateTemplate(array("title" => $title, "description" =>  $description, "stepstoreproduce" => $stepstoreproduce, "impact" => $impact, "mitigation" => $mitigation, "resources" => $ressources), array("id" => $id))){
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "template-edit");
                            $_SESSION['typeAlert'] = "success";
                            header('Location: ' . $this->_routes->url("templates"));
                            exit;
                        } else {
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->url("templates"));
                            exit;
                        }
                    }
                } else {
                    $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                    $_SESSION['typeAlert'] = "error";
                    header('Location: ' . $this->_routes->url("templates"));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function showTemplate($name, $view, $template, $data){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_templateHandler = new TemplateHandler;
            $this->_lang = new languageManager;
            $id = htmlspecialchars($data[2], ENT_QUOTES);
            $exist = false;
            $templates = $this->_templateHandler->getTemplates(array("id" => $id));
            foreach($templates as $rtemplate){
                $exist = true;
                if($this->_session->isAuth()){
                    $admin = $this->_session->isAdmin();
                    $token = $this->_session->getToken();
                    $this->_view = new View($view, $template);
                    $this->_view->generate(array("titre" => $name, "token" => $token, "admin" => $admin, "template" => $rtemplate));
                } else {
                    header('Location: ' . $this->_routes->url('login'));
                    exit;
                }
            }
            if(!$exist){
                $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                $_SESSION['typeAlert'] = "error";
                header('Location: ' . $this->_routes->url('templates'));
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