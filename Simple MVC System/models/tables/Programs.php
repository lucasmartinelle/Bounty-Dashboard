<?php
    namespace Model\Tables;

    class Programs {
        // Variables de la table User
        private $_id;
        private $_creator_id;
        private $_name;
        private $_scope;
        private $_date;
        private $_status;
        private $_tags;
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

        private function setScope($scope){
            if(is_string($scope)){
                $this->_scope = $scope;
            }
        }

        private function setDate($date){
            $this->_date = $date;
        }

        private function setStatus($status){
            if(is_string($status) && (strlen($status) > 0 && strlen($status) <= 5)){
                $this->_status = $status;
            }
        }

        private function setTags($tags){
            if(is_string($tags)){
                $this->_tags = $tags;
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

        public function creator_id(){
            return $this->_creator_id;
        }

        public function name(){
            return $this->_name;
        }

        public function scope(){
            return $this->_scope;
        }

        public function date(){
            return $this->_date;
        }

        public function status(){
            return $this->_status;
        }

        public function tags(){
            return $this->_tags;
        }

        public function platformid(){
            return $this->_platform_id;
        }

        public function createdat(){
            return $this->_createdat;
        }
    }
?>