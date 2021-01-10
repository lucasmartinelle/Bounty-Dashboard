<?php
    session_start();

    if(!isset($_SESSION['watchState']) || empty($_SESSION['watchState'])){
        $_SESSION['watchState'] = 'all';
    }
    
    require_once("app/Routes.php");
    include_once "app/init.php";

    use app\Routes;

    // Debug
    if(ACTIVE_DEBUG == true){
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
    }

    
    if(!isset($_COOKIE['lang']) || empty($_COOKIE['lang'])){
        setcookie('lang', LANGUAGE, time() + (86400 * 30), "/");
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