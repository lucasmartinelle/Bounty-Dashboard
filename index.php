<?php
    session_start();
    
    require_once("app/Routes.php");
    include_once "app/init.php";

    use app\Routes;

    // Debug
    if(ACTIVE_DEBUG == true){
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
    }
    
    // Initialisation des routes
    $routes = new Routes;
    // Recherche de la route
    if (isset($_GET["url"])){
        $url = htmlentities(trim($_GET["url"]));
    } else {
        $url = "";
    }

    // Récupération de la route
    $routes->get($url);
?>