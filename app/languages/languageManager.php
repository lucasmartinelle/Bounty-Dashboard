<?php
    namespace app\languages;

    class languageManager {
        private $json;
        private $lang;

        public function __construct(){
            $this->lang = htmlspecialchars($_COOKIE['lang'], ENT_QUOTES);
            $this->json = json_decode(file_get_contents(WEBSITE_PATH . 'app/languages/'.$this->lang . ".json"));
        }

        public function getTxt($id, $txt){
            return $this->json->$id->$txt;
        }
    }
?>