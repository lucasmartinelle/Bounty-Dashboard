<?php
    namespace Model\Tables;

    class Programs {
        // Variables de la table User
        private $_id;
        private $_scope;
        private $_status;
        private $_platform_id;
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

        private function setScope($scope){
            if(is_string($scope)){
                $this->_scope = $scope;
            }
        }

        private function setStatus($status){
            if(is_string($status) && (strlen($status) > 0 && strlen($status) <= 5)){
                $this->_status = $status;
            }
        }

        private function setPlatformid($id){
            if(is_string($id) && strlen($id) == 36){
                $this->_platform_id = $id;
            }
        }

        private function setCreatedat($createdat){
            $this->_createdat = $createdat;
        }

        // GETTERS
        public function id(){
            return $this->_id;
        }

        public function scope(){
            return $this->_scope;
        }

        public function platformid(){
            return $this->_program_id;
        }

        public function createdat(){
            return $this->_createdat;
        }
    }
?>