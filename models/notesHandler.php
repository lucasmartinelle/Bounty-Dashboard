<?php
    namespace Models;

    require_once("models/Model.php");
    require_once("models/tables/Notes.php");

    use Model\Tables\Notes;
    use Models\Model;

    use PDO;

    class NotesHandler extends Model {

        /* get all programs
        *
        *  ex : $programs = $programHandler->getPrograms();
        *       $id = $programs->id();
        *
        *  return : an array of methods to retrieve the value of an attribute in a database by tuplets
        */
        public function getNotes($where=null){
            if($where != null){
                return $this->getAll('notes', 'Notes', $where);
            } else {
                return $this->getAll('notes', 'Notes');
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
        public function newNote($values){
            $stmt = 'INSERT INTO notes (`id`, `program_id`, `titre`, `text`) VALUES (';
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

        /* update program
        *
        *  ex : $set = array('id' => '...', 'title' => '...');
        *       $where = array('impact' => '...', 'severity' => '...');
        *       $program = $programHandler->updateProgram($set, $where);
        *
        *  return : true if program updated successfuly
        *           false if program couldn't be updated
        */
        public function updateNote($set, $where){
            $stmt = "UPDATE notes SET ";
            foreach($set as $key => $value){
                if($value != null){
                    $stmt .="`". $key . "` = '".$value."', ";
                }
            }
            $stmt = substr($stmt, 0, -2);
            $stmt .= ' WHERE ';
            foreach($where as $key => $value){
                $stmt .= "`". $key . "` = '".$value."' AND ";
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
        public function deleteNotes($id){
            $stmt = "DELETE FROM notes WHERE `id`='".$id."'";
            try {
                $this->statement($stmt);
                return true;
            } catch(Exception $e) {
                return false;
            }
        }
    }
?>