<?php
    namespace app\languages;

    class languageManager {
        private $json;

        public function __construct($language){
            $this->json = json_decode(file_get_contents(WEBSITE_PATH . 'app/languages/'.$language . ".json"));
        }

        public function getTxt($id, $txt){
            return $this->json->$id->$txt;
        }
    }
?>