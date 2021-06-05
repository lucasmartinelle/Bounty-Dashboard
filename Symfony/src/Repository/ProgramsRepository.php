<?php

namespace App\Repository;

use App\Entity\Programs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;

/**
 * @method Programs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Programs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Programs[]    findAll()
 * @method Programs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgramsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Programs::class);
    }

    /**
      * @return json Returns an array in json with all data Like a value
      */
    public function findLike(string $param, string $value){
        if($param == "scope" && $value != ""){
            // init response
            $scopes = array();

            // get all scopes
            $query = $this->createQueryBuilder('p')
                    ->getQuery()
                    ->getResult();

            // add scope to array if contain value and if is not already in array
            foreach($query as $row){
                $scope = $row->getScope();
                $scopepiece = explode("|", $scope);
                foreach($scopepiece as $elem){
                    if(strpos($elem, $value) !== false && !in_array($elem, $scopes)){
                        array_push($scopes, $elem);
                    }
                }
            }

            // return scopes
            return json_encode($scopes);
        } elseif($param == "tags" && $value != ""){
            // init response
            $tags = array();

            // get all scopes
            $query = $this->createQueryBuilder('p')
                    ->getQuery()
                    ->getResult();

            // add tag to array if contain value and if is not already in array
            foreach($query as $row){
                $tag = $row->getTags();
                $tagpiece = explode("|", $tag);
                foreach($tagpiece as $elem){
                    if(strpos($elem, $value) !== false && !in_array($elem,$tags)){
                        array_push($tags, $elem);
                    }
                }
            }

            // return tags
            return json_encode($tags);
        }
        return json_encode("");
    }

    /**
     * @return string
     * Get platform name from a program
     */
    public function getPlatformName(string $id){
        return $this->createQueryBuilder('p')
            ->select('platform.name')
            ->leftJoin('App\Entity\Platforms', 'platform', 'WITH', 'p.platform_id = platform.id')
            ->where('p.platform_id = :platformid')
            ->setParameter('platformid', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()["name"];
    }
}
