<?php
    namespace controllers;

    require_once("views/View.php");
    require_once("models/userHandler.php");
    require_once("utils/Session.php");
    require_once("utils/Validator.php");
    require_once("utils/Captcha.php");
    require_once("app/Routes.php");
    require_once("utils/Sender.php");

    use app\Routes;
    use Utils\Session;
    use Utils\Validator;
    use Utils\Captcha;
    use Utils\Sender;
    use Models\UserHandler;
    use view\View;

    class controllerAuth {
        private $_view;
        private $_session;
        private $_userHandler;
        private $_validator;
        private $_routes;
        private $_sender;
        private $_captcha;
        
        public function __construct($label, $name, $view, $template, $data){
            if($label == "registration"){
                $this->registration($name, $view, $template);
            } elseif($label == "login"){
                $this->login($name, $view, $template);
            } elseif($label == "forgot"){
                $this->forgot($name, $view, $template);
            } elseif($label == "sentRegistration"){
                $this->sentRegistration($name, $view, $template);
            } elseif($label == "sentForgot"){
                $this->sentForgot($name, $view, $template);
            } elseif($label == "confirmForgot"){
                $this->confirmForgot($name, $view, $template);
            } elseif($label == "confirmRegistration"){
                $this->confirmRegistration($data);
            }
        }

        private function registration($name, $view, $template){
            if($_POST){
                $this->postRegistration();
            } else {
                $this->_session = new Session;
                $token = $this->_session->updateToken();
                $this->_view = new View($view, $template);
                $this->_view->generate(array("titre" => $name, "token" => $token));
            }
        }

        protected function postRegistration(){
            $this->_routes = new Routes;
            if($this->postDataValid()){
                $this->_session = new Session;
                $this->_userHandler = new UserHandler;

                $_SESSION['inputValueUsername'] = htmlspecialchars($_POST['username'], ENT_QUOTES);
                $_SESSION['inputValueEmail'] = htmlspecialchars($_POST['email'], ENT_QUOTES);

                $token = $this->_session->updateToken();

                $data = array(
                    array('username', $_POST['username'], 'required', 'max:200'),
                    array('email', $_POST['email'], 'required', 'min:3', 'max:255', 'email', 'unique|users|email'),
                    array('password', $_POST['password'], 'cpassword:'.$_POST['cpassword'], 'required', 'min:6', 'max:32', 'requiredSpecialCharacter', 'requiredNumber', 'requiredLetter')
                );

                $this->_validator = new Validator();
                $response = $this->_validator->validator($data);

                if($response['success'] == 'false'){
                    // register validity of input
                    $_SESSION['inputResponseUsername'] = $response['username'];
                    $_SESSION['inputResponseEmail'] = $response['email'];
                    $_SESSION['inputResponsePassword'] = $response['password'];
                    $_SESSION['inputResponseCPassword'] = $response['cpassword'];

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

                    header('Location: ' . $this->_routes->url("registration"));
                    exit;
                } else {
                    $id = $this->GUIDv4();
                    $username = htmlspecialchars($_POST['username'], ENT_QUOTES);
                    $email = htmlspecialchars($_POST['email'], ENT_QUOTES);
                    $password = password_hash(htmlspecialchars($_POST['password'], ENT_QUOTES), PASSWORD_ARGON2ID);
                    $role;

                    $count = $this->_userHandler->countUsers();
                    if($count < 2){
                        $role = "admin";
                    } else {
                        $role = "hunter";
                    }

                    try {
                        if($this->_userHandler->newUser(array($id, $username, $email, $password, $token, $role))) {
                            // send mail
                            $confirmURL = $this->_routes->urlReplace("confirmRegistration", array($token));
                            $this->_sender = new Sender($username, $email, $confirmURL);
                            if($this->_sender->validationCompte()){
                                header('Location: ' . $this->_routes->url("sentRegistration"));
                                exit;
                            } else {
                                $_SESSION['alert'] = "An error has occurred while processing your request.";
                                $_SESSION['typeAlert'] = "error";
                                header('Location: ' . $this->_routes->url("registration"));
                                exit;
                            }
                        } else {
                            $_SESSION['alert'] = "An error has occurred while processing your request.";
                            $_SESSION['typeAlert'] = "error";
                            header('Location: ' . $this->_routes->url("registration"));
                            exit;
                        }
                    } catch (Exception $e){
                        $_SESSION['alert'] = "An error has occurred while processing your request.";
                        $_SESSION['typeAlert'] = "error";
                        header('Location: ' . $this->_routes->url("registration"));
                        exit;
                    }
                }
            } else {
                $_SESSION['alert'] = "An error has occurred while processing your request.";
                $_SESSION['typeAlert'] = "error";
                header('Location: ' . $this->_routes->url("registration"));
                exit;
            }
        }

        private function login($name, $view, $template){
            $this->_view = new View($view, $template);
            $this->_view->generate(array("titre" => $name));
        }

        private function forgot($name, $view, $template){
            $this->_view = new View($view, $template);
            $this->_view->generate(array("titre" => $name));
        }

        private function sentRegistration($name, $view, $template){
            $id = "registration";
            $this->_view = new View($view, $template);
            $this->_view->generate(array("titre" => $name, "id" => $id));
        }

        private function sentForgot($name, $view, $template){
            $id = "forgot";
            $this->_view = new View($view, $template);
            $this->_view->generate(array("titre" => $name, "id" => $id));
        }

        private function confirmForgot($name, $view, $template){
            $this->_view = new View($view, $template);
            $this->_view->generate(array("titre" => $name));
        }

        private function confirmRegistration($data){
            $token = htmlspecialchars($data[3], ENT_QUOTES);
            $this->_userHandler = new UserHandler;
            $this->_routes = new Routes;
            if(isset($token) && !empty($token)){
                $users = $this->_userHandler->getUsers(array('token' => $token));
                $updated = false;
                foreach($users as $user){
                    if($user->token() == $token){
                        $id = $user->id();
                        $date = date('Y-m-d H:i:s');
                        $this->_session = new Session;
                        $token = $this->_session->updateToken();
                        if($this->_userHandler->updateUser(array('active' => 'Y', 'updated_at' => $date, 'token' => $token), array('id' => htmlspecialchars($id, ENT_QUOTES)))){
                            $updated = true;
                        }
                    }
                }
                if($updated){
                    $_SESSION['alert'] = 'Your email address was confirmed. You can now log in !';
                    $_SESSION['typeAlert'] = 'success';
                    header('Location: ' . $this->_routes->url("login"));
                    exit;
                } else {
                    header('Location: ' . $this->_routes->url("login"));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url("login"));
                exit;
            }
        }

        private function postDataValid() {
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

        function GUIDv4 ($trim = true){
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