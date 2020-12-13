<?php
    namespace Model\Tables;

    class Reports {
        // Variables de la table User
        private $_id;
        private $_creator_id;
        private $_title;
        private $_severity;
        private $_endpoint;
        private $_template_id;
        private $_program_id;
        private $_stepsToReproduce;
        private $_impact;
        private $_mitigation;
        private $_resources;
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

        private function setTitle($title){
            if(is_string($title) && (strlen($title) > 0 && strlen($title) <= 200)){
                $this->_title = $title;
            }
        }

        private function setSeverity($severity){
            $severity = (float) $severity;

            $split_severity = explode(",",$severity);
            $reel = (int) $split_severity[0];
            $decimal = (int) $split_severity[1];
            if(count($split_severity) == 2 && ($reel >= 0 && $reel <= 100) && ($decimal >= 0 && $decimal <= 99999)){
                $this->_severity = $severity;
            }
        }

        private function setEndpoint($endpoint){
            if(is_string($endpoint)){
                $this->_endpoint = $endpoint;
            }
        }

        private function setTemplateid($id){
            if(is_string($id) && strlen($id) == 36){
                $this->_template_id = $id;
            }
        }

        private function setProgramid($id){
            if(is_string($id) && strlen($id) == 36){
                $this->_program_id = $id;
            }
        }

        private function setStepstoreproduce($stepsToReproduce){
            if(is_string($stepsToReproduce)){
                $this->_stepsToReproduce = $stepsToReproduce;
            }
        }

        private function setImpact($impact){
            if(is_string($impact)){
                $this->_impact = $impact;
            }
        }

        private function setMitigation($mitigation){
            if(is_string($mitigation)){
                $this->_mitigation = $mitigation;
            }
        }

        private function setResources($resources){
            if(is_string($resources)){
                $this->_resources = $resources;
            }
        }

        private function setCreatedat($createdat){
            $this->_createdat = $createdat;
        }

        // GETTERS
        public function id(){
            return $this->_id;
        }

        public function title(){
            return $this->_title;
        }

        public function severity(){
            return $this->_severity;
        }

        public function endpoint(){
            return $this->_endpoint;
        }

        public function templateid(){
            return $this->_template_id;
        }

        public function programid(){
            return $this->_program_id;
        }

        public function stepstoreproduce(){
            return $this->_stepsToReproduce;
        }

        public function impact(){
            return $this->_impact;
        }

        public function mitigation(){
            return $this->_mitigation;
        }

        public function resources(){
            return $this->_resources;
        }

        public function createdat(){
            return $this->_createdat;
        }
    }
?>