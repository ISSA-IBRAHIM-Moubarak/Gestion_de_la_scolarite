<?php

namespace App\Repository;

use App\Entity\Tevaluation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tevaluation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tevaluation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tevaluation[]    findAll()
 * @method Tevaluation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TevaluationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tevaluation::class);
    }

    // /**
    //  * @return Tevaluation[] Returns an array of Tevaluation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Tevaluation
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getTevaluations()
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t')
            ->getQuery()
            ->getResult();
        return $qb;
}
}
