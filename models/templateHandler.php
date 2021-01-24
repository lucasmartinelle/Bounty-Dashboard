<?php
    namespace Models;

    require_once("models/Model.php");

    use Models\Model;

    use PDO;

    class TemplateHandler extends Model {

        /* get all templates
        *
        *  ex : $templates = $templateHandler->getTemplates();
        *       $id = $templates->id();
        *
        *  return : an array of methods to retrieve the value of an attribute in a database by tuplets
        */
        public function getTemplates($where=null){
            if($where != null){
                return $this->getAll('templates', 'Templates', $where);
            } else {
                return $this->getAll('templates', 'Templates');
            }
        }

        /* create new template
        *
        *  ex : $value = array('id' => '...', 'title' => '...')
        *       $templates = $templateHandler->newTemplate($value);
        *
        *  return : true if template created successfuly
        *           false if template couldn't be created
        */
        public function newTemplate($columns, $values){
            $stmt = 'INSERT INTO templates (';
            
            foreach($columns as $column){
                $stmt.="`".$column."`, ";
            }

            $stmt = substr($stmt, 0, -2) . ') VALUES (';

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

        /* update template
        *
        *  ex : $set = array('id' => '...', 'title' => '...');
        *       $where = array('impact' => '...', 'severity' => '...');
        *       $template = $templateHandler->updatetemplate($set, $where);
        *
        *  return : true if template updated successfuly
        *           false if template couldn't be updated
        */
        public function updatetemplate($set, $where){
            $stmt = "UPDATE templates SET ";
            foreach($set as $key => $value){
                $stmt .="`". $key . "` = '".$value."', ";
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

        /* delete template
        *  $where must be an array with syntax :
        *    array($column => $value, $column2 => $value2, ....);
        *
        *  ex : $where = array('password' => '...', 'created_at' => '...', ....);
        *       $template = $templateHandler->deleteTemplate($where);
        *
        *  return : true if template deleted successfuly
        *           false if template couldn't be deleted
        */
        public function deleteTemplate($where){
            $stmt = "DELETE FROM templates WHERE ";
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
    }
?>