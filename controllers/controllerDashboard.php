<?php
    namespace controllers;

    require_once("views/View.php");
    use view\View;

    class controllerDashboard {
        private $_view;
        
        public function __construct($label, $name, $view, $template, $data){
            if($label == "dashboard"){
                $this->dashboard($name, $view, $template);
            }
        }

        private function dashboard($name, $view, $template){
            $this->_view = new View($view, $template);
            $this->_view->generate(array("titre" => $name));
        }
    }
?>