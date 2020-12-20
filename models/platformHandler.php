<?php
    namespace Models;

    require_once("models/Model.php");

    use Models\Model;

    use PDO;

    class platformHandler extends Model {

        /* get all platforms
        *
        *  ex : $platform = $platformHandler->getPlatforms();
        *       $id = $platform->id();
        *
        *  return : an array of methods to retrieve the value of an attribute in a database by tuplets
        */
        public function getPlatforms($where=null){
            if($where != null){
                return $this->getAll('platforms', 'Platforms', $where);
            } else {
                return $this->getAll('platforms', 'Platforms');
            }
        }

        /* create new platform
        *
        *  ex : $value = array('id' => '...', 'name' => '...')
        *       $platform = $platformHandler->newPlatform($value);
        *
        *  return : true if platform created successfuly
        *           false if platform couldn't be created
        */
        public function newPlatform($values){
            $stmt = 'INSERT INTO platforms (`id`, `creator_id`, `name`, `description`, `logo`) VALUES (';
            foreach($values as $val){
                $stmt.="'".$val . "', ";
            }
            $stmt = substr($stmt, 0, -2) . ')';

            try {
                $this->statement($stmt);
                return true;
            } catch(Exception $e) {
                return false;
            }
        }

        /* update platform
        *
        *  ex : $set = array('id' => '...', 'title' => '...');
        *       $where = array('impact' => '...', 'severity' => '...');
        *       $platform = $platformHandler->updatePlatform($set, $where);
        *
        *  return : true if platform updated successfuly
        *           false if platform couldn't be updated
        */
        public function updatePlatform($set, $where){

        }

        /* delete platform
        *  $where must be an array with syntax :
        *    array($column => $value, $column2 => $value2, ....);
        *
        *  ex : $where = array('password' => '...', 'created_at' => '...', ....);
        *       $platform = $platformHandler->deletePlatform($where);
        *
        *  return : true if platform deleted successfuly
        *           false if platform couldn't be deleted
        */
        public function deletePlatform($where){

        }
    }
?>