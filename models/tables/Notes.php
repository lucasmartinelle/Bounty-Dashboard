<?php
    namespace Model\Tables;

    class Notes {
        // Variables de la table User
        private $_id;
        private $_program_id;
        private $_titre;
        private $_text;

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

        private function setProgramId($id){
            if(is_string($id) && strlen($id) == 36){
                $this->_program_id = $id;
            }
        }

        private function setTitre($title){
            if(is_string($title)){
                $this->_titre = $title;
            }
        }

        private function setText($text){
            if(is_string($text)){
                $this->_text = $text;
            }
        }

        // GETTERS
        public function id(){
            return $this->_id;
        }

        public function programid(){
            return $this->_program_id;
        }

        public function titre(){
            return $this->_titre;
        }

        public function text(){
            return $this->_text;
        }
    }
?>