<?php
    namespace Models;

    require_once("models/Model.php");
    use Models\Model;

    use PDO;

    class UserHandler extends Model {

        /* get users
        *
        *  ex : $users = $userHandler->getUsers();
        *       $id = $users->id();
        *
        *  return : an array of methods to retrieve the value of an attribute in a database by tuplets
        */
        public function getUsers($where=null){
            if($where != null){
                return $this->getAll('users', 'Users', $where);
            } else {
                return $this->getAll('users', 'Users');
            }
        }

        /* count users
        *
        *  ex : $userHandler->countUsers();
        *
        *  return : amount of users in the table users
        */
        public function countUsers($where=null){
            $stmt = 'SELECT count(*) FROM users';
            if($where != null){
                $stmt .= ' WHERE ';
                foreach($where as $key => $value){
                    $stmt .= "`". $key . "` = '".$value."' AND ";
                }
                $stmt = substr($stmt, 0, -5);
            }

            $req = $this->statement($stmt);
            return $req->fetch(PDO::FETCH_ASSOC)['count(*)'];
        }

        /* new user
        *  $values must be an array with this syntax :
        *    array('value of first attribute', 'value of second attribute', ....);
        *
        *  ex : $values = array('...', '....', ....);
        *       $userHandler->newUser($values);
        *
        *  return : true if users created successfuly
        *           false if users couldn't be created
        */
        public function newUser($values){
            $stmt = 'INSERT INTO users (`id`, `username`, `email`, `password`, `token`, `role`) VALUES (';
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

        /* update user
        *  $set and $where must be an array with syntax :
        *    array($column => $value, $column2 => $value2, ....);
        *
        *  ex : $set = array('token' => '...', 'username' => '...', ....);
        *       $where = array('password' => '...', 'created_at' => '...', ....);
        *       $userHandler->updateUser($set,$where);
        *
        *  return : true if users updated successfuly
        *           false if users couldn't be updated
        */
        public function updateUser($set, $where){
            $stmt = "UPDATE users SET ";
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

        /* delete user
        *  $where must be an array with syntax :
        *    array($column => $value, $column2 => $value2, ....);
        *
        *  ex : $where = array('password' => '...', 'created_at' => '...', ....);
        *       $userHandler->deleteUser($where);
        *
        *  return : true if users deleted successfuly
        *           false if users couldn't be deleted
        */
        public function deleteUser($where){
            $stmt = "DELETE users WHERE ";
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