<?php
    namespace Models;

    require_once("models/Model.php");

    use Models\Model;
    use PDO;

    class CaptchaHandler extends Model {
        public function insertCaptcha($pubkey, $privkey){
            if($this->getPubKey() != null){
                $cur = $this->getPubKey();
                return $this->statement("UPDATE captcha SET `pubkey`='".$pubkey."', `privkey`='".$privkey."' WHERE `pubkey`='".$cur."'");
            } else {
                return $this->statement("INSERT INTO captcha (`pubkey`, `privkey`) VALUES ('".$pubkey."', '".$privkey."')");
            }
        }

        public function verifyCaptcha($captcha){
            $ReCaptchaValid = false;
            $privatekey = $this->getPrivKey();
    
            if($captcha != null or $privatekey != null){
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
            } else {
                return false;
            }
        }

        public function getPubKey(){
            $rq = $this->statement('SELECT pubkey FROM captcha');
            while($row = $rq->fetch(PDO::FETCH_ASSOC)){
                return $row['pubkey'];
            }
            return null;
        }

        private function getPrivKey(){
            $rq = $this->statement('SELECT privkey FROM captcha');
            while($row = $rq->fetch(PDO::FETCH_ASSOC)){
                return $row['privkey'];
            }
            return null;
        }
    }
?>