<?php
    namespace Model\Tables;

    class Reports {
        // Variables de la table User
        private $_id;
        private $_creator_id;
        private $_title;
        private $_severity;
        private $_date;
        private $_endpoint;
        private $_identifiant;
        private $_status;
        private $_gain;
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
            $split_severity = explode(".",$severity);
            if(count($split_severity) == 1){
                $reel = (int) $split_severity[0];
                if($reel >= 0 && $reel <= 99){
                    $this->_severity = $severity;
                }
            } else {
                $reel = (int) $split_severity[0];
                $decimal = (int) $split_severity[1];
                if(count($split_severity) == 2 && ($reel >= 0 && $reel <= 100) && ($decimal >= 0 && $decimal <= 99999)){
                    $this->_severity = $severity;
                }
            }
        }

        private function setDate($date){
            $this->_date = $date;
        }

        private function setEndpoint($endpoint){
            if(is_string($endpoint)){
                $this->_endpoint = $endpoint;
            }
        }

        private function setIdentifiant($identifiant){
            if(is_string($identifiant) && (strlen($identifiant) > 0 && strlen($identifiant) <= 200)){
                $this->_identifiant = $identifiant;
            }
        }

        private function setStatus($status){
            if(is_string($status) && (strlen($status) > 0 && strlen($status) <= 100)){
                $this->_status = $status;
            }
        }

        private function setGain($gain){
            $gain = (int) $gain;

            if($gain >= 0 && $gain <= 9999){
                $this->_gain = $gain;
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

        public function date(){
            return $this->_date;
        }

        public function endpoint(){
            return $this->_endpoint;
        }

        public function identifiant(){
            return $this->_identifiant;
        }

        public function status(){
            return $this->_status;
        }

        public function gain(){
            return $this->_gain;
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