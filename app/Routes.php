<?php
    namespace app;

    require "app/includes/Routing.php";
    use app\includes\Routing;

    // Affirmation des routes
    class Routes extends Routing {
        public function __construct(){
            $this->initRoutes();
        }
        
        public function initRoutes(){
            $this->create(
                "/404",
                "404",
                "error 404",
                "controllerError",
                "errors/view404",
                "dashboard"
            );

            $this->create(
                "/dashoard",
                "dashboard",
                "Dashboard",
                "controllerDashboard",
                "dashboard/viewDashboard",
                "dashboard"
            );
        }

        public function load($label){
            $this->redirect($label);
        }

        public function url($label){
            return $this->getURL($label);
        }

        public function urlReplace($label, $replace){
            return $this->getURLReplace($label, $replace);
        }
    }
?>