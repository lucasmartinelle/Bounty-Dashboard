<?php
    namespace Model\Tables;

    class Platforms {
        // Variables de la table User
        private $_id;
        private $_name;
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

        private function setName($name){
            if(is_string($name) && (strlen($name) > 0 && strlen($name) <= 200)){
                $this->_name = $name;
            }
        }

        private function setCreatedat($createdat){
            $this->_createdat = $createdat;
        }

        // GETTERS
        public function id(){
            return $this->_id;
        }

        public function name(){
            return $this->_name;
        }

        public function createdat(){
            return $this->_createdat;
        }
    }
?>