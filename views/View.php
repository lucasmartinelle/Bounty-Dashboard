<?php
    namespace view;

    class View {
        private $_file;
        private $_template;
        private $_data;

        public function __construct($action, $template){
            $this->_file = 'views/'.$action.'.php';
            $this->_template = 'views/template/'.$template.'.php';
        }

        public function generate($data){
            extract($data);
            include($this->_file);
            include($this->_template);
        }
    }
?>