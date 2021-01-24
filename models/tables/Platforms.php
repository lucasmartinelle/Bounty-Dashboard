<?php
    namespace Model\Tables;

    class Platforms {
        // Variables de la table User
        private $_id;
        private $_creator_id;
        private $_name;
        private $_client;
        private $_BTW;
        private $_address;
        private $_email;
        private $_date;
        private $_created_at;

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

        private function setCreatorid($id){
            if(is_string($id) && strlen($id) == 36){
                $this->_creator_id = $id;
            }
        }

        private function setName($name){
            if(is_string($name) && (strlen($name) > 0 && strlen($name) <= 200)){
                $this->_name = $name;
            }
        }

        private function setClient($client){
            if(is_string($client) or $client == NULL){
                $this->_client = $client;
            }
        }

        private function setBTW($BTW){
            if(is_string($BTW) or $BTW == NULL){
                $this->_BTW = $BTW;
            }
        }

        private function setAddress($address){
            if(is_string($address) or $address == NULL){
                $this->_address = $address;
            }
        }

        private function setEmail($email){
            if(is_string($email) && (strlen($email) > 0 && strlen($email) <= 255) or $email == NULL){
                $this->_email = $email;
            }
        }

        private function setDate($date){
            if(is_string($date) or $date == NULL){
                $this->_date = $date;
            }
        }

        private function setCreatedat($createdat){
            $this->_createdat = $createdat;
        }

        // GETTERS
        public function id(){
            return $this->_id;
        }

        public function creatorid(){
            return $this->_creator_id;
        }

        public function name(){
            return $this->_name;
        }

        public function client(){
            return $this->_client;
        }

        public function BTW(){
            return $this->_BTW;
        }

        public function address(){
            return $this->_address;
        }

        public function email(){
            return $this->_email;
        }

        public function date(){
            return $this->_date;
        }

        public function createdat(){
            return $this->_createdat;
        }
    }
?>