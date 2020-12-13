<?php
    namespace Models;

    require_once("models/Model.php");

    use Models\Model;

    use PDO;

    class reportHandler extends Model {

        /* get all reports
        *
        *  ex : $reports = $reportHandler->getReports();
        *       $id = $reports->id();
        *
        *  return : an array of methods to retrieve the value of an attribute in a database by tuplets
        */
        public function getReports($where=null){
            if($where != null){
                return $this->getAll('reports', 'Reports', $where);
            } else {
                return $this->getAll('reports', 'Reports');
            }
        }

        /* create new report
        *
        *  ex : $value = array('id' => '...', 'title' => '...')
        *       $reports = $reportHandler->newReport($value);
        *
        *  return : true if report created successfuly
        *           false if report couldn't be created
        */
        public function newReport($value){

        }

        /* update report
        *
        *  ex : $set = array('id' => '...', 'title' => '...');
        *       $where = array('impact' => '...', 'severity' => '...');
        *       $report = $reportHandler->updateReport($set, $where);
        *
        *  return : true if report updated successfuly
        *           false if report couldn't be updated
        */
        public function updateReport($set, $where){

        }

        /* delete report
        *  $where must be an array with syntax :
        *    array($column => $value, $column2 => $value2, ....);
        *
        *  ex : $where = array('password' => '...', 'created_at' => '...', ....);
        *       $report = $reportHandler->deleteReport($where);
        *
        *  return : true if report deleted successfuly
        *           false if report couldn't be deleted
        */
        public function deleteReport($where){

        }

        /* list of bugs with multiple filters
        *
        *  ex : $reports = $reportHandler->bugs(true, '5', '17', null, 'close');
        *       -> will return the amount of bugs from the program with the id 17 in the platform with the id 5
        *
        *  return : The amount of bugs in relation to the filter
        */
        public function bugs($count = false, $platform = null, $program = null, $severity = null, $status = null){
            $stmt = '';

            if($count){
                $stmt = 'SELECT count(*) FROM reports';
            } else {
                $stmt = 'SELECT * FROM reports';
            }

            $join = '';
            $where = ' WHERE';
            $addProgram = false;
            $addPlatform = false;


            if($platform != null){
                if(!$addProgram){
                    $join .= ' JOIN programs AS Pg ON reports.program_id = Pg.id JOIN platforms AS Pf ON Pg.platform_id = Pf.id';
                    $addProgram = true;
                    $addPlatform = true;
                } else {
                    $join .= ' JOIN platforms AS Pf ON Pg.platform_id = Pf.id';
                    $addPlatform = true;
                }
                $where .= ' Pf.id = ' . $platform . ' AND';
            }

            if($program != null){
                if(!$addProgram){
                    $join .= ' JOIN programs AS Pg ON reports.program_id = Pg.id';
                }
                $where .= ' Pg.id = ' . $program . ' AND';
            }

            if($severity != null){
                if($platform == null && $programm == null){
                    $where .= ' severity = ' . $severity . ' AND';
                } else {
                    $where .= ' reports.severity = ' . $severity . ' AND';
                }
            }

            if($status != null){
                if($platform == null && $programm == null){
                    $where .= ' status = ' . $status . ' AND';
                } else {
                    $where .= ' reports.status = ' . $status . ' AND';
                }
            }
            
            if($where != ' WHERE'){
                $where = substr($where, 0, -4);
            } else {
                $where = '';
            }

            $stmt .= $join . $where;
        }
    }
?>