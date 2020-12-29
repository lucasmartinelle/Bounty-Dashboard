<?php
    namespace Utils;

    require_once("models/validatorHandler.php");
    require_once("app/languages/languageManager.php");

    use PDO;
    use Models\ValidatorHandler;
    use app\languages\languageManager;

    class Validator extends ValidatorHandler {
        private $_lang;
        private $_data;
        private $_validatorHandler;

        public function validator($data){
            $this->_lang = new languageManager(LANGUAGE);
            if(empty($data)){
                return false;
            } else {
                $validator = array(
                    'success' => 'true',
                    'message' => array(),
                    'unique' => array(),
                    'uploaded' => '',
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
                                $validator['message'][$input][] = $this->_lang->getTxt('validator', 'required');
                            }
                        } else if ($param == "requiredLetter" && !empty($value)){
                            if(!preg_match('/[A-Za-z]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = $this->_lang->getTxt('validator', 'requiredLetter');
                            }
                        } else if ($param == "requiredSpecialCharacter" && !empty($value)){
                            if(!preg_match('/[^a-zA-Z\d]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = $this->_lang->getTxt('validator', 'requiredSpecialCharacter');
                            }
                        } else if ($param == "requiredNumber" && !empty($value)){
                            if(!preg_match('/[0-9]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = $this->_lang->getTxt('validator', 'requiredNumber');
                            }
                        } else if ($param == "onlyNumber" && !empty($value)){
                            if(preg_match('/[A-Za-z]/', $value) || preg_match('/[^a-zA-Z\d]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = $this->_lang->getTxt('validator', 'onlyNumber');
                            }
                        } else if($param == "noNumber" && !empty($value)){
                            if(preg_match('/[0-9]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = $this->_lang->getTxt('validator', 'noNumber');
                            }
                        } else if($param == "noLetter" && !empty($value)){
                            if(preg_match('/[A-Za-z]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = $this->_lang->getTxt('validator', 'noLetter');
                            }
                        } else if($param == "noSpecialCharacter" && !empty($value)){
                            if(strpos($value, ".")){
                                $except = str_replace(".", "", $value);
                                if(preg_match('/[^a-zA-Z\d]/', $except)){
                                    $validator['success'] = 'false';
                                    $validator[$input] = 'invalid';
                                    $validator['message'][$input][] = $this->_lang->getTxt('validator', 'noSpecialCharacter');
                                }
                            }
                        } else if ($param == "onlyLetter" && !empty($value)){
                            if(preg_match('/[0-9]/', $value) || preg_match('/[^a-zA-Z\d]/', $value)){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = $this->_lang->getTxt('validator', 'onlyLetter');
                            }
                        } else if(strpos($param, "float") !== false && !empty($value)){
                            $float_value = explode("|", $param);
                            $maxvalue = $float_value[2];
                            $minvalue = $float_value[1];
                            if(!filter_var($value, FILTER_VALIDATE_FLOAT) || (float) $value < (float) $minvalue || (float) $value > (float) $maxvalue){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = $this->_lang->getTxt('validator', 'not-float');
                            }
                        } else if (strpos($param, "min") !== false && !empty($value)){
                            $min_value = explode(":", $param);
                            if(strlen($value) < $min_value[1]){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = $this->_lang->getTxt('validator', 'min-1') . $min_value[1] . $this->_lang->getTxt('validator', 'min-2');
                            }
                        } else if (strpos($param, "max") !== false && !empty($value)){
                            $max_value = explode(":", $param);
                            if(strlen($value) > $max_value[1]){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = $this->_lang->getTxt('validator', 'max-1') . $max_value[1] . $this->_lang->getTxt('validator', 'max-2');
                            }
                        } else if(strpos($param, "cpassword") !== false && !empty($value)){
                            $cpassword_value = explode(":", $param);
                            if($value != $cpassword_value[1]){
                                $validator['success'] = 'false';
                                $validator["cpassword"] = "invalid";
                                $validator['message']["cpassword"][] = $this->_lang->getTxt('validator', 'cpassword');
                            } else {
                                $validator['cpassword'] = 'valid';
                            }
                        } else if($param == "email" && !empty($value)){
                            if(filter_var($value, FILTER_VALIDATE_EMAIL) === false){
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = $this->_lang->getTxt('validator', 'email');
                            }
                        } else if($param == "date" && !empty($value)){
                            $date = date_parse($value);
                            if (!checkdate($date['month'], $date['day'], $date['year'])) {
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = $this->_lang->getTxt('validator', 'date');
                            }
                        } else if(strpos($param, "unique") !== false && !empty($value)){
                            $dt = explode("|", $param);
                            $ignore = explode(":", $param)[1];
                            $table = $dt[1];
                            $column = explode(":",$dt[2])[0];
                            if($value != $ignore){
                                if(!$this->unique($table, htmlspecialchars($value, ENT_QUOTES), $column)){
                                    $validator['success'] = 'false';
                                    $validator[$input] = 'invalid';
                                    $validator['unique'][$input] = 'false';
                                }
                            }
                        } else if(strpos($param, "exist") !== false && !empty($value)){
                            $dt = explode("|", $param);
                            $table = $dt[1];
                            $column = $dt[2];
                            if($this->unique($table, htmlspecialchars($value, ENT_QUOTES), $column)){
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
                                    $validator['message'][$input][] = $this->_lang->getTxt('validator', 'URL');
                                }
                            } else {
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = $this->_lang->getTxt('validator', 'URL');
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
                                $validator['message'][$input][] = $this->_lang->getTxt('validator', 'equal');
                            }
                        } else if(strpos($param,"upload") !== false){
                            $dt = explode(":", $param);
                            $typeAccept = explode("|", trim($dt[1]));
                            $extensionsAccept = explode("|", trim($dt[2]));
                            if(isset($value) && !empty($value) && $value['error'] != UPLOAD_ERR_NO_FILE){
                                $file_name = htmlspecialchars($value['name'], ENT_QUOTES);
                                $file_size = (int) htmlspecialchars($value['size'], ENT_QUOTES);
                                $file_tmp = htmlspecialchars($value['tmp_name'], ENT_QUOTES);
                                $file_type= htmlspecialchars(strtolower($value['type']), ENT_QUOTES);
                                $file_name_explode = explode(".", $file_name);
                                $file_extension = htmlspecialchars(strtolower(end($file_name_explode)), ENT_QUOTES);
                                $upload_dir = "assets/uploads";
                                $newName = bin2hex(openssl_random_pseudo_bytes(5));

                                if($file_size > 0){
                                    if($file_size > 2097152){
                                        $validator['success'] = 'false';
                                        $validator[$input] = 'invalid';
                                        $validator['message'][$input][] = 'Limit of 2 MB not respected.';
                                    }

                                    if(!in_array($file_type, $typeAccept)){
                                        $validator['success'] = 'false';
                                        $validator[$input] = 'invalid';
                                        $validator['message'][$input][] = 'Type refused.';
                                    }

                                    if(!in_array($file_extension, $extensionsAccept)){
                                        $validator['success'] = 'false';
                                        $validator[$input] = 'invalid';
                                        $validator['message'][$input][] = 'Extension refused.';
                                    }
                                    
                                    if($validator[$input] == 'valid'){
                                        if(!move_uploaded_file($file_tmp,$upload_dir."/".$newName.".".$file_extension)){
                                            $validator['success'] = 'false';
                                            $validator[$input] = 'invalid';
                                            $validator['message'][$input][] = 'move failure.';
                                        } else {
                                            $validator['uploaded'] = $newName.".".$file_extension;
                                        }
                                    }
                                }
                            } else {
                                $validator['success'] = 'false';
                                $validator[$input] = 'invalid';
                                $validator['message'][$input][] = 'This field is required.';
                            }
                        }
                    }
                }
                return $validator;
            }
        }
    }
?>