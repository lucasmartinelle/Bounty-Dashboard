<?php
    namespace controllers;

    require_once("views/View.php");
    use view\View;

    class controllerError {
        private $_view;
        
        public function __construct($label, $name, $view, $template, $data){
            if($label == "404"){
                $this->error404($name, $view, $template);
            }
        }

        private function error404($name, $view, $template){
            $this->_view = new View($view, $template);
            $this->_view->generate(array("titre" => $name));
        }
    }
?>