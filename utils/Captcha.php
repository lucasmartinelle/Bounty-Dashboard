<?php
    namespace utils;

    class Captcha {
        public function verifyCaptcha($captcha, $privatekey){
            $ReCaptchaValid = false;
    
            $url = "https://www.google.com/recaptcha/api/siteverify";

            $data = array(
                'secret' => $privatekey,
                'response' => $captcha,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            );

            $curlConfig = array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => $data
            );

            $ch = curl_init();

            curl_setopt_array($ch, $curlConfig);

            $response = curl_exec($ch);
            curl_close($ch);

            $jsonResponse = json_decode($response);

            if($jsonResponse->success === true){
                $ReCaptchaValid = true;
            }

            return $ReCaptchaValid;
        }
    }
?>