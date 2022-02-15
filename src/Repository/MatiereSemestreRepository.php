<?php

namespace App\Repository;

use App\Entity\MatiereSemestre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MatiereSemestre|null find($id, $lockMode = null, $lockVersion = null)
 * @method MatiereSemestre|null findOneBy(array $criteria, array $orderBy = null)
 * @method MatiereSemestre[]    findAll()
 * @method MatiereSemestre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatiereSemestreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatiereSemestre::class);
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

    public function getMatiereSemestres()
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f')
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }


    public function MatiereSemestres($idsemestre)
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d')
            ->innerJoin('d.matiere','m')
            ->innerJoin('d.semestre','s')
            ->where('s.id = :semestreId')
            ->setParameter('semestreId',$idsemestre)
            
        ;
        return $qb
        ->getQuery()
        ->getResult();
    }

}
