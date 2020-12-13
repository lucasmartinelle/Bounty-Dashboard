<?php
    namespace Models;
    
    use PDO;
    
    abstract class Model {
        private static $_bdd;

        // Instance of the connection to the DB
        private static function setBdd(){
            try {
                self::$_bdd = new PDO('mysql:host='.DB_HOST.';dbname='.DB_DATABASE.'', DB_USERNAME, DB_PASSWORD);
                self::$_bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$_bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch (Exception $e){
                echo $e->getMessage();
            }
        }

        // GETTER of the BDD
        protected function getBdd(){
            if(self::$_bdd == null){
                self::setBdd();
            }
            return self::$_bdd;
        }

        // Retrieves all data from table {$table} into object {$obj}
        protected function getAll($table, $obj,$where=null){
            $var = [];
            $stmt = 'SELECT * FROM ' . $table;
            if($where != null){
                $stmt .= " WHERE";
                foreach($where as $key => $value){
                    $stmt .= " `".$key."`='".$value."' AND";
                }
                $stmt = substr($stmt, 0, -4);
            }
            $req = $this->getBdd()->prepare($stmt);
            $req->execute();
            while($data = $req->fetch(PDO::FETCH_ASSOC)){
                require_once("models/tables/".$obj.".php");
                $load = 'Model\Tables\\'.$obj;
                $var[] = new $load($data);
            }
            return $var;
            $req->closeCursor();
        }

        // Execute statement
        protected function statement($stmt){
            try {
                $req = $this->getBdd()->prepare($stmt);
                $req->execute();
                return true;
            } catch (Exception $e){
                return $e->getMessage();
            }
            $req->closeCursor();
        }
    }
?>