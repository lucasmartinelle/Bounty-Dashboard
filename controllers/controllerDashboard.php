<?php
    namespace controllers;

    require_once("views/View.php");
    require_once("models/userHandler.php");
    require_once("utils/Validator.php");
    require_once("utils/Session.php");
    require_once("models/captchaHandler.php");
    require_once("app/Routes.php");
    require_once("app/languages/languageManager.php");
    require_once("models/billingHandler.php");
    require_once("models/reportHandler.php");
    require_once("models/platformHandler.php");
    require_once("models/programHandler.php");

    use app\Routes;
    use Utils\Session;
    use Utils\Validator;
    use Models\CaptchaHandler;
    use Models\UserHandler;
    use Models\BillingHandler;
    use Models\ReportHandler;
    use Models\PlatformHandler;
    use Models\ProgramHandler;
    use view\View;
    use app\languages\languageManager;

    class controllerDashboard {
        private $_view;
        private $_session;
        private $_userHandler;
        private $_validator;
        private $_routes;
        private $_captchaHandler;
        private $_lang;
        private $_billingHandler; 
        private $_platformHandler;
        private $_programHandler;
        private $_reportHandler;
        
        public function __construct($label, $name, $view, $template, $data){
            if($label == "dashboard"){
                $this->dashboard($name, $view, $template);
            } elseif($label == "settings"){
                $this->settings($name,$view,$template);
            } elseif($label == "adduser"){
                $this->adduser();
            } elseif($label == "removeuser"){
                $this->removeuser($data);
            } elseif($label == "profile"){
                $this->profile($name,$view,$template);
            } elseif($label == "changeUsername"){
                $this->changeUsername();
            } elseif($label == "changeEmail"){
                $this->changeEmail();
            } elseif($label == "changePassword"){
                $this->changePassword();
            } elseif($label == "changeBilling"){
                $this->changeBilling();
            } elseif($label == "changeWatchState"){
                $this->changeWatchState();
            } elseif($label == "changeCaptchaKey"){
                $this->changeCaptchaKey();
            }
        }

        private function dashboard($name, $view, $template){
            if($_POST){
                $this->postDashboard();
            } else {
                $this->_session = new Session;
                $this->_routes = new Routes;
                if($this->_session->isAuth()){
                    $this->_reportHandler = new ReportHandler;
                    $this->_programHandler = new ProgramHandler;
                    $this->_platformHandler = new platformHandler;
                    $new = $this->_reportHandler->countReports(array('status' => 'new'));
                    $other = $this->_reportHandler->countReports(array('status' => 'resolved'), array('accepted', 'resolved'));
                    $gain = $this->_reportHandler->totalGain();
                    $critical = $this->_reportHandler->countReports(array('severity' => '9'), null, true);
                    $platforms = $this->_reportHandler->bugsByPlatforms();
                    $filterProgram = null;
                    $filterProgramInfo = null;
                    $filterPlatform = null;
                    $filterPlatformInfo = null;
                    $filterPlatform2 = null;
                    $filterPlatform2Info = null;
                    $token = $this->_session->updateToken();
                    if(isset($_SESSION['filterProgram']) && !empty($_SESSION['filterProgram'])){
                        $filterProgram = htmlspecialchars($_SESSION['filterProgram'], ENT_QUOTES);
                        $filterProgramInfo = $this->_programHandler->getPrograms(array('id' => $filterProgram))[0]->name();
                    }
                    if(isset($_SESSION['filterPlatform']) && !empty($_SESSION['filterPlatform'])){
                        $filterPlatform = htmlspecialchars($_SESSION['filterPlatform'], ENT_QUOTES);
                        $filterPlatformInfo = $this->_platformHandler->getPlatforms(array('id' => $filterPlatform))[0]->name();
                    }
                    if(isset($_SESSION['filterPlatform2']) && !empty($_SESSION['filterPlatform2'])){
                        $filterPlatform2 = htmlspecialchars($_SESSION['filterPlatform2'], ENT_QUOTES);
                        $filterPlatform2Info = $this->_platformHandler->getPlatforms(array('id' => $filterPlatform2))[0]->name();
                    }
                    $severity = $this->_reportHandler->bugsBySeverity($filterProgram, $filterPlatform);
                    $dates = $this->_reportHandler->bugsByMonth($filterPlatform2);
                    $platformsFilter = $this->_platformHandler->getPlatforms();
                    $programsFilter = $this->_programHandler->getPrograms();
                    $this->_view = new View($view, $template);
                    $this->_view->generate(array("titre" => $name, 'new' => $new, 'token' => $token, 'other' => $other, "gain" => $gain, "critical" => $critical, "platforms" => $platforms, "severity" => $severity, 'dates' => $dates, 'platformsFilter' => $platformsFilter, 'programsFilter' => $programsFilter, 'informationFilterPlatforms' => $filterPlatformInfo, 'informationFilterPrograms' => $filterProgramInfo, 'informationFilterPlatform2' => $filterPlatform2Info));
                } else {
                    header('Location: ' . $this->_routes->url('login'));
                    exit;
                }
            }
        }

        private function postDashboard(){
            $this->_routes = new Routes;
            $this->_session = new Session;
            $last_token = $this->_session->getToken();
            if($this->_session->isAuth()){
                if(!$_POST){
                    header('Location: ' . $this->_routes->url("dashboard"));
                    exit;
                } else {
                    if($this->postDataValid($last_token)){
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
                            array("platform2", $_POST['platform2'], 'equal|'.$listPlatforms)
                        );

                        $token = $this->_session->updateToken();

                        $this->_validator = new Validator();
                        $response = $this->_validator->validator($data);

                        if($response['success'] == 'false'){
                            $_SESSION['inputResponseProgram'] = $response['program'];
                            $_SESSION['inputResponsePlatform'] = $response['platform'];
                            $_SESSION['inputResponsePlatform2'] = $response['platform2'];

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

                            if($response['platform2'] == 'invalid'){
                                $_SESSION['inputResponsePlatformMessage2'] = "<span class='text-danger'>";
                                foreach($response['message']['platform2'] as $e){
                                    $_SESSION['inputResponsePlatformMessage2'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                }
                                $_SESSION['inputResponsePlatformMessage2'] .= "</span>";
                            }

                            header('Location: ' . $this->_routes->url("dashboard"));
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
                            $platform2 = htmlspecialchars($_POST['platform2'], ENT_QUOTES);
                            if(empty($platform2) || !isset($platform2)){
                                $platform2 = null;
                            }

                            $_SESSION['filterProgram'] = $program;
                            $_SESSION['filterPlatform'] = $platform;
                            $_SESSION['filterPlatform2'] = $platform2;

                            header('Location: ' . $this->_routes->url("dashboard"));
                            exit;
                        }
                    }
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function settings($name, $view, $template){
            $this->_session = new Session;
            $this->_routes = new Routes;
            if($_POST){
                $this->postSettings();
            } else {
                if($this->_session->isAuth()){
                    $this->_userHandler = new UserHandler;
                    $admin = $this->_session->isAdmin();
                    $users = $this->_userHandler->getUsers();
                    $token = $this->_session->updateToken();
                    $this->_view = new View($view, $template);
                    $this->_view->generate(array("titre" => $name, "users" => $users, "token" => $token, "admin" => $admin));
                } else {
                    header('Location: ' . $this->_routes->url('login'));
                    exit;
                }
            }
        }

        protected function postSettings(){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            $last_token = $this->_session->getToken();
            if($this->_session->isAuth()){
                if($this->postDataValid($last_token)){
                    
                    $token = $this->_session->updateToken();

                    $data = array(
                        array('language', $_POST['language'], 'equal|en|fr')
                    );

                    $this->_validator = new Validator();
                    $response = $this->_validator->validator($data);

                    if($response['success'] == 'false'){
                        $_SESSION['inputResponseLanguage'] = $response['language'];

                        if($response['language'] == 'invalid'){
                            $_SESSION['inputResponseRoleMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['language'] as $e){
                                $_SESSION['inputResponseLanguageMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseLanguageMessage'] .= "</span>";
                        }

                        header('Location: ' . $this->_routes->url("settings"));
                        exit;
                    } else {
                        $language = strtoupper(htmlspecialchars($_POST['language'], ENT_QUOTES));
                        $id = htmlspecialchars($_SESSION['id'], ENT_QUOTES);
                        setcookie('lang', $language, time() + (86400 * 30), "/");
                        $this->_userHandler = new UserHandler;
                        if($this->_userHandler->updateUser(array('lang' => $language), array('id' => $id))){
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "language-change");
                            $_SESSION['typeAlert'] = "success";
                            header('Location: ' . $this->_routes->url("settings"));
                            exit;
                        } else {
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->url("settings"));
                            exit;
                        }
                    }
                } else {
                    $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                    $_SESSION['typeAlert'] = "error";
                    header('Location: ' . $this->_routes->url("settings"));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url("login"));
                exit;
            }
        }

        private function adduser(){
            $this->_session = new Session;
            $last_token = $this->_session->getToken();
            $this->_routes = new Routes;
            if($this->_session->isAdmin()){
                if($this->_session->isAuth()){
                    if($_POST){
                        $this->_lang = new languageManager;
                        if($this->postDataValid($last_token)){
                            $this->_userHandler = new UserHandler;
                            $_SESSION['inputValueUsername'] = htmlspecialchars($_POST['username'], ENT_QUOTES);
                            $_SESSION['inputValueEmail'] = htmlspecialchars($_POST['email'], ENT_QUOTES);

                            $token = $this->_session->updateToken();

                            $data = array(
                                array('username', $_POST['username'], 'required', 'max:200'),
                                array('email', $_POST['email'], 'required', 'min:3', 'max:255', 'email', 'unique|users|email'),
                                array('password', $_POST['password'], 'cpassword:'.$_POST['cpassword'], 'required', 'min:6', 'max:32', 'requiredSpecialCharacter', 'requiredNumber', 'requiredLetter'),
                                array('role', $_POST['role'], 'required', 'equal|admin|hunter')
                            );

                            $this->_validator = new Validator();
                            $response = $this->_validator->validator($data);

                            if($response['success'] == 'false'){
                                // register validity of input
                                $_SESSION['inputResponseUsername'] = $response['username'];
                                $_SESSION['inputResponseEmail'] = $response['email'];
                                $_SESSION['inputResponsePassword'] = $response['password'];
                                $_SESSION['inputResponseCPassword'] = $response['cpassword'];
                                $_SESSION['inputResponseRole'] = $response['role'];

                                // register error message by input
                                if($_SESSION['inputResponseUsername'] == 'invalid'){
                                    $_SESSION['inputResponseUsernameMessage'] = "<span class='text-danger'>";
                                    foreach($response['message']['username'] as $e){
                                        $_SESSION['inputResponseUsernameMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                    }
                                    $_SESSION['inputResponseUsernameMessage'] .= "</span>";
                                }

                                if($_SESSION['inputResponseEmail'] == 'invalid'){
                                    $_SESSION['inputResponseEmailMessage'] = "<span class='text-danger'>";
                                    foreach($response['message']['email'] as $e){
                                        $_SESSION['inputResponseEmailMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                    }
                                    $_SESSION['inputResponseEmailMessage'] .= "</span>";
                                }

                                if($response['unique']['email'] == 'false'){
                                    $_SESSION['inputResponseEmailMessage'] = "<span class='text-danger'><i class='fas fa-circle' style='font-size: 8px;'></i> " . $this->_lang->getTxt('controllerDashboard', "email-taken") . " </span>";
                                }

                                if($response['password'] == 'invalid'){
                                    $_SESSION['inputResponsePasswordMessage'] = "<span class='text-danger'>";
                                    foreach($response['message']['password'] as $e){
                                        $_SESSION['inputResponsePasswordMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                    }
                                    $_SESSION['inputResponsePasswordMessage'] .= "</span>";
                                }

                                if($response['cpassword'] == 'invalid'){
                                    $_SESSION['inputResponseCPasswordMessage'] = "<span class='text-danger'>";
                                    foreach($response['message']['cpassword'] as $e){
                                        $_SESSION['inputResponseCPasswordMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                    }
                                    $_SESSION['inputResponseCPasswordMessage'] .= "</span>";
                                }

                                if($response['role'] == 'invalid'){
                                    $_SESSION['inputResponseRoleMessage'] = "<span class='text-danger'>";
                                    foreach($response['message']['role'] as $e){
                                        $_SESSION['inputResponseRoleMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                    }
                                    $_SESSION['inputResponseRoleMessage'] .= "</span>";
                                }

                                header('Location: ' . $this->_routes->url("settings"));
                                exit;
                            } else {
                                $id = $this->GUIDv4();
                                $username = htmlspecialchars($_POST['username'], ENT_QUOTES);
                                $email = htmlspecialchars($_POST['email'], ENT_QUOTES);
                                $password = password_hash(htmlspecialchars($_POST['password'], ENT_QUOTES), PASSWORD_ARGON2ID);
                                $role = htmlspecialchars($_POST['role'], ENT_QUOTES);

                                try {
                                    if($this->_userHandler->newUser(array($id, $username, $email, $password, $token, $role, 'Y'))) {
                                        $_SESSION['alert'] = $this->_lang->getTxt('controllerAuth', "user-created");
                                        $_SESSION['typeAlert'] = "success";
                                        header('Location: ' . $this->_routes->url("settings"));
                                        exit;
                                    } else {
                                        $_SESSION['alert'] = $this->_lang->getTxt('controllerAuth', "global-error");
                                        $_SESSION['typeAlert'] = "error";
                                        header('Location: ' . $this->_routes->url("settings"));
                                        exit;
                                    }
                                } catch (Exception $e){
                                    $_SESSION['alert'] = $this->_lang->getTxt('controllerAuth', "global-error");
                                    $_SESSION['typeAlert'] = "error";
                                    header('Location: ' . $this->_routes->url("settings"));
                                    exit;
                                }
                            }
                        } else {
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->url("settings"));
                            exit;
                        }
                    } else {
                        header('Location: ' . $this->_routes->url('dashboard'));
                        exit;
                    }
                } else {
                    header('Location: ' . $this->_routes->url('login'));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url('dashboard'));
                exit;
            }
        }

        private function removeuser($data){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            if($this->_session->isAdmin()){
                if($this->_session->isAuth()){
                    $this->_userHandler = new UserHandler;
                    $id = htmlspecialchars($data[3], ENT_QUOTES);
                    $users = $this->_userHandler->getUsers(array("id" => $id));
                    foreach($users as $user){
                        if($user->role() != "admin"){
                            if($this->_userHandler->deleteUser(array("id" => $id))){
                                $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "delete-user");
                                $_SESSION['typeAlert'] = "success";
                                header('Location: ' . $this->_routes->url('settings'));
                                exit;
                            } else {
                                $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                                $_SESSION['typeAlert'] = "error";
                                header('Location: ' . $this->_routes->url('settings'));
                                exit;
                            }
                        } else {
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->url('settings'));
                            exit;
                        }
                    }
                } else {
                    header('Location: ' . $this->_routes->url('login'));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url('settings'));
                exit;
            }
        }

        
        private function profile($name, $view, $template){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_userHandler = new UserHandler;
            if($this->_session->isAuth()){
                $users = $this->_userHandler->getUsers(array('id' => htmlspecialchars($_SESSION['id'], ENT_QUOTES)));
                $billingActivate = false;
                foreach($users as $user){
                    if($user->activebilling() == 'Y'){
                        $billingActivate = true;
                    }
                }
                $token = $this->_session->updateToken();
                $this->_view = new View($view, $template);
                $this->_view->generate(array("titre" => $name, "token" => $token, "billing" => $billingActivate));
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function changeUsername() {
            $this->_session = new Session;
            $last_token = $this->_session->getToken();
            $this->_routes = new Routes;
            if($this->_session->isAuth()){
                if($_POST){
                    $this->_lang = new languageManager;
                    if($this->postDataValid($last_token)){
                        $this->_userHandler = new UserHandler;
                        $_SESSION['inputValueUsername'] = htmlspecialchars($_POST['username'], ENT_QUOTES);
                        $token = $this->_session->updateToken();

                        $data = array(
                            array('username', $_POST['username'], 'required', 'max:200')
                        );

                        $this->_validator = new Validator();
                        $response = $this->_validator->validator($data);

                        if($response['success'] == 'false'){
                            // register validity of input
                            $_SESSION['inputResponseUsername'] = $response['username'];

                            // register error message by input
                            if($_SESSION['inputResponseUsername'] == 'invalid'){
                                $_SESSION['inputResponseUsernameMessage'] = "<span class='text-danger'>";
                                foreach($response['message']['username'] as $e){
                                    $_SESSION['inputResponseUsernameMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                }
                                $_SESSION['inputResponseUsernameMessage'] .= "</span>";
                            }

                            header('Location: ' . $this->_routes->url("profile"));
                            exit;
                        } else {
                            $id = htmlspecialchars($_SESSION['id'], ENT_QUOTES);
                            $username = htmlspecialchars($_POST['username'], ENT_QUOTES);

                            try {
                                if($this->_userHandler->updateUser(array('username' => $username, "updated_at" => date('Y-m-d H:i:s')), array('id' => $id))){
                                    $_SESSION['username'] = $username;
                                    $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "username-change");
                                    $_SESSION['typeAlert'] = "success";
                                    header('Location: ' . $this->_routes->url("profile"));
                                    exit;
                                } else {
                                    $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                                    $_SESSION['typeAlert'] = "error";
                                    header('Location: ' . $this->_routes->url("profile"));
                                    exit;
                                }
                            } catch(Exception $e){
                                $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                                $_SESSION['typeAlert'] = "error";
                                header('Location: ' . $this->_routes->url("profile"));
                                exit;
                            }
                        }
                    } else {
                        $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                        $_SESSION['typeAlert'] = "error";
                        header('Location: ' . $this->_routes->url("profile"));
                        exit;
                    }
                } else {
                    header('Location: ' . $this->_routes->url('profile'));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function changeEmail() {
            $this->_session = new Session;
            $last_token = $this->_session->getToken();
            $this->_routes = new Routes;
            if($this->_session->isAuth()){
                if($_POST){
                    $this->_lang = new languageManager;
                    if($this->postDataValid($last_token)){
                        $this->_userHandler = new UserHandler;
                        $_SESSION['inputValueEmail'] = htmlspecialchars($_POST['email'], ENT_QUOTES);
                        $token = $this->_session->updateToken();

                        $data = array(
                            array('email', $_POST['email'], 'required', 'min:3', 'max:255', 'email', 'unique|users|email')
                        );

                        $this->_validator = new Validator();
                        $response = $this->_validator->validator($data);

                        if($response['success'] == 'false'){
                            // register validity of input
                            $_SESSION['inputResponseEmail'] = $response['email'];

                            // register error message by input
                            if($_SESSION['inputResponseEmail'] == 'invalid'){
                                $counterror = 0;
                                $_SESSION['inputResponseEmailMessage'] = "<span class='text-danger'>";
                                foreach($response['message']['email'] as $e){
                                    $counterror++;
                                    $_SESSION['inputResponseEmailMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                }
                                if($counterror == 0){
                                    if($response['unique']['email'] == 'false'){
                                        $_SESSION['inputResponseEmailMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> ". $this->_lang->getTxt('controllerDashboard', "not-unique") ." <br>";
                                    }
                                }
                                $_SESSION['inputResponseEmailMessage'] .= "</span>";
                            }

                            header('Location: ' . $this->_routes->url("profile"));
                            exit;
                        } else {
                            $id = htmlspecialchars($_SESSION['id'], ENT_QUOTES);
                            $email = htmlspecialchars($_POST['email'], ENT_QUOTES);

                            try {
                                if($this->_userHandler->updateUser(array('email' => $email, "updated_at" => date('Y-m-d H:i:s')), array('id' => $id))){
                                    $_SESSION['email'] = $email;
                                    $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "email-change");
                                    $_SESSION['typeAlert'] = "success";
                                    header('Location: ' . $this->_routes->url("profile"));
                                    exit;
                                } else {
                                    $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                                    $_SESSION['typeAlert'] = "error";
                                    header('Location: ' . $this->_routes->url("profile"));
                                    exit;
                                }
                            } catch(Exception $e){
                                $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                                $_SESSION['typeAlert'] = "error";
                                header('Location: ' . $this->_routes->url("profile"));
                                exit;
                            }
                        }
                    } else {
                        $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                        $_SESSION['typeAlert'] = "error";
                        header('Location: ' . $this->_routes->url("profile"));
                        exit;
                    }
                } else {
                    header('Location: ' . $this->_routes->url('profile'));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function changePassword() {
            $this->_session = new Session;
            $last_token = $this->_session->getToken();
            $this->_routes = new Routes;
            if($this->_session->isAuth()){
                if($_POST){
                    $this->_lang = new languageManager;
                    if($this->postDataValid($last_token)){
                        $this->_userHandler = new UserHandler;
                        $token = $this->_session->updateToken();

                        $data = array(
                            array('password', $_POST['password'], 'cpassword:'.$_POST['cpassword'], 'required', 'min:6', 'max:32', 'requiredSpecialCharacter', 'requiredNumber', 'requiredLetter')
                        );

                        $this->_validator = new Validator();
                        $response = $this->_validator->validator($data);

                        if($response['success'] == 'false'){
                            // register validity of input
                            $_SESSION['inputResponsePassword'] = $response['password'];
                            $_SESSION['inputResponseCPassword'] = $response['cpassword'];

                            if($response['password'] == 'invalid'){
                                $_SESSION['inputResponsePasswordMessage'] = "<span class='text-danger'>";
                                foreach($response['message']['password'] as $e){
                                    $_SESSION['inputResponsePasswordMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                }
                                $_SESSION['inputResponsePasswordMessage'] .= "</span>";
                            }
        
                            if($response['cpassword'] == 'invalid'){
                                $_SESSION['inputResponseCPasswordMessage'] = "<span class='text-danger'>";
                                foreach($response['message']['cpassword'] as $e){
                                    $_SESSION['inputResponseCPasswordMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                }
                                $_SESSION['inputResponseCPasswordMessage'] .= "</span>";
                            }

                            header('Location: ' . $this->_routes->url("profile"));
                            exit;
                        } else {
                            $id = htmlspecialchars($_SESSION['id'], ENT_QUOTES);
                            $password = password_hash(htmlspecialchars($_POST['password'], ENT_QUOTES), PASSWORD_ARGON2ID);

                            try {
                                if($this->_userHandler->updateUser(array('password' => $password, "updated_at" => date('Y-m-d H:i:s')), array('id' => $id))){
                                    $_SESSION['password'] = $password;
                                    $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "password-change");
                                    $_SESSION['typeAlert'] = "success";
                                    header('Location: ' . $this->_routes->url("profile"));
                                    exit;
                                } else {
                                    $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                                    $_SESSION['typeAlert'] = "error";
                                    header('Location: ' . $this->_routes->url("profile"));
                                    exit;
                                }
                            } catch(Exception $e){
                                $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                                $_SESSION['typeAlert'] = "error";
                                header('Location: ' . $this->_routes->url("profile"));
                                exit;
                            }
                        }
                    } else {
                        $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                        $_SESSION['typeAlert'] = "error";
                        header('Location: ' . $this->_routes->url("profile"));
                        exit;
                    }
                } else {
                    header('Location: ' . $this->_routes->url('profile'));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function changeBilling(){
            $this->_session = new Session;
            $last_token = $this->_session->getToken();
            $this->_routes = new Routes;
            if($this->_session->isAuth()){
                if($_POST){
                    $this->_lang = new languageManager;
                    if($this->postDataValid($last_token)){
                        $this->_userHandler = new UserHandler;
                        $users = $this->_userHandler->getUsers(array('id' => htmlspecialchars($_SESSION['id'], ENT_QUOTES)));
                        $billingActivate = false;
                        foreach($users as $user){
                            if($user->activebilling() == 'Y'){
                                $billingActivate = true;
                            }
                        }
                        if($billingActivate){
                            if($this->_userHandler->updateUser(array('active_billing' => 'N', 'updated_at' => date('Y-m-d H:i:s')), array('id' => htmlspecialchars($_SESSION['id'], ENT_QUOTES)))){
                                $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "billing-disable");
                                $_SESSION['typeAlert'] = "success";
                                header('Location: ' . $this->_routes->url("profile"));
                                exit;
                            } else {
                                $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                                $_SESSION['typeAlert'] = "error";
                                header('Location: ' . $this->_routes->url("profile"));
                                exit;
                            }
                        } else {
                            $_SESSION['inputValueName'] = htmlspecialchars($_POST['name'], ENT_QUOTES);
                            $_SESSION['inputValueFirstname'] = htmlspecialchars($_POST['firstname'], ENT_QUOTES);
                            $_SESSION['inputValueAddress'] = htmlspecialchars($_POST['address'], ENT_QUOTES);
                            $_SESSION['inputValuePhone'] = htmlspecialchars($_POST['phone'], ENT_QUOTES);
                            $_SESSION['inputValueEmail'] = htmlspecialchars($_POST['email'], ENT_QUOTES);
                            $_SESSION['inputValueSIRET'] = htmlspecialchars($_POST['SIRET'], ENT_QUOTES);
                            $_SESSION['inputValueVAT'] = htmlspecialchars($_POST['VAT'], ENT_QUOTES);
                            $_SESSION['inputValueBank'] = htmlspecialchars($_POST['bank'], ENT_QUOTES);
                            $_SESSION['inputValueBIC'] = htmlspecialchars($_POST['BIC'], ENT_QUOTES);
                            $_SESSION['inputValueIBAN'] = htmlspecialchars($_POST['IBAN'], ENT_QUOTES);

                            $token = $this->_session->updateToken();

                            $data = array(
                                array('name', $_POST['name'], 'required'),
                                array('firstname', $_POST['firstname'], 'required'),
                                array('address', $_POST['address'], 'required'),
                                array('phone', $_POST['phone'], 'required', 'onlyNumber'),
                                array('email', $_POST['email'], 'required', 'min:3', 'max:255', 'email'),
                                array('SIRET', $_POST['SIRET'], 'required', 'max:14'),
                                array('VAT', $_POST['VAT'], 'required', 'max:100'),
                                array('bank', $_POST['bank'], 'required'),
                                array('BIC', $_POST['BIC'], 'required', 'max:11'),
                                array('IBAN', $_POST['IBAN'], 'required', 'max:34')
                            );

                            $this->_validator = new Validator();
                            $response = $this->_validator->validator($data);

                            if($response['success'] == 'false'){
                                // register validity of input
                                $_SESSION['inputResponseName'] = $response['name'];
                                $_SESSION['inputResponseFirstname'] = $response['firstname'];
                                $_SESSION['inputResponseAddress'] = $response['address'];
                                $_SESSION['inputResponsePhone'] = $response['phone'];
                                $_SESSION['inputResponseEmail2'] = $response['email'];
                                $_SESSION['inputResponseSIRET'] = $response['SIRET'];
                                $_SESSION['inputResponseVAT'] = $response['VAT'];
                                $_SESSION['inputResponseBank'] = $response['bank'];
                                $_SESSION['inputResponseBIC'] = $response['BIC'];
                                $_SESSION['inputResponseIBAN'] = $response['IBAN'];

                                // register error message by input
                                if($_SESSION['inputResponseName'] == 'invalid'){
                                    $_SESSION['inputResponseNameMessage'] = "<span class='text-danger'>";
                                    foreach($response['message']['name'] as $e){
                                        $_SESSION['inputResponseNameMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                    }
                                    $_SESSION['inputResponseNameMessage'] .= "</span>";
                                }

                                if($_SESSION['inputResponseFirstname'] == 'invalid'){
                                    $_SESSION['inputResponseFirstnameMessage'] = "<span class='text-danger'>";
                                    foreach($response['message']['firstname'] as $e){
                                        $_SESSION['inputResponseFirstnameMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                    }
                                    $_SESSION['inputResponseFirstnameMessage'] .= "</span>";
                                }

                                if($_SESSION['inputResponseAddress'] == 'invalid'){
                                    $_SESSION['inputResponseAddressMessage'] = "<span class='text-danger'>";
                                    foreach($response['message']['address'] as $e){
                                        $_SESSION['inputResponseAddressMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                    }
                                    $_SESSION['inputResponseAddressMessage'] .= "</span>";
                                }

                                if($_SESSION['inputResponsePhone'] == 'invalid'){
                                    $_SESSION['inputResponsePhoneMessage'] = "<span class='text-danger'>";
                                    foreach($response['message']['phone'] as $e){
                                        $_SESSION['inputResponsePhoneMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                    }
                                    $_SESSION['inputResponsePhoneMessage'] .= "</span>";
                                }

                                if($_SESSION['inputResponseEmail2'] == 'invalid'){
                                    $_SESSION['inputResponseEmail2Message'] = "<span class='text-danger'>";
                                    foreach($response['message']['email'] as $e){
                                        $_SESSION['inputResponseEmail2Message'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                    }
                                    $_SESSION['inputResponseEmail2Message'] .= "</span>";
                                }

                                if($_SESSION['inputResponseSIRET'] == 'invalid'){
                                    $_SESSION['inputResponseSIRETMessage'] = "<span class='text-danger'>";
                                    foreach($response['message']['SIRET'] as $e){
                                        $_SESSION['inputResponseSIRETMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                    }
                                    $_SESSION['inputResponseSIRETMessage'] .= "</span>";
                                }

                                if($_SESSION['inputResponseVAT'] == 'invalid'){
                                    $_SESSION['inputResponseVATMessage'] = "<span class='text-danger'>";
                                    foreach($response['message']['VAT'] as $e){
                                        $_SESSION['inputResponseVATMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                    }
                                    $_SESSION['inputResponseVATMessage'] .= "</span>";
                                }

                                if($_SESSION['inputResponseBank'] == 'invalid'){
                                    $_SESSION['inputResponseBankMessage'] = "<span class='text-danger'>";
                                    foreach($response['message']['bank'] as $e){
                                        $_SESSION['inputResponseBankMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                    }
                                    $_SESSION['inputResponseBankMessage'] .= "</span>";
                                }

                                if($_SESSION['inputResponseBIC'] == 'invalid'){
                                    $_SESSION['inputResponseBICMessage'] = "<span class='text-danger'>";
                                    foreach($response['message']['BIC'] as $e){
                                        $_SESSION['inputResponseBICMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                    }
                                    $_SESSION['inputResponseBICMessage'] .= "</span>";
                                }

                                if($_SESSION['inputResponseIBAN'] == 'invalid'){
                                    $_SESSION['inputResponseIBANMessage'] = "<span class='text-danger'>";
                                    foreach($response['message']['IBAN'] as $e){
                                        $_SESSION['inputResponseIBANMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                    }
                                    $_SESSION['inputResponseIBANMessage'] .= "</span>";
                                }

                                header('Location: ' . $this->_routes->url("profile"));
                                exit;
                            } else {
                                $exist = false;
                                $work = false;
                                $this->_billingHandler = new BillingHandler;
                                $billings = $this->_billingHandler->getBillings(array('user_id' => htmlspecialchars($_SESSION['id'], ENT_QUOTES)));
                                foreach($billings as $billing){
                                    $exist = true;
                                }
                                $name = htmlspecialchars($_POST['name'], ENT_QUOTES);
                                $firstname = htmlspecialchars($_POST['firstname'], ENT_QUOTES);
                                $address = htmlspecialchars($_POST['address'], ENT_QUOTES);
                                $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES);
                                $email = htmlspecialchars($_POST['email'], ENT_QUOTES);
                                $SIRET = htmlspecialchars($_POST['SIRET'], ENT_QUOTES);
                                $VAT = htmlspecialchars($_POST['VAT'], ENT_QUOTES);
                                $bank = htmlspecialchars($_POST['bank'], ENT_QUOTES);
                                $BIC = htmlspecialchars($_POST['BIC'], ENT_QUOTES);
                                $IBAN = htmlspecialchars($_POST['IBAN'], ENT_QUOTES);

                                try {
                                    if(!$exist){
                                        $id = $this->GUIDv4();
                                        if($this->_billingHandler->newBilling(array($id, htmlspecialchars($_SESSION['id'], ENT_QUOTES), $name, $firstname, $address, $phone, $email, $SIRET, $VAT, $bank, $BIC, $IBAN))){
                                            $work = true;
                                        } else {
                                            $work = false;
                                        }
                                    } else {
                                        if($this->_billingHandler->updateBilling(array("name" => $name, "firstname" => $firstname, "address" => $address, "phone" => $phone, "email" => $email, "SIRET" => $SIRET, "VAT" => $VAT, "bank" => $bank, "BIC" => $BIC, "IBAN" => $IBAN), array('user_id' => htmlspecialchars($_SESSION['id'], ENT_QUOTES)))){
                                            $work = true;
                                        } else {
                                            $work = false;
                                        }
                                    }
                                    if(!$this->_userHandler->updateUser(array("active_billing" => 'Y', 'updated_at' => date('Y-m-d H:i:s')), array('id' => htmlspecialchars($_SESSION['id'], ENT_QUOTES)))){
                                        $work = true;
                                    }
                                } catch(Exception $e){
                                    $work = false;
                                }

                                if($work){
                                    $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "billing-enable");
                                    $_SESSION['typeAlert'] = "success";
                                    header('Location: ' . $this->_routes->url("profile"));
                                    exit;
                                } else {
                                    $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                                    $_SESSION['typeAlert'] = "error";
                                    header('Location: ' . $this->_routes->url("profile"));
                                    exit;
                                }
                            }
                        }
                    } else {
                        $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                        $_SESSION['typeAlert'] = "error";
                        header('Location: ' . $this->_routes->url("profile"));
                        exit;
                    }
                } else {
                    header('Location: ' . $this->_routes->url('profile'));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function changeWatchState(){
            if(isset($_SESSION['watchState']) && !empty($_SESSION['watchState'])){
                if(htmlspecialchars($_SESSION['watchState'], ENT_QUOTES) == 'me'){
                    $_SESSION['watchState'] = 'all';
                } else {
                    $_SESSION['watchState'] = 'me';
                }
            }
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }

        private function changeCaptchaKey(){
            $this->_session = new Session;
            $last_token = $this->_session->getToken();
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            if($this->_session->isAuth()){
                if($this->_session->isAdmin()){
                    if($_POST){
                        if($this->postDataValid($last_token)){
                            $this->_userHandler = new UserHandler;
                            $token = $this->_session->updateToken();

                            $data = array(
                                array('pubkey', $_POST['pubkey'], 'required', 'max:50'),
                                array('privkey', $_POST['privkey'], 'required', 'max:50')
                            );

                            $this->_validator = new Validator();
                            $response = $this->_validator->validator($data);

                            if($response['success'] == 'false'){
                                // register validity of input
                                $_SESSION['inputResponsePubkey'] = $response['pubkey'];
                                $_SESSION['inputResponsePrivkey'] = $response['privkey'];

                                if($response['pubkey'] == 'invalid'){
                                    $_SESSION['inputResponsePubkeyMessage'] = "<span class='text-danger'>";
                                    foreach($response['message']['pubkey'] as $e){
                                        $_SESSION['inputResponsePubkeyMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                    }
                                    $_SESSION['inputResponsePubkeyMessage'] .= "</span>";
                                }
            
                                if($response['privkey'] == 'invalid'){
                                    $_SESSION['inputResponsePrivkeyMessage'] = "<span class='text-danger'>";
                                    foreach($response['message']['privkey'] as $e){
                                        $_SESSION['inputResponsePrivkeyMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                                    }
                                    $_SESSION['inputResponsePrivkeyMessage'] .= "</span>";
                                }

                                header('Location: ' . $this->_routes->url("settings"));
                                exit;
                            } else {
                                $pubkey = htmlspecialchars($_POST['pubkey'], ENT_QUOTES);
                                $privkey = htmlspecialchars($_POST['privkey'], ENT_QUOTES);
                                $this->_captchaHandler = new CaptchaHandler;
                                if($this->_captchaHandler->insertCaptcha($pubkey, $privkey)){
                                    $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "update-captcha");
                                    $_SESSION['typeAlert'] = "success";
                                    header('Location: ' . $this->_routes->url("settings"));
                                    exit;
                                } else {
                                    $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                                    $_SESSION['typeAlert'] = "error";
                                    header('Location: ' . $this->_routes->url("settings"));
                                    exit;
                                }
                            }
                        } else {
                            $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->url("settings"));
                            exit;
                        }
                    } else {
                        $_SESSION['alert'] = $this->_lang->getTxt('controllerDashboard', "global-error");
                        $_SESSION['typeAlert'] = "error";
                        header('Location: ' . $this->_routes->url("dashboard"));
                        exit;
                    }
                } else {
                    header('Location: ' . $this->_routes->url('dashboard'));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function postDataValid($token=null) {
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