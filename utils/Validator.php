<?php
    namespace Utils;

    class Validator {

        private $_data;

        public function __construct($data){
            $this->_data = $data;
        }

        public function validator(){
            $data = $this->_data;
            $validator = array(
                'success' => 'true',
                'message' => array(),
            );
            if(empty($data)){
                $validator['success'] = 'false';
                return $validator;
            } else {
                for($i = 0; $i < count($data); $i++){

                    $input = $data[$i][0];
                    $value = $data[$i][1];
                    $validator[$input] = 'valid';

                    for($j = 2; $j < count($data[$i]); $j++){
                        $param = $data[$i][$j];
                        if($param == "required"){
                            if(empty($value) || trim($value) == ""){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input] .= 'This field is required. ';
                            }
                        } else if ($param == "requiredLetter"){
                            if(!preg_match('/[A-Za-z]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input] .= 'must contain at least one letter. ';
                            }
                        } else if ($param == "requiredSpecialCharacter"){
                            if(!preg_match('/[^a-zA-Z\d]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input] .= 'must contain at least one special character. ';
                            }
                        } else if ($param == "requiredNumber"){
                            if(!preg_match('/[0-9]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input] .= 'must contain at least one number. ';
                            }
                        } else if ($param == "onlyNumber"){
                            if(preg_match('/[A-Za-z]/', $value) || preg_match('/[^a-zA-Z\d]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input] .= 'must contain only numbers. ';
                            }
                        } else if($param == "noNumber"){
                            if(preg_match('/[0-9]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input] .= 'Numbers are not allowed. ';
                            }
                        } else if($param == "noLetter"){
                            if(preg_match('/[A-Za-z]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input] .= 'Letters are not allowed. ';
                            }
                        } else if($param == "noSpecialCharacter"){
                            if(strpos($value, ".")){
                                $except = str_replace(".", "", $value);
                                if(preg_match('/[^a-zA-Z\d]/', $except)){
                                    $validator['success'] = 'false';
                                    $validator[$input] = 'invalid';
                                    $validator['message'][$input] .= 'Specials characters are not allowed. ';
                                }
                            }
                        } else if ($param == "onlyLetter"){
                            if(preg_match('/[0-9]/', $value) || preg_match('/[^a-zA-Z\d]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input] .= 'Must contain only letters';
                            }
                        } else if (strpos($param, "min") !== false){
                            $min_value = explode(":", $param);
                            if(strlen($value) < $min_value[1]){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input] .= 'Minimale size : ' . $min_value[1] . '. ';
                            }
                        } else if (strpos($param, "max") !== false){
                            $max_value = explode(":", $param);
                            if(strlen($value) > $max_value[1]){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input] .= 'Maximum size : ' . $max_value[1] . '. ';
                            }
                        } else if(strpos($param, "cpassword") !== false){
                            $cpassword_value = explode(":", $param);
                            if($value != $cpassword_value[1]){
                                $validator['success'] = 'false';
                                $validator["cpassword"] = "invalid";
                                $validator['message']["cpassword"] .= 'Passwords must match. ';
                            } else {
                                $validator['cpassword'] = 'valid';
                            }
                        } else if($param == "email"){
                            if(filter_var($value, FILTER_VALIDATE_EMAIL) === false){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input] .= 'Invalid email format. ';
                            }
                        }
                    }
                }
                return $validator;
            }
        }
    }
?>