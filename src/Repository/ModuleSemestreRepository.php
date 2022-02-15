<?php

namespace App\Repository;

use App\Entity\ModuleSemestre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ModuleSemestre|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModuleSemestre|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModuleSemestre[]    findAll()
 * @method ModuleSemestre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleSemestreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModuleSemestre::class);
    }

    // /**
    //  * @return ModuleSemestre[] Returns an array of ModuleSemestre objects
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
    public function findOneBySomeField($value): ?ModuleSemestre
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getModuleSemestres()
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f')
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }

    public function ModuleSemestre($idsemestre)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->innerJoin('c.module','m')
            ->innerJoin('c.semestre','s')
            ->where('s.id = :semestreId')
            ->setParameter('semestreId',$idsemestre)
            
        ;
        return $qb
        ->getQuery()
        ->getResult();
    }

}
