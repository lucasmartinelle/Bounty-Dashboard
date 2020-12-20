<?php
    namespace Model\Tables;

    class Platforms {
        // Variables de la table User
        private $_id;
        private $_creator_id;
        private $_name;
        private $_description;
        private $_logo;
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

        private function setDescription($description){
            if(is_string($description) && (strlen($description) > 0)){
                $this->_description = $description;
            }
        }

        private function setLogo($logo){
            if(is_string($logo) && (strlen($logo) > 0 && strlen($logo) < 40)){
                $this->_logo = $logo;
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

        public function description(){
            return $this->_description;
        }

        public function logo(){
            return $this->_logo;
        }

        public function createdat(){
            return $this->_createdat;
        }
    }
?>