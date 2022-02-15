<?php

namespace App\Repository;

use App\Entity\Retard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Retard|null find($id, $lockMode = null, $lockVersion = null)
 * @method Retard|null findOneBy(array $criteria, array $orderBy = null)
 * @method Retard[]    findAll()
 * @method Retard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RetardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Retard::class);
    }

    // /**
    //  * @return Retard[] Returns an array of Retard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Retard
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getRetardBySurveillants($structure)
    {
        $qb = $this->createQueryBuilder('i')
            ->select('i')
            ->innerJoin('i.user', 'u')
            ->where('u.structure = :structureId')
            ->setParameter('structureId', $structure)
            ->getQuery()
            ->getResult();

        return $qb;
    }


    public function getRetards()
    {
        $qb = $this->createQueryBuilder('mo')
            ->select('mo')
            ->getQuery()
            ->getResult();
        return $qb;
    }

    public function getNombreRetard($structure){
        $qb = $this->createQueryBuilder('r')
        ->select('count(r.id) as nombre')
        ->innerJoin('r.user', 'u')
        ->where('u.structure = :structureId')
        ->setParameter('structureId', $structure)
        ->getQuery()
        ->getResult()
        ;
        return $qb;
    } 

}
