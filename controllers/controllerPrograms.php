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
    require_once("models/notesHandler.php");

    use app\Routes;
    use Utils\Session;
    use Utils\Validator;
    use Models\CaptchaHandler;
    use Models\PlatformHandler;
    use Models\ProgramHandler;
    use Models\NotesHandler;
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
        private $_notesHandler;
        
        public function __construct($label, $name, $view, $template, $data){
            if($label == "programs"){
                $this->programs($name, $view, $template);
            } elseif($label == "deleteProgram"){
                $this->deleteProgram($data);
            } elseif($label == "changeStatusProgram"){
                $this->changeStatusProgram();
            } elseif($label == "programNote"){
                $this->notes($name, $view, $template, $data);
            } elseif($label == "deleteNote"){
                $this->deleteNotes($data);
            } elseif($label == "changeNote"){
                $this->changeNote($data);
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
            $platformsById = array();
            foreach($programs as $program){
                array_push($numberofbugs, $this->_programHandler->countBugs($program->id()));
                array_push($gain, $this->_programHandler->getGains($program->id()));
                array_push($platformsById, $this->_programHandler->getPlatform($program->platformid()));
            }
            if($_POST){
                $this->postPrograms($platforms);
            } else {
                if($this->_session->isAuth()){
                    $admin = $this->_session->isAdmin();
                    $token = $this->_session->getToken();
                    $this->_view = new View($view, $template);
                    $this->_view->generate(array("titre" => $name, "token" => $token, "admin" => $admin, "platforms" => $platforms, "programs" => $programs, "numberofbugs" => $numberofbugs, "gainsbyprograms" => $gain, "platformsById" => $platformsById, "severity" => $severity));
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
                        array("platform", $_POST['platform'], 'required', 'equal|'.$listPlatforms)
                    );

                    $this->_validator = new Validator();
                    $response = $this->_validator->validator($data);

                    if($response['success'] == 'false'){
                        $_SESSION['inputResponseName'] = $response['name'];
                        $_SESSION['inputResponseScope'] = $response['scope'];
                        $_SESSION['inputResponseDate'] = $response['date'];
                        $_SESSION['inputResponseStatus'] = $response['status'];
                        $_SESSION['inputResponsePlatform'] = $response['platform'];

                        if($response['name'] == 'invalid'){
                            $_SESSION['inputResponseNameMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['name'] as $e){
                                $_SESSION['inputResponseNameMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseNameMessage'] .= "</span>";
                        }

                        if($response['unique']['name'] == 'false'){
                            $_SESSION['inputResponseNameMessage'] = "<span class='text-danger'><i class='fas fa-circle' style='font-size: 8px;'></i> " . $this->_lang->getTxt('controllerReports', "name-taken") . " </span>";
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
                        $tags = (isset($_POST['tags']) && !empty(htmlspecialchars_decode($_POST['tags'], ENT_QUOTES))) ? htmlspecialchars($_POST['tags'], ENT_QUOTES) : NULL;
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

        private function changeStatusProgram(){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            $last_token = $this->_session->getToken();
            if($this->_session->isAuth()){
                if($this->postDataValid($last_token)){
                    $token = $this->_session->updateToken();

                    $data = array(
                        array("idProgram", $_POST['idProgram'], 'required', 'max:36'),
                        array("status", $_POST['status'], 'required', "max:100", "equal|open|close")
                    );

                    $this->_validator = new Validator();
                    $response = $this->_validator->validator($data);

                    if($response['success'] == 'false'){
                        $_SESSION['inputResponseStatus'] = $response['status'];

                        if($response['idProgram'] == 'invalid'){
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->url("programs"));
                            exit;
                        }

                        if($response['status'] == 'invalid'){
                            $_SESSION['inputResponseStatusMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['status'] as $e){
                                $_SESSION['inputResponseStatusMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseStatusMessage'] .= "</span>";
                        }

                        header('Location: ' . $this->_routes->url("programs"));
                        exit;
                    } else {
                        $id = htmlspecialchars($_POST['idProgram'], ENT_QUOTES);
                        $status = htmlspecialchars($_POST['status'], ENT_QUOTES);  
                        $this->_programHandler = new ProgramHandler;
                        if($this->_programHandler->updateProgram(array("status" => $status), array("id" => $id))){
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "status-change");
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

        private function notes($name, $view, $template, $data){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            $this->_programHandler = new ProgramHandler;
            $id = htmlspecialchars($data[3]);
            $programs = $this->_programHandler->getPrograms(array("id" => $id));
            $exist = False;
            foreach($programs as $program){
                $exist = True;
                if($_POST){
                    $this->postNotes($id);
                } else {
                    if($this->_session->isAuth()){
                        $this->_notesHandler = new NotesHandler;
                        $notes = $this->_notesHandler->getNotes(array("program_id" => $id));
                        $token = $this->_session->getToken();
                        $this->_view = new View($view, $template);
                        $this->_view->generate(array("titre" => $name, "token" => $token, "program" => $program->name(), "notes" => $notes));
                    } else {
                        header('Location: ' . $this->_routes->url('login'));
                        exit;
                    }
                }
            }
            if(!$exist){
                $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                $_SESSION['typeAlert'] = "error";
                header('Location: ' . $this->_routes->url("programs"));
                exit;
            }
        }

        private function postNotes($id){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            $last_token = $this->_session->getToken();
            if($this->_session->isAuth()){
                if($this->postDataValid($last_token)){
                    $_SESSION['inputValueTitle'] = htmlspecialchars($_POST['title'], ENT_QUOTES);
                    $_SESSION['inputValueMessage'] = htmlspecialchars($_POST['message'], ENT_QUOTES);

                    $token = $this->_session->updateToken();

                    $data = array(
                        array("title", $_POST['title'], 'required'),
                        array("messages", $_POST['message'], 'required'),
                    );

                    $this->_validator = new Validator();
                    $response = $this->_validator->validator($data);

                    if($response['success'] == 'false'){
                        $_SESSION['inputResponseTitle'] = $response['title'];
                        $_SESSION['inputResponseMessage'] = $response['messages'];
                        

                        if($response['title'] == 'invalid'){
                            $_SESSION['inputResponseTitleMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['title'] as $e){
                                $_SESSION['inputResponseTitleMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseTitleMessage'] .= "</span>";
                        }

                        if($response['messages'] == 'invalid'){
                            $_SESSION['inputResponseMessageMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['messages'] as $e){
                                $_SESSION['inputResponseMessageMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseMessageMessage'] .= "</span>";
                        }

                        header('Location: ' . $this->_routes->urlReplace("programNote", array($id)));
                        exit;
                    } else {
                        $this->_notesHandler = new NotesHandler;
                        $uid = $this->GUIDv4();
                        $title = htmlspecialchars($_POST['title'], ENT_QUOTES);
                        $message = htmlspecialchars($_POST['message'], ENT_QUOTES);
                        if($this->_notesHandler->newNote(array($uid,$id,$title,$message))){
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "add-note");
                            $_SESSION['typeAlert'] = "success";
                            header('Location: ' . $this->_routes->urlReplace("programNote", array($id)));
                            exit;
                        } else {
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->urlReplace("programNote", array($id)));
                            exit;
                        }
                    }
                } else {
                    $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                    $_SESSION['typeAlert'] = "error";
                    header('Location: ' . $this->_routes->urlReplace("programNote", array($id)));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url("login"));
                exit;
            }
        }

        private function deleteNotes($data){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            if($this->_session->isAuth()){
                $this->_notesHandler = new NotesHandler;
                $id = htmlspecialchars($data[3], ENT_QUOTES);
                echo $id;
                $notes = $this->_notesHandler->getNotes(array("id" => $id));
                foreach($notes as $note){
                    if($this->_notesHandler->deleteNotes($id)){
                        $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "note-delete");
                        $_SESSION['typeAlert'] = "success";
                        header('Location: ' . $this->_routes->url('programs'));
                        exit;
                    } else {
                        $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                        $_SESSION['typeAlert'] = "error";
                        header('Location: ' . $this->_routes->urlReplace('programNote', array($id)));
                        exit;
                    }
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function changeNote($data){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            $this->_notesHandler = new NotesHandler;
            $id = htmlspecialchars($data[3], ENT_QUOTES);
            $last_token = $this->_session->getToken();
            if($this->_session->isAuth()){  
                $exist = false;
                $notes = $this->_notesHandler->getNotes(array("id" => $id));
                foreach($notes as $note){
                    $exist = true;
                    if($_POST){
                        if($this->postDataValid($last_token)){
                            $token = $this->_session->updateToken();
                            $_SESSION['inputValueTitle2'] = htmlspecialchars($_POST['title'], ENT_QUOTES);
                            $_SESSION['inputValueMessage2'] = htmlspecialchars($_POST['message'], ENT_QUOTES);

                            $data = array(
                                array("title", $_POST['title'], "max:255")
                            );

                            $this->_validator = new Validator();
                            $response = $this->_validator->validator($data);

                            if($response['success'] == 'false'){
                                $_SESSION['inputResponseTitle2'] = $response['title'];

                                if($response['title'] == 'invalid'){
                                    $_SESSION['inputResponseTitleMessage2'] = "<span class='text-danger'>";
                                    foreach($response['message']['title'] as $e){
                                        $_SESSION['inputResponseTitleMessage2'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                    }
                                    $_SESSION['inputResponseTitleMessage2'] .= "</span>";
                                }

                                header('Location: ' . $this->_routes->urlReplace("programNote", array($note->programid())));
                                exit;
                            } else {
                                $title = (isset($_POST['title']) && !empty($_POST['title'])) ? htmlspecialchars($_POST['title'], ENT_QUOTES) : null;
                                $message = (isset($_POST['message']) && !empty($_POST['message'])) ? htmlspecialchars($_POST['message'], ENT_QUOTES) : null;
                                
                                if($this->_notesHandler->updateNote(array("titre" => $title, "text" => $message), array("id" => $id))){
                                    $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "note-change");
                                    $_SESSION['typeAlert'] = "success";
                                    header('Location: ' . $this->_routes->urlReplace("programNote", array($note->programid())));
                                    exit;
                                } else {
                                    $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                                    $_SESSION['typeAlert'] = "error";
                                    header('Location: ' . $this->_routes->urlReplace("programNote", array($note->programid())));
                                    exit;
                                }
                            }
                        } else {
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->urlReplace("programNote", array($note->programid())));
                            exit;
                        }
                    } else {
                        $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                        $_SESSION['typeAlert'] = "error";
                        header('Location: ' . $this->_routes->urlReplace("programNote", array($note->programid())));
                        exit;
                    }
                }
                if(!$exist){
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