<?php
    namespace Models;

    require_once("models/Model.php");
    require_once("models/tables/Programs.php");

    use Model\Tables\Programs;
    use Models\Model;

    use PDO;

    class ProgramHandler extends Model {

        /* get all programs
        *
        *  ex : $programs = $programHandler->getPrograms();
        *       $id = $programs->id();
        *
        *  return : an array of methods to retrieve the value of an attribute in a database by tuplets
        */
        public function getPrograms($where=null){
            if($where != null){
                return $this->getAll('programs', 'Programs', $where);
            } else {
                return $this->getAll('programs', 'Programs');
            }
        }

        public function getLikePrograms($where){
            $stmt = 'SELECT * FROM programs WHERE ';
            foreach($where as $key => $value){
                $stmt .= "`". $key . "` LIKE '%".$value."%'";
            }
            $stmt = substr($stmt, 0, -5);
            $var = array();
            $req = $this->statement($stmt);
            while($data = $req->fetch(PDO::FETCH_ASSOC)){
                $var[] = new Programs($data);
            }
            return $var;
        }

        public function countBugs($id){
            $stmt = "SELECT count(*) FROM reports WHERE `program_id`='".$id."'";
            $req = $this->statement($stmt);
            return $req->fetch(PDO::FETCH_ASSOC)['count(*)'];
        }

        public function getGains($id){
            $stmt = "SELECT sum(gain) FROM reports WHERE `program_id`='".$id."'";
            $req = $this->statement($stmt);
            return $req->fetch(PDO::FETCH_ASSOC)['sum(gain)'];
        }

        public function getPlatform($id){
            $stmt = "SELECT P.name FROM programs JOIN platforms AS P ON P.id = programs.platform_id WHERE P.id='".$id."'";
            $req = $this->statement($stmt);
            return $req->fetch(PDO::FETCH_ASSOC)['name'];
        }

        /* create new program
        *
        *  ex : $value = array('id' => '...', 'title' => '...')
        *       $programs = $programHandler->newProgram($value);
        *
        *  return : true if program created successfuly
        *           false if program couldn't be created
        */
        public function newProgram($values){
            $stmt = 'INSERT INTO programs (`id`, `creator_id`, `name`, `scope`, `date`, `status`, `tags`, `platform_id`) VALUES (';
            foreach($values as $val){
                if($val != ''){
                    $stmt.="'".$val . "', ";
                } else {
                    $stmt .="NULL, ";
                }
            }
            $stmt = substr($stmt, 0, -2) . ')';

            try {
                $this->statement($stmt);
                return true;
            } catch(Exception $e) {
                return false;
            }
        }

        /* update program
        *
        *  ex : $set = array('id' => '...', 'title' => '...');
        *       $where = array('impact' => '...', 'severity' => '...');
        *       $program = $programHandler->updateProgram($set, $where);
        *
        *  return : true if program updated successfuly
        *           false if program couldn't be updated
        */
        public function updateProgram($set, $where){
            $stmt = "UPDATE programs SET ";
            foreach($set as $key => $value){
                if($value != ''){
                    $stmt .="`". $key . "` = '".$value."', ";
                } else {
                    $stmt .="`". $key . "` = NULL, ";
                }
            }
            $stmt = substr($stmt, 0, -2);
            $stmt .= ' WHERE ';
            foreach($where as $key => $value){
                if($value != ''){
                    $stmt .="`". $key . "` = '".$value."' AND ";
                } else {
                    $stmt .="`". $key . "` = NULL AND ";
                }
            }
            $stmt = substr($stmt, 0, -5);

            try {
                $this->statement($stmt);
                return true;
            } catch(Exception $e) {
                return false;
            }
        }

        /* delete program
        *  $where must be an array with syntax :
        *    array($column => $value, $column2 => $value2, ....);
        *
        *  ex : $where = array('password' => '...', 'created_at' => '...', ....);
        *       $program = $programHandler->deleteProgram($where);
        *
        *  return : true if program deleted successfuly
        *           false if program couldn't be deleted
        */
        public function deleteProgram($id){
            $stmt = "DELETE FROM programs WHERE `id`='".$id."'";

            try {
                $this->statement($stmt);
            } catch(Exception $e) {
                return false;
            }

            $stmt = "DELETE FROM reports WHERE `program_id`='".$id."'";

            try {
                $this->statement($stmt);
                return true;
            } catch(Exception $e) {
                return false;
            }
        }

        /* list of bugs with multiple filters
        *
        *  ex : $programs = $programHandler->bugs(true);
        *       -> will return the amount of bugs from the program
        *
        *  return : The amount of bugs in relation to the filter
        */
        public function bugsBySeverity(){
            $severity = array();
            $stmt = "SELECT severity FROM reports";
            try {
                $req = $this->statement($stmt);

                while($row = $req->fetch(PDO::FETCH_ASSOC)){
                    $sev = (string) $row['severity'];
                    if($sev == 0){
                        $sev = 'None';
                    } elseif($sev > 0 and $sev < 4){
                        $sev = 'Low';
                    } elseif($sev >= 4 and $sev < 7){
                        $sev = 'Medium';
                    } elseif($sev >= 7 and $sev < 9){
                        $sev = 'High';
                    } elseif($sev >= 9 and $sev <= 10){
                        $sev = 'Critical';
                    }
                    if(array_key_exists($sev, $severity)){
                        $severity[$sev] += 1;
                    } else {
                        $severity[$sev] = 1;
                    }
                }

                return $severity;
            } catch(Exception $e) {
                return false;
            }
        }
    }
?>