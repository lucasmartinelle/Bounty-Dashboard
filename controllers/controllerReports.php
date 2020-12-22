<?php
    namespace controllers;

    require_once("views/View.php");
    require_once("models/userHandler.php");
    require_once("models/platformHandler.php");
    require_once("models/programHandler.php");
    require_once("utils/Validator.php");
    require_once("utils/Session.php");
    require_once("utils/Captcha.php");
    require_once("app/Routes.php");
    require_once("app/languages/languageManager.php");
    require_once("models/billingHandler.php");

    use app\Routes;
    use Utils\Session;
    use Utils\Validator;
    use Utils\Captcha;
    use Models\UserHandler;
    use Models\BillingHandler;
    use Models\PlatformHandler;
    use Models\ProgramHandler;
    use view\View;
    use app\languages\languageManager;

    class controllerReports {
        private $_view;
        private $_session;
        private $_userHandler;
        private $_validator;
        private $_routes;
        private $_captcha;
        private $_lang;
        private $_billingHandler; 
        private $_platformHandler;
        private $_programHandler;
        
        public function __construct($label, $name, $view, $template, $data){
            if($label == "platforms"){
                $this->platforms($name, $view, $template);
            } elseif($label == "platformDelete"){
                $this->deletePlatform($data);
            } elseif($label == "programs"){
                $this->programs($name, $view, $template);
            } elseif($label == "scope"){
                $this->scope();
            }
        }

        private function platforms($name, $view, $template){
            $this->_session = new Session;
            $this->_routes = new Routes;
            if($_POST){
                $this->postPlatforms();
            } else {
                if($this->_session->isAuth()){
                    $this->_userHandler = new UserHandler;
                    $this->_platformHandler = new platformHandler;
                    $admin = $this->_session->isAdmin();
                    $token = $this->_session->getToken();
                    $platforms = $this->_platformHandler->getPlatforms();
                    $this->_view = new View($view, $template);
                    $this->_view->generate(array("titre" => $name, "token" => $token, "admin" => $admin, "platforms" => $platforms));
                } else {
                    header('Location: ' . $this->_routes->url('login'));
                    exit;
                }
            }
        }

        protected function postPlatforms(){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            $last_token = $this->_session->getToken();
            if($this->_session->isAuth()){
                if($this->postDataValid($last_token)){
                    $_SESSION['inputValueName'] = htmlspecialchars($_POST['name'], ENT_QUOTES);
                    $_SESSION['inputValueDescription'] = htmlspecialchars($_POST['description'], ENT_QUOTES);

                    $token = $this->_session->updateToken();

                    $data = array(
                        array('file', $_FILES['file'], 'upload:image/gif|image/png|image/jpg|image/jpeg:gif|png|jpg|jpeg'),
                        array('name', $_POST['name'], 'required', 'max:200'),
                        array('description', $_POST['description'], 'required')
                    );

                    $this->_validator = new Validator();
                    $response = $this->_validator->validator($data);

                    if($response['success'] == 'false'){
                        $_SESSION['inputResponseFile'] = $response['file'];
                        $_SESSION['inputResponseName'] = $response['name'];
                        $_SESSION['inputResponseDescription'] = $response['description'];

                        if($response['file'] == 'invalid'){
                            $_SESSION['inputResponseFileMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['file'] as $e){
                                $_SESSION['inputResponseFileMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseFileMessage'] .= "</span>";
                        } else {
                            unlink(WEBSITE_PATH . 'assets/uploads/' . $response['uploaded']);
                        }

                        if($response['name'] == 'invalid'){
                            $_SESSION['inputResponseNameMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['name'] as $e){
                                $_SESSION['inputResponseNameMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseNameMessage'] .= "</span>";
                        }

                        if($response['description'] == 'invalid'){
                            $_SESSION['inputResponseDescriptionMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['description'] as $e){
                                $_SESSION['inputResponseDescriptionMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseDescriptionMessage'] .= "</span>";
                        }
                        header('Location: ' . $this->_routes->url("platforms"));
                        exit;
                    } else {
                        $id = $this->GUIDv4();
                        $creator_id = htmlspecialchars($_SESSION['id'], ENT_QUOTES);
                        $name = htmlspecialchars($_POST['name'], ENT_QUOTES);
                        $description = htmlspecialchars($_POST['description'], ENT_QUOTES);
                        $logo = htmlspecialchars($response['uploaded'], ENT_QUOTES);
                        $this->_platformHandler = new platformHandler;
                        if($this->_platformHandler->newPlatform(array($id, $creator_id, $name, $description, $logo))){
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "platform-create");
                            $_SESSION['typeAlert'] = "success";
                            header('Location: ' . $this->_routes->url("platforms"));
                            exit;
                        } else {
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->url("platforms"));
                            exit;
                        }
                    }
                } else {
                    $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                    $_SESSION['typeAlert'] = "error";
                    header('Location: ' . $this->_routes->url("platforms"));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function deletePlatform($data){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            if($this->_session->isAuth()){
                if($this->_session->isAdmin()){
                    $id = htmlspecialchars($data[2], ENT_QUOTES);
                    $this->_platformHandler = new platformHandler;
                    $platforms = $this->_platformHandler->getPlatforms(array("id" => $id));
                    $exist = false;
                    foreach($platforms as $platform){
                        $exist = true;
                        if($this->_platformHandler->deletePlatform($id)){
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "platform-deleted");
                            $_SESSION['typeAlert'] = "success";
                            header('Location: ' . $this->_routes->url("platforms"));
                            exit;
                        } else {
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->url("platforms"));
                            exit;
                        }
                    }
                    if(!$exist){
                        $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                        $_SESSION['typeAlert'] = "error";
                        header('Location: ' . $this->_routes->url("platforms"));
                        exit;
                    }
                } else {
                    header('Location: ' . $this->_routes->url("platforms"));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function programs($name, $view, $template){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_platformHandler = new platformHandler;
            $platforms = $this->_platformHandler->getPlatforms();
            if($_POST){
                $this->postPrograms($platforms);
            } else {
                if($this->_session->isAuth()){
                    $this->_userHandler = new UserHandler;
                    $admin = $this->_session->isAdmin();
                    $token = $this->_session->getToken();
                    $this->_view = new View($view, $template);
                    $this->_view->generate(array("titre" => $name, "token" => $token, "admin" => $admin, "platforms" => $platforms));
                } else {
                    header('Location: ' . $this->_routes->url('login'));
                    exit;
                }
            }
        }

        private function postPrograms($platforms){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            $last_token = $this->_session->getToken();
            if($this->_session->isAuth()){
                if($this->postDataValid($last_token)){
                    $listPlatforms = "";
                    foreach($platforms as $platform){
                        $listPlatforms .= $platform->id() . "|";
                    }
                    $listPlatforms = substr($listPlatforms, 0, -1);
                    $_SESSION['inputValueName'] = htmlspecialchars($_POST['name'], ENT_QUOTES);
                    $_SESSION['inputValueScope'] = htmlspecialchars($_POST['scope'], ENT_QUOTES);
                    $_SESSION['inputValueDate'] = htmlspecialchars($_POST['date'], ENT_QUOTES);
                    $_SESSION['inputValueTags'] = htmlspecialchars($_POST['tags'], ENT_QUOTES);

                    $token = $this->_session->updateToken();

                    $data = array(
                        array("name", $_POST['name'], 'required', "max:200", "unique|programs|name"),
                        array("scope", $_POST['scope'], 'required'),
                        array("date", $_POST['date'], 'required', 'date'),
                        array("status", $_POST['status'], 'required', 'equal|open|close'),
                        array("tags", $_POST['tags'], 'required'),
                        array("platform", $_POST['platform'], 'required', 'equal|'.$listPlatforms)
                    );

                    $this->_validator = new Validator();
                    $response = $this->_validator->validator($data);

                    if($response['success'] == 'false'){
                        $_SESSION['inputResponseName'] = $response['name'];
                        $_SESSION['inputResponseScope'] = $response['scope'];
                        $_SESSION['inputResponseDate'] = $response['date'];
                        $_SESSION['inputResponseStatus'] = $response['status'];
                        $_SESSION['inputResponseTags'] = $response['tags'];
                        $_SESSION['inputResponsePlatform'] = $response['platform'];

                        if($response['name'] == 'invalid'){
                            $_SESSION['inputResponseNameMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['name'] as $e){
                                $_SESSION['inputResponseNameMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseNameMessage'] .= "</span>";
                        }

                        if($response['scope'] == 'invalid'){
                            $_SESSION['inputResponseScopeMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['scope'] as $e){
                                $_SESSION['inputResponseScopeMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseScopeMessage'] .= "</span>";
                        }

                        if($response['date'] == 'invalid'){
                            $_SESSION['inputResponseDateMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['date'] as $e){
                                $_SESSION['inputResponseDateMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseDateMessage'] .= "</span>";
                        }

                        if($response['status'] == 'invalid'){
                            $_SESSION['inputResponseStatusMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['status'] as $e){
                                $_SESSION['inputResponseStatusMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseStatusMessage'] .= "</span>";
                        }

                        if($response['tags'] == 'invalid'){
                            $_SESSION['inputResponseTagsMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['tags'] as $e){
                                $_SESSION['inputResponseTagsMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseTagsMessage'] .= "</span>";
                        }

                        if($response['platform'] == 'invalid'){
                            $_SESSION['inputResponsePlatformMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['platform'] as $e){
                                $_SESSION['inputResponsePlatformMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponsePlatformMessage'] .= "</span>";
                        }

                        header('Location: ' . $this->_routes->url("programs"));
                        exit;
                    } else {
                        $id = $this->GUIDv4();
                        $creator_id = htmlspecialchars($_SESSION['id'], ENT_QUOTES);
                        $name = htmlspecialchars($_POST['name'], ENT_QUOTES);
                        $scope = htmlspecialchars($_POST['scope'], ENT_QUOTES);
                        $date = htmlspecialchars($_POST['date'], ENT_QUOTES);
                        $date = strtotime($date);
                        $date = date('Y-m-d H:i:s', $date);
                        $status = htmlspecialchars($_POST['status'], ENT_QUOTES);
                        $platform_id = htmlspecialchars($_POST['platform'], ENT_QUOTES);
                        $this->_programHandler = new ProgramHandler;
                        if($this->_programHandler->newProgram(array($id, $creator_id, $name, $scope, $date, $status, $platform_id))){
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "program-create");
                            $_SESSION['typeAlert'] = "success";
                            header('Location: ' . $this->_routes->url("programs"));
                            exit;
                        } else {
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->url("programs"));
                            exit;
                        }
                    }
                } else {
                    $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                    $_SESSION['typeAlert'] = "error";
                    header('Location: ' . $this->_routes->url("programs"));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function scope(){
            $term;
            if(isset($_GET['term'])){
                $term = htmlspecialchars($_GET['term'], ENT_QUOTES);
            }
            $array = array('test', 'pomme');
            echo json_encode($array);
        }

        
        private function postDataValid($token) {
            if($token != null){
                $this->_session = new Session;
                if(isset($_POST['token'])){
                    $postToken = htmlspecialchars($_POST['token'], ENT_QUOTES);
                    if($token == $postToken){
                        $this->_captcha = new Captcha;
                        $ReCaptchaValid = $this->_captcha->verifyCaptcha($_POST['g-recaptcha-response'], PRIVATE_KEY);
                        if($ReCaptchaValid == true){
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
                        $this->_captcha = new Captcha;
                        $ReCaptchaValid = $this->_captcha->verifyCaptcha($_POST['g-recaptcha-response'], PRIVATE_KEY);
                        if($ReCaptchaValid == true){
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