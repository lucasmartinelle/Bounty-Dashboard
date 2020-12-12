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
        protected function getAll($table, $obj){
            $var = [];
            $req = $this->getBdd()->prepare('SELECT * FROM ' . $table . ' ORDER BY id DESC');
            $req->execute();
            while($data = $req->fetch(PDO::FETCH_ASSOC)){
                require_once("models/tables/".$obj.".php");
                $load = 'Model\Tables\\'.$obj;
                $var[] = new $load($data);
            }
            return $var;
            $req->closeCursor();
        }

        // Insert data in the {$table} table
        // Exemple [  insert('users', array('id' => '1', 'email' => 'test@test.com', ..))  ]
        protected function insert($table, $donnees){
            try {
                $colonnes = '';
                $values = '';
                foreach($donnees as $colonne => $value){
                    $colonnes .= "`".$colonne."`, ";
                    $values .= "'".$value."', ";
                }
                $colonnes = substr($colonnes, 0, -2);
                $values = substr($values, 0, -2);
                $req = $this->getBdd()->prepare("INSERT INTO `" . $table . "` (".$colonnes.") VALUES (".$values.")");
                $req->execute();
                return true;
            } catch (Exception $e){
                return $e->getMessage();
            }
            $req->closeCursor();
        }

        // Selection of data in the {$table} table
        // Exemple [  select('users', array('id', 'username', 'validity'), array('validity' => 'on'), '0,5', array('email' => 'test'))  ]
        protected function select($table, $colonne=null, $where=null, $limit=null, $like=null){
            $stmt = "SELECT";
            try {
                if($colonne != null){
                    foreach($colonne as $key){
                        $stmt .= " `".$key."`,";
                    }
                    $stmt = substr($stmt, 0, -1);
                    $stmt .= " FROM `".$table."`";
                } else {
                    $stmt .= " * FROM `".$table."`";
                }

                if($where != null and $like == null){
                    $stmt .= " WHERE";
                    foreach($where as $key => $value){
                        $stmt .= " `".$key."`='".$value."' AND";
                    }
                    $stmt = substr($stmt, 0, -4);
                }

                if($like != null and $where == null){
                    $stmt .= " WHERE";
                    foreach($like as $key => $value){
                        $stmt .= " `".$key."` LIKE '%".$value."%' OR";
                    }
                    $stmt = substr($stmt, 0, -3);
                }

                if($limit != null){
                    $stmt .= " LIMIT " . $limit;
                }

                $req = $this->getBdd()->prepare($stmt);
                $req->execute();
                return $req;
                $req->closeCursor();
            } catch(Exception $e){
                return null;
            }
        }

        // Updating data in the {$table} table
        // Exemple [  update('users', array('validity' => 'on', 'updated_at' => '2020-01-01 00:00:00), array('username' => 'test'))  ]
        protected function update($table, $update, $where){
            try {
                $stmt = "UPDATE " . "`".$table."` SET";
                foreach($update as $key => $value){
                    $stmt .= " `".$key."`='".$value."',";
                }
                $stmt = substr($stmt, 0, -1);
                $stmt .= " WHERE";
                foreach($where as $key => $value){
                    $stmt .= " `".$key."`='".$value."' AND";
                }
                $stmt = substr($stmt, 0, -4);
                $req = $this->getBdd()->prepare($stmt);
                $req->execute();
                return true;
                $req->closeCursor();
            } catch (Exception $e){
                return false;
            }
        }

        // Deleting data in the {$table} table
        // Exemple [  delete('users', array('id' => '1', 'username' => 'test'))  ]
        protected function delete($table, $where){
            try {
                $stmt = "DELETE FROM `".$table."` WHERE ";
                foreach($where as $key => $value){
                    $stmt .= " `".$key."`='".$value."' AND";
                }
                $stmt = substr($stmt, 0, -4);
                $req = $this->getBdd()->prepare($stmt);
                $req->execute();
                return true;
                $req->closeCursor();
            } catch (Exception $e){
                return false;
            }
        }
    }
?>