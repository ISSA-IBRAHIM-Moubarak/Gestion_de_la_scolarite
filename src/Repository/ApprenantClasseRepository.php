<?php

namespace App\Repository;

use App\Entity\ApprenantClasse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ApprenantClasse|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApprenantClasse|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApprenantClasse[]    findAll()
 * @method ApprenantClasse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApprenantClasseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApprenantClasse::class);
    }

    // /**
    //  * @return ApprenantClasse[] Returns an array of ApprenantClasse objects
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
    public function findOneBySomeField($value): ?ApprenantClasse
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getApprenantClasses()
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->getQuery()
            ->getResult();
        return $qb;
    }

    public function getInscritsByClasse($salleId)
    {
        $qb = $this->createQueryBuilder('i')
            ->select('i')
            ->innerJoin('i.salle', 's')
            ->where('s.id = :salleId')
            ->setParameter('salleId',$salleId)
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }

    public function getEffectict($structure,$salle) {
        $qb = $this->createQueryBuilder('ac')
        ->select('count(ac.id) as effectif')
        ->innerJoin('ac.inscription', 'i')
        ->innerJoin('ac.salle', 'sa')
        ->innerJoin('i.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId and i.statut = :statut and sa.id = :salleId')
        ->setParameter('structureId', $structure)
        ->setParameter('statut', 1)
        ->setParameter('salleId', $salle);
        
       return $qb
        ->getQuery()
        ->getResult();
    } 
}
