<?php

namespace App\Repository;

use App\Entity\Absence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Absence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Absence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Absence[]    findAll()
 * @method Absence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbsenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Absence::class);
    }

    // /**
    //  * @return Absence[] Returns an array of Absence objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Absence
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getAbsences()
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->getQuery()
            ->getResult();
        return $qb;
    }

    public function getAbsenceBySurveillants($structure)
    {
        $qb = $this->createQueryBuilder('i')
        ->select('i')
        ->innerJoin('i.user', 'u')
        ->where('u.structure = :structureId')
        ->setParameter('structureId', $structure )
        ->getQuery()
        ->getResult();
        
        return $qb;
    }

    public function getNombreAbsence($structure,$day){
        $qb = $this->createQueryBuilder('a')
        ->select('count(a.id) as nombre')
        ->innerJoin('a.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId')
        ->setParameter('structureId', $structure);
        if ($day) {
            $qb->andwhere('a.periode >= :dateFrom ');
            $qb->setParameter('dateFrom', $day);
        }
        return 
        $qb->getQuery()->getResult();
    ;
    return $qb;
} 

public function getNombreAbsenceBySurveillant($structure,$day,$user){
    $qb = $this->createQueryBuilder('a')
    ->select('count(a.id) as nombre')
    ->innerJoin('a.user', 'u')
    ->innerJoin('u.structure', 's')
    ->where('s.id = :structureId and u.id = :userId')
    ->setParameter('structureId', $structure)
    ->setParameter('userId', $user);
    if ($day) {
        $qb->andwhere('a.periode >= :dateFrom ');
        $qb->setParameter('dateFrom', $day);
    }
    return 
    $qb->getQuery()->getResult();
} 
}
