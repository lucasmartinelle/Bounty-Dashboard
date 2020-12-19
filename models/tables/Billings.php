<?php
    namespace Model\Tables;

    class Billings {
        // Variables de la table User
        private $_id;
        private $_user_id;
        private $_name;
        private $_firstname;
        private $_address;
        private $_phone;
        private $_email;
        private $_SIRET;
        private $_VAT;
        private $_bank;
        private $_BIC;
        private $_IBAN;

        // Constructeur de la classe
        public function __construct(array $data){
            $this->hydrate($data);
        }

        // hydratation des variables en passant par les setters
        public function hydrate(array $data){
            foreach($data as $key => $value){
                $method = 'set'.ucfirst($key);
                $method = str_replace('_', '', $method);

                if(method_exists($this, $method)){
                    $this->$method($value);
                }
            }
        }

        // SETTERS
        private function setId($id){
            if(is_string($id) && strlen($id) == 36){
                $this->_id = $id;
            }
        }

        private function setUserid($userid){
            if(is_string($userid) && strlen($userid) == 36){
                $this->_user_id = $userid;
            }
        }

        private function setName($name){
            if(is_string($name)){
                $this->_name = $name;
            }
        }

        private function setFirstname($firstname){
            if(is_string($firstname)){
                $this->_firstname = $firstname;
            }
        }

        private function setAddress($address){
            if(is_string($address)){
                $this->_address = $address;
            }
        }

        private function setPhone($phone){
            $phone = (int) $phone;
            
            if($phone > 0){
                $this->_phone = $phone;
            }
        }

        private function setEmail($email){
            if(is_string($email)){
                $this->_email = $email;
            }
        }

        private function setSIRET($SIRET){
            if(is_string($SIRET) && strlen($SIRET) < 14){
                $this->_SIRET = $SIRET;
            }
        }

        private function setVAT($VAT){
            if(is_string($VAT) && strlen($VAT) < 100){
                $this->_VAT = $VAT;
            }
        }

        private function setBank($bank){
            if(is_string($bank)){
                $this->_bank = $bank;
            }
        }

        private function setBIC($BIC){
            if(is_string($BIC) && strlen($BIC) < 11){
                $this->_BIC = $BIC;
            }
        }

        private function setIBAN($IBAN){
            if(is_string($IBAN) && strlen($IBAN) < 14){
                $this->_IBAN = $IBAN;
            }
        }

        // GETTERS
        public function id(){
            return $this->_id;
        }

        public function user_id(){
            return $this->_user_id;
        }

        public function name(){
            return $this->_name;
        }

        public function firstname(){
            return $this->_firstname;
        }

        public function address(){
            return $this->_address;
        }

        public function phone(){
            return $this->_phone;
        }

        public function email(){
            return $this->_email;
        }

        public function SIRET(){
            return $this->_SIRET;
        }

        public function VAT(){
            return $this->_VAT;
        }

        public function bank(){
            return $this->_bank;
        }

        public function BIC(){
            return $this->_BIC;
        }

        public function IBAN(){
            return $this->_IBAN;
        }
    }
?>