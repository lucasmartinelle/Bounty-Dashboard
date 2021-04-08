<?php
    namespace app\includes;

    class Routing {
        private $_routes = array();
        private $_routesByLabel = array();
        private $_view;
        private $_controller;

        // creation of a new route
        public function create($url, $label, $name, $controller, $view, $template){
            $this->_routes[$url] = array("label" => $label, "name" => $name, "controller" => $controller, "view" => $view, "template" => $template);
            $this->_routesByLabel[$label] = array("url" => $url, "name" => $name, "controller" => $controller, "view" => $view, "template" => $template);
        }

        public function get($url){
            if(empty($url)){
                header('Location: ' . URL . $this->_routesByLabel[DEFAULT_PAGE]["url"]);
            } else {
                if($this->stringStartsWith($url, "/") === false){
                    $url = "/"  . $url;
                }
                // error management
                $pageLoad = 0;
                $controllerExist = 0;
                $viewExist = 0;
                $templateExist = 0;
                // Initialization of the variable indicating that the page was found.
                $find = 0;
                // We start by looking in the defined routes (without {})
                // We're going through the route chart
                foreach($this->_routes as $route => $info){
                    // We retrieve the elements of the URL in an array for the controller
                    $constructorController = array();
                    // Initialization of the error variable
                    $error = false;
                    // We check that the URL has as many arguments as the element of the array
                    $array_url_piece = explode("/", $route);
                    $url_piece = explode("/", $url);;
                    if(count($array_url_piece) == count($url_piece)){
                        // Browse the url part by part to check the existence of relative data ("{}")
                        for($i = 1; $i < count($url_piece); $i++){
                            // If a part of the URL is not equal to the element of the array we look if this element does not contain "{}"
                            if($url_piece[$i] != $array_url_piece[$i]){
                                $error = true;
                            // The element of the array is equal to the element of the URL
                            } else {
                                array_push($constructorController, htmlspecialchars($url_piece[$i], ENT_QUOTES));
                            }
                        }
                    // not so many arguments = error
                    } else {
                        $error = true;
                    }

                    if(!$error){
                        // we check if the controller exists
                        if(file_exists("controllers/".$info['controller'].".php")){
                            $controllerExist = 1;
                        } 
                        // we check if the view exists
                        if($info['view'] == null){
                            $viewExist = 1;
                        } else {
                            if(file_exists("views/".$info['view'].".php")){
                                $viewExist = 1;
                            }
                        }
                        // we check if the template exists
                        if($info['template'] == null){
                            $templateExist = 1;
                        } else {
                            if(file_exists("views/template/".$info['template'].".php")){
                                $templateExist = 1;
                            }
                        }
                        // if they exist we call the page
                        if($controllerExist == 1 && $viewExist == 1 && $templateExist == 1){
                            $pageLoad = 1;
                            $find = 1;
                            require_once("controllers/".$info['controller'].".php");
                            $load = 'controllers\\'.$info['controller'];
                            $this->_controller = new $load($info['label'], $info['name'], $info['view'], $info['template'], $constructorController);
                        }
                    }
                }
                if($find == 0){
                    foreach($this->_routes as $route => $info){
                        // We retrieve the elements of the URL in an array for the controller
                        $constructorController = array();
                        // Initialization of the error variable
                        $error = false;
                        // We check that the URL has as many arguments as the element of the array
                        $array_url_piece = explode("/", $route);
                        $url_piece = explode("/", $url);
                        if(count($array_url_piece) == count($url_piece)){
                            // Browse the url part by part to check the existence of relative data ("{}")
                            for($i = 1; $i < count($url_piece); $i++){
                                // If a part of the URL is not equal to the element of the array we look if this element does not contain "{}".
                                if($url_piece[$i] != $array_url_piece[$i]){
                                    if(strpos($array_url_piece[$i], "{") !== false && strpos($array_url_piece[$i], "}") !== false){
                                        // if it's the case, we get the type between "{}" and we add the element in the constructor of the controller
                                        $type = $this->get_string_between($array_url_piece[$i], "{", "}");
                                        if($type == "int"){
                                            array_push($constructorController, (int) htmlspecialchars($url_piece[$i], ENT_QUOTES));
                                        } else if($type == "string"){
                                            array_push($constructorController, (string) htmlspecialchars($url_piece[$i], ENT_QUOTES));
                                        } else {
                                            // type is unknown = error
                                            $error = true;
                                        }
                                    } else {
                                        // unknown element and no "{}" = error
                                        $error = true;
                                    }
                                // The element of the array is equal to the element of the URL
                                } else {
                                    array_push($constructorController, htmlspecialchars($url_piece[$i], ENT_QUOTES));
                                }
                            }
                        // not so many arguments = error
                        } else {
                            $error = true;
                        }

                        if(!$error){
                            // we check if the controller exists
                            if(file_exists("controllers/".$info['controller'].".php")){
                                $controllerExist = 1;
                            } 
                            // we check if the view exists
                            if($info['view'] == null){
                                $viewExist = 1;
                            } else {
                                if(file_exists("views/".$info['view'].".php")){
                                    $viewExist = 1;
                                }
                            }
                            // we check if the template exists
                            if($info['template'] == null){
                                $templateExist = 1;
                            } else {
                                if(file_exists("views/template/".$info['template'].".php")){
                                    $templateExist = 1;
                                }
                            }
                            // if they exist we call the page
                            if($controllerExist == 1 && $viewExist == 1 && $templateExist == 1){
                                $pageLoad = 1;
                                require_once("controllers/".$info['controller'].".php");
                                $load = 'controllers\\'.$info['controller'];
                                $this->_controller = new $load($info['label'], $info['name'], $info['view'], $info['template'], $constructorController);
                            }
                        }
                    }
                }

                if($pageLoad == 0){
                    // otherwise, redirection page 404
                    header('Location: ' . URL . $this->_routesByLabel["404"]["url"]);
                }
            }
        }

        public function redirect($label){
            header('Location: ' . URL . $this->_routesByLabel[$label]["url"]);
        }

        public function getURL($label){
            return URL . $this->_routesByLabel[$label]["url"];
        }

        public function getURLReplace($label, $replace){
            $url = $this->_routesByLabel[$label]["url"];
            foreach($replace as $value){
                if(strpos($url, "{int}") !== false){
                    $url = str_replace("{int}", (int) htmlspecialchars($value), $url);
                } else if(strpos($url, "{string}") !== false){
                    $url = str_replace("{string}", (string) htmlspecialchars($value), $url);
                }
            }
            return URL . $url;
        }

        // function to retrieve the types between "{}"
        private function get_string_between($string, $start, $end){
            $string = ' ' . $string;
            $ini = strpos($string, $start);
            if ($ini == 0) return '';
            $ini += strlen($start);
            $len = strpos($string, $end, $ini) - $ini;
            return substr($string, $ini, $len);
        }

        private function stringStartsWith($haystack,$needle,$case=true) {
            if ($case){
                return strpos($haystack, $needle, 0) === 0;
            }
            return stripos($haystack, $needle, 0) === 0;
        }

    }
?>