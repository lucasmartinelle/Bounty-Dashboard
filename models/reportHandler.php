<?php
    namespace Models;

    require_once("models/Model.php");
    require_once("models/tables/Reports.php");

    use Model\Tables\Reports;
    use Models\Model;

    use PDO;

    class ReportHandler extends Model {

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

        public function countReports($where=null, $in=false, $sup = false){
            $stmt = 'SELECT count(*) FROM reports';
            if($where != null){
                $stmt .= ' WHERE ';
                foreach($where as $key => $value){
                    if($in){
                        $ins = '';
                        foreach($in as $i){
                            $ins .= "'" . $i . "',";
                        }
                        $ins = substr($ins, 0, -1);
                        $stmt .= "`". $key . "` IN (".$ins.") ";
                    } else {
                        if(!$sup){
                            $stmt .= "`". $key . "` = '".$value."' AND ";
                        } else {
                            $stmt .= "`". $key . "` > '".$value."' AND ";
                        }
                    }
                }
                if(!$in){
                    $stmt = substr($stmt, 0, -5);
                }
            }

            $req = $this->statement($stmt);
            return $req->fetch(PDO::FETCH_ASSOC)['count(*)'];
        }

        public function totalGain(){
            $stmt = 'SELECT sum(gain) FROM reports';
            $req = $this->statement($stmt);
            return $req->fetch(PDO::FETCH_ASSOC)['sum(gain)'];
        }

        /* create new report
        *
        *  ex : $value = array('id' => '...', 'title' => '...')
        *       $reports = $reportHandler->newReport($value);
        *
        *  return : true if report created successfuly
        *           false if report couldn't be created
        */
        public function newReport($values, $template = false){
            $stmt;
            if($template){
                $stmt = 'INSERT INTO reports (`id`, `creator_id`, `title`, `severity`, `date`, `endpoint`, `identifiant`, `template_id`, `program_id`, `stepsToReproduce`, `impact`, `mitigation`, `resources`) VALUES (';
            } else {
                $stmt = 'INSERT INTO reports (`id`, `creator_id`, `title`, `severity`, `date`, `endpoint`, `identifiant`, `program_id`, `stepsToReproduce`, `impact`, `mitigation`, `resources`) VALUES (';
            }
            
            foreach($values as $val){
                if($val != ''){
                    $stmt.="'".$val . "', ";
                } else {
                    $stmt.="NULL, ";
                }
            }
            $stmt = substr($stmt, 0, -2) . ')';

            try {
                echo $stmt;
                $this->statement($stmt);
                return true;
            } catch(Exception $e) {
                return false;
            }
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
            $stmt = "UPDATE reports SET ";
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
                    $stmt .="`". $key . "` = '".$value." AND ";
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
            $stmt = "DELETE FROM reports WHERE ";
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

        public function bugsByPlatforms(){
            $platforms = array();
            $stmt = "SELECT pf.name FROM reports JOIN programs AS pr ON reports.program_id=pr.id JOIN platforms AS pf ON pr.platform_id=pf.id";
            try {
                $req = $this->statement($stmt);

                while($row = $req->fetch(PDO::FETCH_ASSOC)){
                    $pf = $row['name'];
                    if(array_key_exists($pf, $platforms)){
                        $platforms[$pf] += 1;
                    } else {
                        $platforms[$pf] = 1;
                    }
                }

                return $platforms;
            } catch(Exception $e) {
                return false;
            }
        }

        public function bugsBySeverity($program = null, $platform = null){
            $severity = array();
            $stmt = "SELECT severity FROM reports";
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
                $where .= " Pf.id = '" . $platform . "' AND";
            }

            if($program != null){
                if(!$addProgram){
                    $join .= ' JOIN programs AS Pg ON reports.program_id = Pg.id';
                }
                $where .= " Pg.id = '" . $program . "' AND";
            }

            if($where != ' WHERE'){
                $where = substr($where, 0, -4);
            } else {
                $where = '';
            }

            $stmt .= $join . $where;

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

        public function bugsByMonth($platform = null){
            if($_COOKIE['lang'] == 'FR'){
                $months = array(
                    '01' => 'Janvier',
                    '02' => 'Février',
                    '03' => 'Mars',
                    '04' => 'Avril',
                    '05' => 'Mai',
                    '06' => 'Juin',
                    '07' => 'Juillet',
                    '08' => 'Août',
                    '09' => 'Septembre',
                    '10' => 'Octobre',
                    '11' => 'Novembre',
                    '12' => 'Décembre',
                );
            } else {
                $months = array(
                    '01' => 'January',
                    '02' => 'February',
                    '03' => 'March',
                    '04' => 'April',
                    '05' => 'May',
                    '06' => 'June',
                    '07' => 'July',
                    '08' => 'August',
                    '09' => 'September',
                    '10' => 'October',
                    '11' => 'November',
                    '12' => 'December'
                );
            }
            $dates = array();
            $stmt = "SELECT reports.date FROM reports";
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
                $where .= " Pf.id = '" . $platform . "' AND";
            }

            if($where != ' WHERE'){
                $where = substr($where, 0, -4);
            } else {
                $where = '';
            }

            $stmt .= $join . $where;

            try {
                $req = $this->statement($stmt);

                while($row = $req->fetch(PDO::FETCH_ASSOC)){
                    $date = $row['date'];
                    if($date != NULL){
                        $explodeddate = explode("-", $date);
                        $month = $explodeddate[1];
                        if(array_key_exists($months[$month], $dates)){
                            $dates[$months[$month]] += 1;
                        } else {
                            $dates[$months[$month]] = 1;
                        }
                    }
                }

                return $dates;
            } catch(Exception $e) {
                return false;
            }
        }

        public function earningpermonth($year=null){
            $dates = array('01' => 0, '02' => 0, '03' => 0, '04' => 0, '05' => 0, '06' => 0, '07' => 0, '08' => 0, '09' => 0, '10' => 0, '11' => 0, '12' => 0);
            $stmt = "SELECT * FROM reports";

            try {
                $req = $this->statement($stmt);

                while($row = $req->fetch(PDO::FETCH_ASSOC)){
                    $date = $row['date'];
                    $explodeddate = explode("-", $date);
                    $month = $explodeddate[1];
                    if($year != null and $year == $explodeddate[0] and $year != 'all'){
                        if(array_key_exists($month, $dates)){
                            $dates[$month] += $row['gain'];
                        } else {
                            $dates[$month] = $row['gain'];
                        }
                    } else if($year == null or $year == 'all'){
                        if(array_key_exists($month, $dates)){
                            $dates[$month] += $row['gain'];
                        } else {
                            $dates[$month] = $row['gain'];
                        }
                    }
                }
                return $dates;
            } catch(Exception $e) {
                return false;
            }
        }

        /* list of bugs with multiple filters
        *
        *  ex : $reports = $reportHandler->bugs(true, '5', '17', null, 'close');
        *       -> will return the amount of bugs from the program with the id 17 in the platform with the id 5
        *
        *  return : The amount of bugs in relation to the filter
        */
        public function bugs($count = false, $program = null, $platform = null, $status = null, $severitymin = null, $severitymax = null, $month = null, $bounty = null, $creator = null){
            $stmt = '';

            if($count){
                $stmt = 'SELECT count(*) FROM reports';
            } else {
                $stmt = 'SELECT reports.* FROM reports';
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
                $where .= " Pf.id = '" . $platform . "' AND";
            }

            if($program != null){
                if(!$addProgram){
                    $join .= ' JOIN programs AS Pg ON reports.program_id = Pg.id';
                }
                $where .= " Pg.id = '" . $program . "' AND";
            }

            if($status != null){
                if($platform == null && $program == null){
                    $where .= " status = '" . $status . "' AND";
                } else {
                    $where .= " reports.status = '" . $status . "' AND";
                }
            }

            if($severitymin != null){
                if($platform == null && $program == null){
                    $where .= ' severity > ' . $severitymin . ' AND';
                } else {
                    $where .= ' reports.severity > ' . $severitymin . ' AND';
                }
            }

            
            if($severitymax != null){
                if($platform == null && $program == null){
                    $where .= ' severity < ' . $severitymax . ' AND';
                } else {
                    $where .= ' reports.severity < ' . $severitymax . ' AND';
                }
            }

            if($bounty != null){
                if($platform == null && $program == null){
                    $where .= ' gain > ' . ($bounty - 1) . ' AND';
                } else {
                    $where .= ' reports.gain > ' . ($bounty - 1) . ' AND';
                }
            }

            if($creator != null){
                if($platform == null && $program == null){
                    $where .= " creator_id = '" . $creator . "' AND";
                } else {
                    $where .= " reports.creator_id = '" . $creator . "' AND";
                }
            }
            
            if($where != ' WHERE'){
                $where = substr($where, 0, -4);
            } else {
                $where = '';
            }

            $stmt .= $join . $where;
            $var = array();
            $req = $this->statement($stmt);
            if($count){
                return $req->fetch(PDO::FETCH_ASSOC)['count(*)'];
            } else {
                while($data = $req->fetch(PDO::FETCH_ASSOC)){
                    if($month != null){
                        $dmonth = explode('-', $data['date'])[1];
                        if($dmonth == $month){
                            $var[] = new Reports($data);
                        }
                    } else {
                        $var[] = new Reports($data);
                    }
                }
                return $var;
            }
        }
    }
?>