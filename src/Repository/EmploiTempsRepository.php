<?php

namespace App\Repository;

use App\Entity\EmploiTemps;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EmploiTemps|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmploiTemps|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmploiTemps[]    findAll()
 * @method EmploiTemps[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmploiTempsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmploiTemps::class);
    }

    // /**
    //  * @return EmploiTemps[] Returns an array of EmploiTemps objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EmploiTemps
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getEmploiTempss()
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f')
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }
}
