<?php
    namespace Model\Tables;

    class Users {
        // Variables de la table User
        private $_id;
        private $_username;
        private $_email;
        private $_password;
        private $_token;
        private $_role;
        private $_active;
        private $_createdat;
        private $_updatedat;
        private $_bad_attempt;
        private $_last_failed;

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

        private function setUsername($username){
            if(is_string($username) && (strlen($username) > 0 && strlen($username) <= 200)){
                $this->_username = $username;
            }
        }

        private function setEmail($email){
            if(is_string($email) && (strlen($email) > 0 && strlen($email) <= 255)){
                $this->_email = $email;
            }
        }

        private function setPassword($password){
            if(is_string($password)){
                $this->_password = $password;
            }
        }

        private function setToken($token){
            if(is_string($token)){
                $this->_token = $token;
            }
        }

        private function setRole($role){
            if(is_string($role) && ($role == "admin" || $role == "hunter")){
                $this->_role = $role;
            }
        }

        private function setActive($active){
            if(is_string($active) && ($active == "N" || $active == "Y")){
                $this->_active = $active;
            }
        }

        private function setCreatedat($createdat){
            $this->_createdat = $createdat;
        }

        private function setUpdatedat($updatedat){
            $this->_updatedat = $updatedat;
        }

        private function setBadattempt($badattempt){
            $badattempt = (int) $badattempt;

            if($badattempt >= 0 && $badattempt <= 5){
                $this->_bad_attempt = $badattempt;
            }
        }

        private function setLastfailed($lastfailed){
            $this->_last_failed = $lastfailed;
        }

        // GETTERS
        public function id(){
            return $this->_id;
        }

        public function username(){
            return $this->_username;
        }

        public function email(){
            return $this->_email;
        }

        public function password(){
            return $this->_password;
        }

        public function token(){
            return $this->_token;
        }

        public function role(){
            return $this->_role;
        }

        public function active(){
            return $this->_active;
        }

        public function createdat(){
            return $this->_createdat;
        }

        public function updatedat(){
            return $this->_updatedat;
        }

        public function badattempt(){
            return $this->_bad_attempt;
        }

        public function lastfailed(){
            return $this->_last_failed;
        }
    }
?>