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
                "/dashboard",
                "dashboard",
                "Dashboard",
                "controllerDashboard",
                "dashboard/viewDashboard",
                "dashboard"
            );

            // auth
            $this->create(
                "/dashboard/registration",
                "registration",
                "Registration",
                "controllerAuth",
                "auth/viewRegistration",
                "dashboard"
            );

            $this->create(
                "/dashboard/login",
                "login",
                "Login",
                "controllerAuth",
                "auth/viewLogin",
                "dashboard"
            );

            $this->create(
                "/dashboard/forgot",
                "forgot",
                "Forgot",
                "controllerAuth",
                "auth/viewForgot",
                "dashboard"
            );

            $this->create(
                "/dashboard/sent/registration",
                "sentRegistration",
                "Sent Email",
                "controllerAuth",
                "auth/viewEmailSent",
                "dashboard"
            );

            $this->create(
                "/dashboard/sent/forgot",
                "sentForgot",
                "Sent email",
                "controllerAuth",
                "auth/viewEmailSent",
                "dashboard"
            );

            $this->create(
                "/dashboard/confirm/forgot/{string}",
                "confirmForgot",
                "Confirmation",
                "controllerAuth",
                "auth/viewValidationForgot",
                "dashboard"
            );

            $this->create(
                "/dashboard/confirm/registration/{string}",
                "confirmRegistration",
                "Confirmation",
                "controllerAuth",
                null,
                null
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