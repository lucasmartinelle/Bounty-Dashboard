<?php
    namespace Utils;

    require_once("models/validatorHandler.php");

    use PDO;
    use Models\ValidatorHandler;

    class Validator extends ValidatorHandler {

        private $_data;
        private $_validatorHandler;

        public function validator($data){
            if(empty($data)){
                return false;
            } else {
                $validator = array(
                    'success' => 'true',
                    'message' => array(),
                    'unique' => array(),
                    'uploaded' => array(),
                );

                for($i = 0; $i < count($data); $i++){

                    $input = $data[$i][0];
                    $value = $data[$i][1];
                    $validator[$input] = 'valid';
                    $validator['message'][$input] = array();

                    for($j = 2; $j < count($data[$i]); $j++){
                        $param = $data[$i][$j];
                        if($param == "required"){
                            if(!isset($value) || empty($value) || trim($value) == ""){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = 'This field is required';
                            }
                        } else if ($param == "requiredLetter" && !empty($value)){
                            if(!preg_match('/[A-Za-z]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = 'Must contain at least one letter';
                            }
                        } else if ($param == "requiredSpecialCharacter" && !empty($value)){
                            if(!preg_match('/[^a-zA-Z\d]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = 'Must contain at least one special character';
                            }
                        } else if ($param == "requiredNumber" && !empty($value)){
                            if(!preg_match('/[0-9]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = 'Must contain at least one digit';
                            }
                        } else if ($param == "onlyNumber" && !empty($value)){
                            if(preg_match('/[A-Za-z]/', $value) || preg_match('/[^a-zA-Z\d]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = 'Must contain only digits';
                            }
                        } else if($param == "noNumber" && !empty($value)){
                            if(preg_match('/[0-9]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = 'Numbers are not allowed';
                            }
                        } else if($param == "noLetter" && !empty($value)){
                            if(preg_match('/[A-Za-z]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = 'Letters are not allowed';
                            }
                        } else if($param == "noSpecialCharacter" && !empty($value)){
                            if(strpos($value, ".")){
                                $except = str_replace(".", "", $value);
                                if(preg_match('/[^a-zA-Z\d]/', $except)){
                                    $validator['success'] = 'false';
                                    $validator[$input] = 'invalid';
                                    $validator['message'][$input][] = 'Special characters are not allowed';
                                }
                            }
                        } else if ($param == "onlyLetter" && !empty($value)){
                            if(preg_match('/[0-9]/', $value) || preg_match('/[^a-zA-Z\d]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = 'Must contain only letters';
                            }
                        } else if (strpos($param, "min") !== false && !empty($value)){
                            $min_value = explode(":", $param);
                            if(strlen($value) < $min_value[1]){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = 'Minimum size : ' . $min_value[1] . ' characters';
                            }
                        } else if (strpos($param, "max") !== false && !empty($value)){
                            $max_value = explode(":", $param);
                            if(strlen($value) > $max_value[1]){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = 'Maximum size : ' . $max_value[1] . ' characters';
                            }
                        } else if(strpos($param, "cpassword") !== false && !empty($value)){
                            $cpassword_value = explode(":", $param);
                            if($value != $cpassword_value[1]){
                                $validator['success'] = 'false';
                                $validator["cpassword"] = "invalid";
                                $validator['message']["cpassword"][] = 'Passwords must match';
                            } else {
                                $validator['cpassword'] = 'valid';
                            }
                        } else if($param == "email" && !empty($value)){
                            if(filter_var($value, FILTER_VALIDATE_EMAIL) === false){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = 'Invalid email format';
                            }
                        } else if(strpos($param, "unique") !== false && !empty($value)){
                            $dt = explode("|", $param);
                            $table = $dt[1];
                            $column = $dt[2];
                            if(!$this->unique($table, $value, $column)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['unique'][$input] = 'false';
                            }
                        } else if(strpos($param, "exist") !== false && !empty($value)){
                            $dt = explode("|", $param);
                            $table = $dt[1];
                            $column = $dt[2];
                            if($this->unique($table, $value, $column)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['unique'][$input] = 'false';
                            }
                        } else if($param == "URL" && !empty($value)){
                            $ver = idn_to_ascii($value);
                            if(substr(trim($value), 0, strlen("https://")) === "https://" || substr(trim($value), 0, strlen("http://")) === "http://"){
                                if(!filter_var($ver, FILTER_VALIDATE_URL)){
                                    $validator['success'] = 'false';
                                    $validator[$input] = 'invalid';
                                    $validator['message'][$input][] = 'Invalid URL format';
                                }
                            } else {
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = 'Invalid URL format';
                            }
                        } else if(strpos($param,"equal") !== false && !empty($value)){
                            $dt = explode("|", $param);
                            $Ok = false;
                            foreach($dt as $val){
                                if($val != "equal"){
                                    if(htmlspecialchars($val, ENT_QUOTES) == $value){
                                        $Ok = true;
                                    }
                                }
                            }
                            if($Ok == false){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = 'Value denied.';
                            }
                        }
                    }
                }
                return $validator;
            }
        }
    }
?>