<?php

namespace App\Repository;

use App\Entity\Module;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Module|null find($id, $lockMode = null, $lockVersion = null)
 * @method Module|null findOneBy(array $criteria, array $orderBy = null)
 * @method Module[]    findAll()
 * @method Module[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    // /**
    //  * @return Module[] Returns an array of Module objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('mo')
            ->andWhere('mo.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('mo.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Module
    {
        return $this->createQueryBuilder('mo')
            ->andWhere('mo.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getModules()
    {
        $qb = $this->createQueryBuilder('mo')
            ->select('mo')
            ->getQuery()
            ->getResult();
        return $qb;
    }
}