<?php

namespace App\Repository;

use App\Entity\Apprenant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Apprenant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Apprenant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Apprenant[]    findAll()
 * @method Apprenant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApprenantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Apprenant::class);
    }

    // /**
    //  * @return Apprenant[] Returns an array of Apprenant objects
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
    public function findOneBySomeField($value): ?Apprenant
    {
        return $this->createQueryBuilder('a')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getApprenants()
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }

    public function getApprenantByCaissiers($structure)
    {
        $qb = $this->createQueryBuilder('i')
            ->select('i')
            ->innerJoin('i.user', 'u')
            ->where('u.structure = :structureId')
            ->setParameter('structureId', $structure )
            ->orderBy('i.id', 'DESC')
            ->getQuery()
            ->getResult();
        
            return $qb;
        
    }

    public function getNombreApprenant($structure){
        $qb = $this->createQueryBuilder('i')
        ->select('count(i.id) as nombre')
        ->innerJoin('i.user', 'u')
        ->where('u.structure = :structureId')
        ->setParameter('structureId', $structure)
        ->getQuery()
        ->getResult()
        ;
        return $qb;
    } 

    /*public function getNombreApprenantByCaissier($structure){
        $qb = $this->createQueryBuilder('i')
        ->select('count(i.id) as nombre')
        ->innerJoin('i.user', 'u')
        ->where('u.structure = :structureId')
        ->setParameter('structureId', $structure)
        ->groupBy('')
        ->getQuery()
        ->getResult()
        ;
        return $qb;
    }*/
}
