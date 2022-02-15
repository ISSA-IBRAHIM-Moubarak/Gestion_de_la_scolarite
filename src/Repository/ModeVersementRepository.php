<?php

namespace App\Repository;

use App\Entity\ModeVersement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ModeVersement|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModeVersement|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModeVersement[]    findAll()
 * @method ModeVersement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModeVersementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModeVersement::class);
    }

    // /**
    //  * @return ModeVersement[] Returns an array of ModeVersement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ModeVersement
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getModeVersements()
    {
        $qb = $this->createQueryBuilder('n')
            ->select('n')
            ->getQuery()
            ->getResult();
        return $qb;
    }
}
