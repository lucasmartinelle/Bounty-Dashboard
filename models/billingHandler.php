<?php
    namespace Models;

    require_once("models/Model.php");

    use Models\Model;

    use PDO;

    class BillingHandler extends Model {

        /* get all billings
        *
        *  ex : $billings = $billingHandler->getBillings();
        *       $id = $billings->id();
        *
        *  return : an array of methods to retrieve the value of an attribute in a database by tuplets
        */
        public function getBillings($where=null){
            if($where != null){
                return $this->getAll('billings', 'Billings', $where);
            } else {
                return $this->getAll('billings', 'Billings');
            }
        }

        /* create new billing
        *
        *  ex : $value = array('id' => '...', 'name' => '...')
        *       $billings = $billingHandler->newBilling($value);
        *
        *  return : true if program created successfuly
        *           false if program couldn't be created
        */
        public function newBilling($values){
            $stmt = 'INSERT INTO billings (`id`, `user_id`, `name`, `firstname`, `address`, `phone`, `email`, `SIRET`, `VAT`, `BANK`, `BIC`, `IBAN`) VALUES (';
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

        /* update billing
        *  $set and $where must be an array with syntax :
        *    array($column => $value, $column2 => $value2, ....);
        *
        *  ex : $set = array('name' => '...', 'BANK' => '...', ....);
        *       $where = array('user_id' => '...', ....);
        *       $billingHandler->updateBilling($set,$where);
        *
        *  return : true if users updated successfuly
        *           false if users couldn't be updated
        */
        public function updateBilling($set, $where){
            $stmt = "UPDATE billings SET ";
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
    }
?>