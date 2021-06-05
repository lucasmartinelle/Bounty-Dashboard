<?php

namespace App\Repository;

use App\Entity\Platforms;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Platforms|null find($id, $lockMode = null, $lockVersion = null)
 * @method Platforms|null findOneBy(array $criteria, array $orderBy = null)
 * @method Platforms[]    findAll()
 * @method Platforms[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlatformsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Platforms::class);
    }

    // /**
    //  * @return Platforms[] Returns an array of Platforms objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Platforms
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
