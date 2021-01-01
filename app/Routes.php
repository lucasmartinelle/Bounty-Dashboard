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

            //admin
            $this->create(
                "/dashboard/admin/addUser",
                "adduser",
                "Add a user",
                "controllerDashboard",
                null,
                null
            );

            $this->create(
                "/dashboard/admin/removeUser/{string}",
                "removeuser",
                "Remove a user",
                "controllerDashboard",
                null,
                null
            );

            $this->create(
                "/dashboard/platforms",
                "platforms",
                "Platforms",
                "controllerPlatforms",
                "platforms/viewPlatforms",
                "dashboard"
            );

            $this->create(
                "/dashboard/deletePlatform",
                "deletePlatform",
                "Delete a platform",
                "controllerPlatforms",
                null,
                null
            );

            $this->create(
                "/dashboard/programs",
                "programs",
                "Programs",
                "controllerPrograms",
                "programs/viewPrograms",
                "dashboard"
            );

            $this->create(
                "/dashboard/deleteProgram/{string}",
                "deleteProgram",
                "Delete a program",
                "controllerPrograms",
                null,
                null
            );

            
            $this->create(
                "/dashboard/scope",
                "scope",
                "Scope",
                "controllerPrograms",
                null,
                null
            );

            $this->create(
                "/dashboard/tags",
                "tags",
                "Tags",
                "controllerPrograms",
                null,
                null
            );

            $this->create(
                "/dashboard/reports",
                "reports",
                "Reports",
                "controllerReports",
                "reports/viewReports",
                "dashboard"
            );

            $this->create(
                "/dashboard/filterReports",
                "filterReports",
                "Reports",
                "controllerReports",
                "reports/viewReports",
                "dashboard"
            );

            $this->create(
                "/dashboard/createReport",
                "createReport",
                "Create a Report",
                "controllerReports",
                "reports/viewCreateReport",
                "dashboard"
            );

            $this->create(
                "/dashboard/editReport/{string}",
                "editReport",
                "Edit a Report",
                "controllerReports",
                "reports/viewEditReport",
                "dashboard"
            );

            $this->create(
                "/dashboard/showReport/{string}",
                "showReport",
                "Show a Report",
                "controllerReports",
                "reports/viewShowReport",
                "dashboard"
            );

            $this->create(
                "/dashboard/deleteReport/{string}",
                "deleteReport",
                "Delete a Report",
                "controllerReports",
                null,
                null
            );

            $this->create(
                "/dashboard/generateMarkdown",
                "generateMarkdown",
                "Generate markdown",
                "controllerReports",
                null,
                null
            );
            
            $this->create(
                "/dashboard/changeGain",
                "gainReport",
                "Set gain of a report",
                "controllerReports",
                null,
                null  
            );

            $this->create(
                "/dashboard/useTemplate",
                "useTemplate",
                "Use a template",
                "controllerReports",
                null,
                null
            );

            $this->create(
                "/dashboard/templates",
                "templates",
                "Templates",
                "controllerTemplates",
                "templates/viewTemplates",
                "dashboard"
            );

            $this->create(
                "/dashboard/createTemplate",
                "createTemplate",
                "Create a Template",
                "controllerTemplates",
                "templates/viewCreateTemplate",
                "dashboard"
            );

            $this->create(
                "/dashboard/editTemplate/{string}",
                "editTemplate",
                "Edit a Template",
                "controllerTemplates",
                "templates/viewEditTemplate",
                "dashboard"
            );

            $this->create(
                "/dashboard/showTemplate/{string}",
                "showTemplate",
                "Show a Template",
                "controllerTemplates",
                "templates/viewShowTemplate",
                "dashboard"
            );

            $this->create(
                "/dashboard/deleteTemplate/{string}",
                "deleteTemplate",
                "Delete a Template",
                "controllerTemplates",
                null,
                null
            );

            $this->create(
                "/dashboard/invoices",
                "invoices",
                "Invoices",
                "controllerInvoices",
                "invoices/viewInvoices",
                "dashboard",
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