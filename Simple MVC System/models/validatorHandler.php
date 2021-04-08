<?php
    namespace Models;

    require_once("models/Model.php");
    use Models\Model;

    use PDO;

    class ValidatorHandler extends Model {
        public function unique($table, $value, $column){
            $req = $this->statement("SELECT count(*) FROM " . $table . " WHERE `" . $column . "` = '".$value."'");
            while($amount = $req->fetch(PDO::FETCH_ASSOC)){
                if($amount["count(*)"] == 0){
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
?>