<?php

namespace App\Repository;

use App\Entity\Cantine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cantine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cantine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cantine[]    findAll()
 * @method Cantine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CantineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cantine::class);
    }

    // /**
    //  * @return Cantine[] Returns an array of Cantine objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cantine
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getCantines()
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }

    public function getGerantByCantine($structure)
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

   /* public function getNombreCantine($structure,$day){
        $qb = $this->createQueryBuilder('g')
            ->select('count(g.id) as nombre')
            ->innerJoin('g.user', 'u')
            ->innerJoin('u.structure', 's')
            ->where('s.id = :structureId')
            ->setParameter('structureId', $structure);
            if ($day) {
                $qb->andwhere('g.DateDebutCantine >= :dateFrom ');
                $qb->setParameter('dateFrom', $day);
            }
            return 
            $qb->getQuery()->getResult();
        ;
        return $qb;
    } */

    public function getNombreCantineByGerant($structure,$day,$user){
        $qb = $this->createQueryBuilder('g')
        ->select('count(g.id) as nombre')
        ->innerJoin('g.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId and u.id = :userId')
        ->setParameter('structureId', $structure)
        ->setParameter('userId', $user);
        if ($day) {
            $qb->andwhere('g.DateDebutCantine >= :dateFrom ');
            $qb->setParameter('dateFrom', $day);
        }
        return 
        $qb->getQuery()->getResult();
    } 

   /* public function getMontantCantine($structure,$day){
        $qb = $this->createQueryBuilder('g')
            ->select('sum(g.montantCantine) as nombre')
            ->innerJoin('g.user', 'u')
            ->innerJoin('u.structure', 's')
            ->where('s.id = :structureId')
            ->setParameter('structureId', $structure);
            if ($day) {
                $qb->andwhere('g.DateDebutCantine >= :dateFrom ');
                $qb->setParameter('dateFrom', $day);
            }
            return 
            $qb->getQuery()->getResult();
        ;
        return $qb;
    } */

    public function getMontantCantineByGerant($structure,$day,$user){
        $qb = $this->createQueryBuilder('g')
        ->select('sum(g.montantCantine) as nombre')
        ->innerJoin('g.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId and u.id = :userId')
        ->setParameter('structureId', $structure)
        ->setParameter('userId', $user);
        if ($day) {
            $qb->andwhere('g.DateDebutCantine >= :dateFrom ');
            $qb->setParameter('dateFrom', $day);
        }
        return 
        $qb->getQuery()->getResult();
    } 
}
