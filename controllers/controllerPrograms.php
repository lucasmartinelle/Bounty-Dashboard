<?php
    namespace controllers;

    require_once("views/View.php");
    require_once("models/userHandler.php");
    require_once("models/platformHandler.php");
    require_once("models/programHandler.php");
    require_once("models/reportHandler.php");
    require_once("models/templateHandler.php");
    require_once("utils/Validator.php");
    require_once("utils/Session.php");
    require_once("models/captchaHandler.php");
    require_once("app/Routes.php");
    require_once("app/languages/languageManager.php");
    require_once("models/billingHandler.php");

    use app\Routes;
    use Utils\Session;
    use Utils\Validator;
    use Models\CaptchaHandler;
    use Models\PlatformHandler;
    use Models\ProgramHandler;
    use view\View;
    use app\languages\languageManager;

    class controllerPrograms {
        private $_view;
        private $_session;
        private $_validator;
        private $_routes;
        private $_captchaHandler;
        private $_lang;
        private $_platformHandler;
        private $_programHandler;
        
        public function __construct($label, $name, $view, $template, $data){
            if($label == "programs"){
                $this->programs($name, $view, $template);
            } elseif($label == "deleteProgram"){
                $this->deleteProgram();
            } elseif($label == "scope"){
                $this->scope();
            } elseif($label == "tags"){
                $this->tags();
            }
        }

        private function programs($name, $view, $template){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_platformHandler = new platformHandler;
            $this->_programHandler = new ProgramHandler;
            $platforms = $this->_platformHandler->getPlatforms();
            if(isset($_SESSION['watchState']) && !empty($_SESSION['watchState']) && $_SESSION['watchState'] == 'me'){ 
                $programs = $this->_programHandler->getPrograms(array('creator_id' => htmlspecialchars($_SESSION['id'], ENT_QUOTES)));
            } else {
                $programs = $this->_programHandler->getPrograms();
            }
            $severity = $this->_programHandler->bugsBySeverity();
            $numberofbugs = array();
            $gain = array();
            foreach($programs as $program){
                array_push($numberofbugs, $this->_programHandler->countBugs($program->id()));
                array_push($gain, $this->_programHandler->getGains($program->id()));
            }
            if($_POST){
                $this->postPrograms($platforms);
            } else {
                if($this->_session->isAuth()){
                    $admin = $this->_session->isAdmin();
                    $token = $this->_session->getToken();
                    $this->_view = new View($view, $template);
                    $this->_view->generate(array("titre" => $name, "token" => $token, "admin" => $admin, "platforms" => $platforms, "programs" => $programs, "numberofbugs" => $numberofbugs, "gainsbyprograms" => $gain, "severity" => $severity));
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
                        $tags = htmlspecialchars($_POST['tags'], ENT_QUOTES);
                        $platform_id = htmlspecialchars($_POST['platform'], ENT_QUOTES);
                        $this->_programHandler = new ProgramHandler;
                        if($this->_programHandler->newProgram(array($id, $creator_id, $name, $scope, $date, $status, $tags, $platform_id))){
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

        private function deleteProgram($data){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            if($this->_session->isAuth()){
                $this->_programHandler = new ProgramHandler;
                $id = htmlspecialchars($data[2], ENT_QUOTES);
                $programs = $this->_programHandler->getPrograms(array("id" => $id));
                foreach($programs as $program){
                    if($this->_programHandler->deleteProgram($id)){
                        $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "program-delete");
                        $_SESSION['typeAlert'] = "success";
                        header('Location: ' . $this->_routes->url('programs'));
                        exit;
                    } else {
                        $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                        $_SESSION['typeAlert'] = "error";
                        header('Location: ' . $this->_routes->url('programs'));
                        exit;
                    }
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }


        private function scope(){
            $array = array();
            $this->_programHandler = new ProgramHandler; 
            $programs = $this->_programHandler->getPrograms();
            foreach($programs as $program){
                $scopes = explode("|", $program->scope());
                foreach($scopes as $scope){
                    if(!in_array($scope,$array)){
                        array_push($array, $scope);
                    }
                }
            }
            echo json_encode($array);
        }

        private function tags(){
            $array = array();
            $this->_programHandler = new ProgramHandler; 
            $programs = $this->_programHandler->getPrograms();
            foreach($programs as $program){
                $tags = explode("|", $program->tags());
                foreach($tags as $tag){
                    if(!in_array($tag,$array)){
                        array_push($array, $tag);
                    }
                }
            }
            echo json_encode($array);
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