<?php
    namespace controllers;

    require_once("views/View.php");
    require_once("utils/Validator.php");
    require_once("utils/Session.php");
    require_once("utils/Captcha.php");
    require_once("app/Routes.php");
    require_once("app/languages/languageManager.php");

    use app\Routes;
    use Utils\Session;
    use Utils\Validator;
    use Utils\Captcha;
    use view\View;
    use app\languages\languageManager;

    class controllerInvoices {
        private $_view;
        private $_session;
        private $_validator;
        private $_routes;
        private $_captcha;
        private $_lang;
        
        public function __construct($label, $name, $view, $template, $data){
            if($label == "invoices"){
                $this->invoices($name, $view, $template);
            }
        }

        private function invoices($name, $view, $template){
            $this->_view = new View($view, $template);
            $this->_view->generate(array("titre" => $name));
        }
    } 
?>