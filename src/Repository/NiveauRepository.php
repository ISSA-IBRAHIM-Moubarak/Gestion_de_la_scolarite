<?php

namespace App\Repository;

use App\Entity\Niveau;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Niveau|null find($id, $lockMode = null, $lockVersion = null)
 * @method Niveau|null findOneBy(array $criteria, array $orderBy = null)
 * @method Niveau[]    findAll()
 * @method Niveau[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NiveauRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Niveau::class);
    }

    // /**
    //  * @return Niveau[] Returns an array of Niveau objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Niveau
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getNiveaus()
    {
        $qb = $this->createQueryBuilder('n')
            ->select('n')
            ->getQuery()
            ->getResult();
        return $qb;
    }


    public function getNombreVersement($structure,$day){
        $qb = $this->createQueryBuilder('v')
        ->select('sum(v.montantVersement) as nombre')
        ->innerJoin('v.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId')
        ->setParameter('structureId', $structure);
        if ($day) {
            $qb->andwhere('v.DateVersements >= :dateFrom ');
            $qb->setParameter('dateFrom', $day);
        }
        return 
        $qb->getQuery()->getResult();
    ;
    return $qb;
} 

public function getNombreApprenant($structure){
    $qb = $this->createQueryBuilder('v')
    ->select('sum(v.montantVersement) as nombre')
    ->innerJoin('v.user', 'u')
    ->innerJoin('u.structure', 's')
    ->where('s.id = :structureId')
    ->setParameter('structureId', $structure);
    return 
    $qb->getQuery()->getResult();
;
return $qb;
} 

}
