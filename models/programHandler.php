<?php
    namespace Models;

    require_once("models/Model.php");

    use Models\Model;

    use PDO;

    class programHandler extends Model {

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

        /* create new program
        *
        *  ex : $value = array('id' => '...', 'title' => '...')
        *       $programs = $programHandler->newProgram($value);
        *
        *  return : true if program created successfuly
        *           false if program couldn't be created
        */
        public function newProgram($value){

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
        public function deleteProgram($where){

        }

        /* list of bugs with multiple filters
        *
        *  ex : $programs = $programHandler->bugs(true);
        *       -> will return the amount of bugs from the program
        *
        *  return : The amount of bugs in relation to the filter
        */
        public function bugs($count = false, $severity = null){
            $stmt = '';

            if($count){
                $stmt = 'SELECT count(*) FROM programs';
            } else {
                $stmt = 'SELECT * FROM programs';
            }

            $where = ' WHERE';

            if($severity != null){
                $where .= ' programs.severity = ' . $severity . ' AND';
            }
            
            if($where != ' WHERE'){
                $where = substr($where, 0, -4);
            } else {
                $where = '';
            }

            $stmt .= $where;
        }
    }
?>