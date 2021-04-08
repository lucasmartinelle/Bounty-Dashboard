<?php

namespace App\Repository;

use App\Entity\Captcha;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class CaptchaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Captcha::class);
    }


    /**
     * get actual keys of captcha
     * @return array: an array of Captcha objects
     */
    public function getKeys()
    {
        $entityManager = $this->getEntityManager();

        $qb = $this->createQueryBuilder('c')->setMaxResults(1);

        // returns an array of Captcha objects
        return $qb->getQuery()->execute();
    }
}
