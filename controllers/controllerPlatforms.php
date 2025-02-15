<?php
    namespace controllers;

    require_once("views/View.php");
    require_once("models/userHandler.php");
    require_once("models/platformHandler.php");
    require_once("models/reportHandler.php");
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
    use Models\ReportHandler;
    use view\View;
    use app\languages\languageManager;

    class controllerPlatforms {
        private $_view;
        private $_session;
        private $_validator;
        private $_routes;
        private $_captchaHandler;
        private $_lang;
        private $_platformHandler;
        private $_reportHandler;
        
        public function __construct($label, $name, $view, $template, $data){
            if($label == "platforms"){
                $this->platforms($name, $view, $template);
            } elseif($label == "deletePlatform"){
                $this->deletePlatform();
            } elseif($label == "filterEarningPerMonth"){
                $this->filterEarningPerMonth();
            }
        }

        private function platforms($name, $view, $template){
            $this->_session = new Session;
            $this->_routes = new Routes;
            if($_POST){
                $this->postPlatforms();
            } else {
                if($this->_session->isAuth()){
                    $this->_platformHandler = new platformHandler;
                    $this->_reportHandler = new ReportHandler;
                    $admin = $this->_session->isAdmin();
                    $token = $this->_session->getToken();
                    $platforms = $this->_platformHandler->getPlatforms();
                    $year = (isset($_SESSION['filterYear']) && !empty($_SESSION['filterYear'])) ? htmlspecialchars($_SESSION['filterYear'], ENT_QUOTES) : null;
                    $earningpermonth = $this->_reportHandler->earningpermonth($year);
                    $severity = $this->_reportHandler->bugsBySeverity();
                    $this->_view = new View($view, $template);
                    $this->_view->generate(array("titre" => $name, "token" => $token, "admin" => $admin, "platforms" => $platforms, "earningpermonth" => $earningpermonth, "severity" => $severity, "informationFilterYear" => $year));
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

                    $token = $this->_session->updateToken();

                    $data = array(
                        array('name', $_POST['name'], 'required', 'max:200', "unique|platforms|name"),
                        array('email', $_POST['email'], 'email'),
                        array('date', $_POST['date'], 'date')
                    );

                    $this->_validator = new Validator();
                    $response = $this->_validator->validator($data);

                    if($response['success'] == 'false'){
                        $_SESSION['inputResponseName'] = $response['name'];

                        if($response['name'] == 'invalid'){
                            $_SESSION['inputResponseNameMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['name'] as $e){
                                $_SESSION['inputResponseNameMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseNameMessage'] .= "</span>";
                        }

                        if($response['email'] == 'invalid'){
                            $_SESSION['inputResponseEmail'] = $response['email'];
                            $_SESSION['inputResponseEmailMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['email'] as $e){
                                $_SESSION['inputResponseEmailMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseEmailMessage'] .= "</span>";
                        }

                        if($response['date'] == 'invalid'){
                            $_SESSION['inputResponseDate'] = $response['date'];

                            if($response['date'] == 'invalid'){
                                $_SESSION['inputResponseDateMessage'] = "<span class='text-danger'>";
                                foreach($response['message']['date'] as $e){
                                    $_SESSION['inputResponseDateMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                }
                                $_SESSION['inputResponseDateMessage'] .= "</span>";
                            }
                        }

                        if($response['unique']['name'] == 'false'){
                            $_SESSION['inputResponseNameMessage'] = "<span class='text-danger'><i class='fas fa-circle' style='font-size: 8px;'></i> " . $this->_lang->getTxt('controllerReports', "name-taken") . " </span>";
                        }

                        header('Location: ' . $this->_routes->url("platforms"));
                        exit;
                    } else {
                        $id = $this->GUIDv4();
                        $creator_id = htmlspecialchars($_SESSION['id'], ENT_QUOTES);
                        $name = htmlspecialchars($_POST['name'], ENT_QUOTES);
                        $columns = array("id", "creator_id", "name");
                        $values = array($id, $creator_id, $name);
                        if(isset($_POST['client']) && !empty($_POST['client'])){
                            array_push($columns, 'client');
                            array_push($values, htmlspecialchars($_POST['client'], ENT_QUOTES));
                        }
                        if(isset($_POST['BTW']) && !empty($_POST['BTW'])){
                            array_push($columns, 'BTW');
                            array_push($values, htmlspecialchars($_POST['BTW'], ENT_QUOTES));
                        }
                        if(isset($_POST['address']) && !empty($_POST['address'])){
                            array_push($columns, 'address');
                            array_push($values, htmlspecialchars($_POST['address'], ENT_QUOTES));
                        }
                        if(isset($_POST['email']) && !empty($_POST['email'])){
                            array_push($columns, 'email');
                            array_push($values, htmlspecialchars($_POST['email'], ENT_QUOTES));
                        }
                        if(isset($_POST['date']) && !empty($_POST['date'])){
                            array_push($columns, 'date');
                            array_push($values, htmlspecialchars($_POST['date'], ENT_QUOTES));
                        }
                        $this->_platformHandler = new platformHandler;
                        if($this->_platformHandler->newPlatform($columns, $values)){
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

        private function deletePlatform(){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            $last_token = $this->_session->getToken();
            if($this->_session->isAuth()){
                if($this->_session->isAdmin()){
                    if($this->postDataValid($last_token)){
                        $this->_platformHandler = new platformHandler;
                        $platforms = $this->_platformHandler->getPlatforms();
                        $listPlatforms = "";
                        foreach($platforms as $platform){
                            $listPlatforms .= $platform->id() . "|";
                        }
                        $listPlatforms = substr($listPlatforms, 0, -1);
    
                        $token = $this->_session->updateToken();
    
                        $data = array(
                            array("platform", $_POST['platform'], 'required', 'equal|'.$listPlatforms)
                        );
    
                        $this->_validator = new Validator();
                        $response = $this->_validator->validator($data);
    
                        if($response['success'] == 'false'){
                            $_SESSION['inputResponsePlatform'] = $response['platform'];

                            if($response['platform'] == 'invalid'){
                                $_SESSION['inputResponsePlatformMessage'] = "<span class='text-danger'>";
                                foreach($response['message']['platform'] as $e){
                                    $_SESSION['inputResponsePlatformMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                }
                                $_SESSION['inputResponsePlatformMessage'] .= "</span>";
                            }

                            header('Location: ' . $this->_routes->url("platforms"));
                            exit;
                        } else {
                            $id = htmlspecialchars($_POST['platform'], ENT_QUOTES);
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
                        }
                    } else {
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

        private function filterEarningPerMonth(){
            $this->_routes = new Routes;
            $this->_session = new Session;
            $this->_lang = new languageManager;
            if(!$_POST){
                header('Location: ' . $this->_routes->url("platforms"));
                exit;
            } else {
                $last_token = $this->_session->getToken();
                if($this->_session->isAuth()){
                    if($this->postDataValid($last_token)){
                        $values = '';
                        for($i=2020; $i <= (int) date('Y'); $i++){
                            $values.=$i.'|';
                        }
                        $values = substr($values, 0, -1);
                        $data = array(
                            array("year", $_POST['year'], 'required', 'equal|all|'.$values)
                        );

                        $token = $this->_session->updateToken();

                        $this->_validator = new Validator();
                        $response = $this->_validator->validator($data);

                        if($response['success'] == 'false'){
                            $_SESSION['inputResponseYear'] = $response['year'];

                            if($response['year'] == 'invalid'){
                                $_SESSION['inputResponseYearMessage'] = "<span class='text-danger'>";
                                foreach($response['message']['year'] as $e){
                                    $_SESSION['inputResponseYearMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                }
                                $_SESSION['inputResponseYearMessage'] .= "</span>";
                            }

                            header('Location: ' . $this->_routes->url("platforms"));
                            exit;
                        } else {
                            $year = htmlspecialchars($_POST['year'], ENT_QUOTES);
                            $_SESSION['filterYear'] = $year;
                            header('Location: ' . $this->_routes->url("platforms"));
                            exit;
                        }
                    } else {
                        $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                        $_SESSION['typeAlert'] = "error";
                        header('Location: ' . $this->_routes->url("platforms"));
                        exit;
                    }
                } else {
                    header('Location: ' . $this->_routes->url("login"));
                    exit;
                }
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