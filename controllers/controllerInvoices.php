<?php
    namespace controllers;

    require_once("views/View.php");
    require_once("utils/Validator.php");
    require_once("utils/Session.php");
    require_once("models/captchaHandler.php");
    require_once("app/Routes.php");
    require_once("app/languages/languageManager.php");
    require_once("models/platformHandler.php");
    require_once("models/reportHandler.php");
    require_once("models/billingHandler.php");
    require_once("models/userHandler.php");
    require_once("utils/Invoices.php");

    use app\Routes;
    use Utils\Session;
    use Utils\Validator;
    use Models\CaptchaHandler;
    use Utils\Invoices;
    use Models\PlatformHandler;
    use Models\ReportHandler;
    use Models\BillingHandler;
    use Models\UserHandler;
    use view\View;
    use app\languages\languageManager;
    use DateTime;

    class controllerInvoices {
        private $_view;
        private $_session;
        private $_validator;
        private $_routes;
        private $_captchaHandler;
        private $_invoices;
        private $_lang;
        private $_platformHandler;
        private $_reportHandler;
        private $_billingHandler;
        private $_userHandler;
        
        public function __construct($label, $name, $view, $template, $data){
            if($label == "invoices"){
                $this->invoices($name, $view, $template);
            } elseif($label == "generateInvoice"){
                $this->generateInvoice();
            }
        }

        private function invoices($name, $view, $template){
            $this->_session = new Session;
            $this->_userHandler = new UserHandler;
            $this->_routes = new Routes;
            $active = $this->_userHandler->getUsers(array("id" => htmlspecialchars($_SESSION['id'], ENT_QUOTES)))[0]->activeBilling();
            if($active == 'Y'){
                if($_POST){
                    $this->postInvoices();
                } else {
                    if($this->_session->isAuth()){
                        $this->_platformHandler = new platformHandler;
                        $this->_reportHandler = new ReportHandler;
                        $platforms = $this->_platformHandler->getPlatforms();
                        $filterInvoice = null;
                        if(isset($_SESSION['filterInvoicePlatform']) && !empty($_SESSION['filterInvoicePlatform'])){
                            $filterInvoice = htmlspecialchars($_SESSION['filterInvoicePlatform'], ENT_QUOTES);
                        }
                        $filterMonth = null;
                        if(isset($_SESSION['filterInvoiceMonth']) && !empty($_SESSION['filterInvoiceMonth'])){
                            $filterMonth = htmlspecialchars($_SESSION['filterInvoiceMonth'], ENT_QUOTES);
                        }
                        $reports = $this->_reportHandler->bugs(false, null, $filterInvoice, null, null, null, $filterMonth, 1, htmlspecialchars($_SESSION['id'], ENT_QUOTES));
                        if($filterMonth == null){
                            $filterMonth = 'none';
                        }
                        if($filterInvoice == null){
                            $filterInvoice = 'none';
                        } else {
                            $filterInvoice = $this->_platformHandler->getPlatforms(array("id" => $filterInvoice))[0]->name();
                        }
                        $token = $this->_session->getToken();
                        $this->_view = new View($view, $template);
                        $this->_view->generate(array("titre" => $name, "token" => $token, "platforms" => $platforms, "reports" => $reports, 'filterMonth' => $filterMonth, 'filterInvoice' => $filterInvoice));
                    } else {
                        header('Location: ' . $this->_routes->url('login'));
                        exit;
                    }
                }
            } else {
                header('Location: ' . $this->_routes->url('dashboard'));
                exit;
            }
        }

        private function postInvoices(){
            $this->_session = new Session;
            $this->_routes = new Routes;
            $this->_lang = new languageManager;
            $last_token = $this->_session->getToken();
            if($this->_session->isAuth()){
                if($this->postDataValid($last_token)){
                    $token = $this->_session->updateToken();

                    $this->_platformHandler = new platformHandler;
                    $platforms = $this->_platformHandler->getPlatforms();

                    $listPlatforms = "";
                    foreach($platforms as $platform){
                        $listPlatforms .= $platform->id() . "|";
                    }
                    $listPlatforms = substr($listPlatforms, 0, -1);

                    $data = array(
                        array("platform", $_POST['platform'], 'required', 'equal|'.$listPlatforms),
                        array("month", $_POST['month'], 'required', 'equal|01|02|03|04|05|06|07|08|09|10|11|12')
                    );

                    $this->_validator = new Validator();
                    $response = $this->_validator->validator($data);

                    if($response['success'] == 'false'){
                        $_SESSION['inputResponsePlatform'] = $response['platform'];
                        $_SESSION['inputResponseMonth'] = $response['month'];

                        if($response['platform'] == 'invalid'){
                            $_SESSION['inputResponsePlatformMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['platform'] as $e){
                                $_SESSION['inputResponsePlatformMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponsePlatformMessage'] .= "</span>";
                        }

                        if($response['month'] == 'invalid'){
                            $_SESSION['inputResponseMonthMessage'] = "<span class='text-danger'>";
                            foreach($response['message']['month'] as $e){
                                $_SESSION['inputResponsePlatformMessage'] .= "<i class='fas fa-circle' style='font-size: 8px;'></i> " . $e . "<br>";
                            }
                            $_SESSION['inputResponseMonthMessage'] .= "</span>";
                        }

                        header('Location: ' . $this->_routes->url('invoices'));
                        exit;
                    } else {
                        $_SESSION['filterInvoicePlatform'] = htmlspecialchars($_POST['platform'], ENT_QUOTES);
                        $_SESSION['filterInvoiceMonth'] = htmlspecialchars($_POST['month'], ENT_QUOTES);

                        header('Location: ' . $this->_routes->url('invoices'));
                        exit;
                    }
                } else {
                    $_SESSION['alert'] = $this->_lang->getTxt('controllerReports', "global-error");
                    $_SESSION['typeAlert'] = "error";
                    header('Location: ' . $this->_routes->url("invoices"));
                    exit;
                }
            } else {
                header('Location: ' . $this->_routes->url('login'));
                exit;
            }
        }

        private function generateInvoice(){
            $this->_billingHandler = new BillingHandler;
            $this->_userHandler = new UserHandler;
            $this->_platformHandler = new platformHandler;
            $month = htmlspecialchars($_POST['month'], ENT_QUOTES);
            $platform = htmlspecialchars($_POST['platform'], ENT_QUOTES);
            if($month != 'none' && $platform != 'none'){
                $billings = $this->_billingHandler->getBillings(array("user_id" => htmlspecialchars($_SESSION['id'], ENT_QUOTES)));
                $users = $this->_userHandler->getUsers(array("id" => htmlspecialchars($_SESSION['id'], ENT_QUOTES)));
                $dateObj   = DateTime::createFromFormat('!m', $month);
                $monthName = $dateObj->format('F');
                $platformInfo = $this->_platformHandler->getPlatforms(array("name" => $platform));
                $data = array(
                    "prenom" => $billings[0]->firstname(), 
                    "nom" => $billings[0]->name(), 
                    "month" => $monthName, 
                    "number" => str_pad($users[0]->invoicenb() + 1, 2, '0', STR_PAD_LEFT), 
                    "address" => $billings[0]->address(), 
                    "phone" => $billings[0]->phone(), 
                    "email" => $billings[0]->email(),
                    "SIRET" => $billings[0]->SIRET(),
                    "VAT" => $billings[0]->VAT(),
                    "BANK" => $billings[0]->bank(),
                    "IBAN" => $billings[0]->IBAN(),
                    "BIC" => $billings[0]->BIC(),
                    "PROJECTPLATFORM" => $platformInfo[0]->name(),
                    "CLIENTPLATFORM" => $platformInfo[0]->client(),
                    "BTWPLATFORM" => $platformInfo[0]->BTW(),
                    "ADDRESSPLATFORM" => $platformInfo[0]->address(),
                    "EMAILPLATFORM" => $platformInfo[0]->email(),
                    "DATEPLATFORM" => $platformInfo[0]->date()
                );

                if(isset($_POST['reports']) && !empty($_POST['reports'])){                
                    $reports = $_POST['reports'];
                    $this->_invoices = new Invoices($data, $reports);
                    $inv = $this->_invoices->createInvoice();
                    $invoicenb = $this->_userHandler->getUsers(array("id" => htmlspecialchars($_SESSION['id'], ENT_QUOTES)))[0]->invoicenb();
                    if($this->_userHandler->updateUser(array("invoice_nb" => (int) $invoicenb + 1), array("id" => htmlspecialchars($_SESSION['id'], ENT_QUOTES)))){
                        $returned = array(
                            'title' => '2020'.date_parse($monthName)['month'].($invoicenb + 1),
                            'html' => $inv
                        );
                        $json = json_encode($returned);
                        echo $json;
                    } else {
                        $returned = array(
                            'html' => 'error'
                        );
                        $json = json_encode($returned);
                        echo $json;
                    }
                } else {
                    $returned = array(
                        'html' => 'none'
                    );
                    $json = json_encode($returned);
                    echo $json;
                }
            } else {
                $returned = array(
                    'html' => 'nofilters'
                );
                $json = json_encode($returned);
                echo $json;
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