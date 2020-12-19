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

            $this->create(
                "/dashboard/logout",
                "logout",
                "Logout",
                "controllerAuth",
                null,
                null
            );

            // dashboard
            $this->create(
                "/dashboard",
                "dashboard",
                "Dashboard",
                "controllerDashboard",
                "dashboard/viewDashboard",
                "dashboard"
            );

            $this->create(
                "/dashboard/settings",
                "settings",
                "Settings",
                "controllerDashboard",
                "dashboard/viewSettings",
                "dashboard"
            );

            $this->create(
                "/dashboard/profile",
                "profile",
                "Profile",
                "controllerDashboard",
                "dashboard/viewProfile",
                "dashboard"
            );

            $this->create(
                "/dashboard/profile/changeUsername",
                "changeUsername",
                "Change Username",
                "controllerDashboard",
                null,
                null
            );

            $this->create(
                "/dashboard/profile/changeEmail",
                "changeEmail",
                "Change Email",
                "controllerDashboard",
                null,
                null
            );

            $this->create(
                "/dashboard/profile/changePassword",
                "changePassword",
                "Change Password",
                "controllerDashboard",
                null,
                null
            );

            $this->create(
                "/dashboard/profile/changeBilling",
                "changeBilling",
                "Change Billing",
                "controllerDashboard",
                null,
                null
            );

            $this->create(
                "/dashboard/admin/addUser",
                "adduser",
                "Add a user",
                "controllerDashboard",
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