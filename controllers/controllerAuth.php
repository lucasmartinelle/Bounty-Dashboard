<?php
    namespace controllers;

    require_once("views/View.php");
    use view\View;

    class controllerAuth {
        private $_view;
        
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
                $this->confirmRegistration($name, $view, $template);
            }
        }

        private function registration($name, $view, $template){
            $this->_view = new View($view, $template);
            $this->_view->generate(array("titre" => $name));
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

        private function confirmRegistration($name, $view, $template){
            $this->_view = new View($view, $template);
            $this->_view->generate(array("titre" => $name));
        }
    }
?>