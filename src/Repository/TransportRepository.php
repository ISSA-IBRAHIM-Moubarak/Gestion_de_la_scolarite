<?php

namespace App\Repository;

use App\Entity\Transport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transport|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transport|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transport[]    findAll()
 * @method Transport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transport::class);
    }

    // /**
    //  * @return Transport[] Returns an array of Transport objects
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
    public function findOneBySomeField($value): ?Transport
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getTransports()
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f')
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }

    public function getGerantByTransport($structure)
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

   /* public function getNombreTransport($structure,$day){
        $qb = $this->createQueryBuilder('g')
            ->select('count(g.id) as nombre')
            ->innerJoin('g.user', 'u')
            ->innerJoin('u.structure', 's')
            ->where('s.id = :structureId')
            ->setParameter('structureId', $structure);
            if ($day) {
                $qb->andwhere('g.DateDebutTransport >= :dateFrom ');
                $qb->setParameter('dateFrom', $day);
            }
            return 
            $qb->getQuery()->getResult();
        ;
        return $qb;
    } */

   /* public function getMontantTransport($structure,$day){
        $qb = $this->createQueryBuilder('g')
            ->select('sum(g.montantTransport) as nombre')
            ->innerJoin('g.user', 'u')
            ->innerJoin('u.structure', 's')
            ->where('s.id = :structureId')
            ->setParameter('structureId', $structure);
            if ($day) {
                $qb->andwhere('g.DateDebutTransport >= :dateFrom ');
                $qb->setParameter('dateFrom', $day);
            }
            return 
            $qb->getQuery()->getResult();
        ;
        return $qb;
    } */

    public function getNombreTransportByGerant($structure,$day,$user){
        $qb = $this->createQueryBuilder('g')
        ->select('count(g.id) as nombre')
        ->innerJoin('g.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId and u.id = :userId')
        ->setParameter('structureId', $structure)
        ->setParameter('userId', $user);
        if ($day) {
            $qb->andwhere('g.DateDebutTransport >= :dateFrom ');
            $qb->setParameter('dateFrom', $day);
        }
        return 
        $qb->getQuery()->getResult();
    } 

    public function getMontantTransportByGerant($structure,$day,$user){
        $qb = $this->createQueryBuilder('g')
        ->select('sum(g.montantTransport) as nombre')
        ->innerJoin('g.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId and u.id = :userId')
        ->setParameter('structureId', $structure)
        ->setParameter('userId', $user);
        if ($day) {
            $qb->andwhere('g.DateDebutTransport >= :dateFrom ');
            $qb->setParameter('dateFrom', $day);
        }
        return 
        $qb->getQuery()->getResult();
    } 
}
