<?php

namespace App\Repository;

use App\Entity\Reports;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reports|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reports|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reports[]    findAll()
 * @method Reports[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reports::class);
    }

    /**
     * @return object
     */
    public function filters($program, $platform, $status, $severity_min, $severity_max, $creatorid=null)
    {
        $qb = $this->createQueryBuilder('r');

        $addProgram = false;
        $addPlatform = false;
        $where = false;

        if($platform){
            if(!$addProgram){
                $qb = $qb->leftJoin('App\Entity\Programs', 'programs', 'WITH', 'r.program_id = programs.id');
                $addProgram = true;
            }
            $qb = $qb->leftJoin('App\Entity\Platforms', 'platforms', 'WITH', 'programs.platform_id = platforms.id');
            $addPlatform = true;
        }

        if($program){
            if(!$addProgram){
                $qb = $qb->leftJoin('App\Entity\Programs', 'programs', 'WITH', 'r.program_id = programs.id');
                $addProgram = true;
            }
        }

        if($addPlatform){
            $qb = $qb->andWhere('platforms.id = :platformid')
                ->setParameter('platformid', $platform);
        }

        if($addProgram){
            $qb = $qb->andWhere('programs.id = :programid')
                ->setParameter('programid', $program);
        }

        if($status){
            $qb = $qb->andWhere('r.status = :status')
                ->setParameter('status', $status);
        }

        if($severity_min){
            $qb = $qb->andWhere('r.severity > :severitymin')
                ->setParameter('severitymin', $severity_min);
        }

        if($severity_max){
            $qb = $qb->andWhere('r.severity < :severitymax')
                ->setParameter('severitymax', $severity_max);
        }

        if($creatorid){
            $qb = $qb->andWhere('r.creator_id = :creatorid')
                ->setParameter('creatorid', $creatorid);
        }

        return $qb->getQuery()->execute();
    }

    /**
     * @param programid count bug for a special program
     * @return int
     * Count bugs
     */
    public function countBugs(string $programid=null){
        if(!$programid){
            return (int) $this->createQueryBuilder('r')
                ->select('count(r.id)')
                ->getQuery()
                ->getSingleScalarResult();
        } else {
            return (int) $this->createQueryBuilder('r')
                ->select('count(r.id)')
                ->where('r.program_id = :programid')
                ->setParameter('programid', $programid)
                ->getQuery()
                ->getSingleScalarResult();
        }
    }

    /**
     * @param programid sum gain for a special program
     * @return int
     * sum gain
     */
    public function sumGain(string $programid=null){
        if(!$programid){
            return (int) $this->createQueryBuilder('r')
                ->select('sum(r.gain)')
                ->getQuery()
                ->getSingleScalarResult();
        } else {
            return (int) $this->createQueryBuilder('r')
                ->select('sum(r.gain)')
                ->where('r.program_id = :programid')
                ->setParameter('programid', $programid)
                ->getQuery()
                ->getSingleScalarResult();
        }
    }

    
    /**
     * @return array
     * Get bugs by severity
     */
    public function getBugBySeverity(){
        $severity = array();

        $reports = $this->createQueryBuilder('r')
            ->select('r.severity')
            ->getQuery()
            ->getResult();

        foreach($reports as $report){
            $sev = (float) $report['severity'];
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
    }

    public function earningPerMonth($year=null){
        $dates = array('01' => 0, '02' => 0, '03' => 0, '04' => 0, '05' => 0, '06' => 0, '07' => 0, '08' => 0, '09' => 0, '10' => 0, '11' => 0, '12' => 0);
        
        $reports = $this->createQueryBuilder('r')
            ->select(array('r.date', 'r.gain'))
            ->getQuery()
            ->getResult();

        foreach($reports as $report){
            if($report['date'] != NULL){
                $date = $report['date']->format('Y-m-d');
                $exploded_date = explode("-", $date);
                $month = $exploded_date[1];
                if($year != null and $year == $exploded_date[0] and $year != 'all'){
                    $dates[$month] += $report['gain'];
                } else if($year == null or $year == 'all'){
                    $dates[$month] += $report['gain'];
                }
            }
        }

        return $dates;
    }

    /**
     * @return object
     */
    public function filtersInvoices($month=null, $platform=null, $creator)
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r')
            ->where('r.creator_id = :creatorid')
            ->setParameter('creatorid', $creator);

        if($platform){
            $qb = $qb->leftJoin('App\Entity\Programs', 'programs', 'WITH', 'r.program_id = programs.id')
                ->leftJoin('App\Entity\Platforms', 'platforms', 'WITH', 'programs.platform_id = platforms.id')
                ->andWhere('platforms.id = :platformid')
                ->setParameter('platformid', $platform);
        }

        if($month){
            $reportsQuery = $qb->getQuery()->getResult();
            $reports = array();

            foreach($reportsQuery as $report){
                $date = $report->getDate();
                if($date){
                    $exploded_date = explode("-", $date);
                    if($exploded_date[1] == $month){
                        array_push($reports, $report);
                    }
                }
            }

            return $reports;
        } else {
            return $qb->getQuery()->execute();
        }
    }

    /**
     * @return int
     */
    public function bugsOpened(){
        return $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->where('r.status = :status')
            ->setParameter(':status', 'new')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return int
     */
    public function bugsAcceptedAndFixed(){
        return $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->where('r.status = :status OR r.status = :status2')
            ->setParameter(':status', 'accepted')
            ->setParameter(':status2', 'resolved')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return int
     */
    public function totalEarnings(){
        return $this->createQueryBuilder('r')
            ->select('sum(r.gain)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return int
     */
    public function criticalsBugsFounds(){
        return $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->where('r.severity >= :min')
            ->setParameter(':min', 9)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return array
     */
    public function bugsFoundsPerMonth($platform=null, $year=null){
        $qb = $this->createQueryBuilder('r')
            ->select('r');

        if($platform and $platform != "all"){
            $qb = $qb->leftJoin('App\Entity\Programs', 'programs', 'WITH', 'r.program_id = programs.id')
                ->leftJoin('App\Entity\Platforms', 'platforms', 'WITH', 'programs.platform_id = platforms.id')
                ->where('platforms.id = :platformid')
                ->setParameter('platformid', $platform);
        }

        $reports = $qb->getQuery()->getResult();

        $dates = array('01' => 0, '02' => 0, '03' => 0, '04' => 0, '05' => 0, '06' => 0, '07' => 0, '08' => 0, '09' => 0, '10' => 0, '11' => 0, '12' => 0);

        foreach($reports as $report){
            $date = $report->getDate();
            if($date){
                $exploded_date = explode("-", $date);
                $month = $exploded_date[1];
                if($year != null and $year == $exploded_date[2] and $year != 'all'){
                    $dates[$month] += 1;
                } else if($year == null or $year == 'all'){
                    $dates[$month] += 1;
                }
            }
        }

        return $dates;
    }

    /**
     * @return array
     */
    public function bugsFoundsBySeverity($platform=null, $program=null){
        $qb = $this->createQueryBuilder('r')
            ->select('r');

        $addProgram = false;
        $severity = array();

        if($platform){
            $qb = $qb->leftJoin('App\Entity\Programs', 'programs', 'WITH', 'r.program_id = programs.id')
                ->leftJoin('App\Entity\Platforms', 'platforms', 'WITH', 'programs.platform_id = platforms.id')
                ->where('platforms.id = :platformid')
                ->setParameter('platformid', $platform);
            if($program){
                $qb = $qb->andWhere('programs.id = :programid')
                    ->setParameter('programid', $program);
                $addProgram = true;
            }
        }

        if($program && !$addProgram){
            $qb = $qb->where('r.program_id = :programid')
                ->setParameter('programid', $program);
        }

        $reports = $qb->getQuery()->execute();

        foreach($reports as $report){
            $sev = (float) $report->getSeverity();
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
    }

    /**
     * @return array
     */
    public function bugsFoundsByPlatforms(){
        $platforms = array();

        $reports = $this->createQueryBuilder('r')
            ->select('platforms.name')
            ->leftJoin('App\Entity\Programs', 'programs', 'WITH', 'r.program_id = programs.id')
            ->leftJoin('App\Entity\Platforms', 'platforms', 'WITH', 'programs.platform_id = platforms.id')
            ->getQuery()
            ->execute();

        foreach($reports as $report){
            if(array_key_exists($report["name"], $platforms)){
                $platforms[$report["name"]] += 1;
            } else {
                $platforms[$report["name"]] = 1;
            }
        }

        return $platforms;
    }
}