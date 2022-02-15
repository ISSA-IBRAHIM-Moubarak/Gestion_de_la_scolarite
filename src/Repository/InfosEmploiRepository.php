<?php

namespace App\Repository;

use App\Entity\InfosEmploi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InfosEmploi|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfosEmploi|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfosEmploi[]    findAll()
 * @method InfosEmploi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfosEmploiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfosEmploi::class);
    }

    // /**
    //  * @return InfosEmploi[] Returns an array of InfosEmploi objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InfosEmploi
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getInfosEmploi()
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e')
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }

    public function getImprimerEmploiSuperieur($id)
    {
        $qb = $this->createQueryBuilder('ie')
            ->select('ie.id,ie.heureDebut,ie.heureFin,ie.periode,mo.intituleModule,e.dateDebut,e.dateFin')
            ->innerJoin('ie.emploi', 'e')
            ->innerJoin('ie.matiere', 'ma')
            ->innerJoin('ie.module', 'mo')   
            ->where('e.id = :Id')
            ->setParameter('Id', $id )
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }

    public function getImprimerEmploi($id)
    {
        $qb = $this->createQueryBuilder('ie')
            ->select('ie.id,ie.heureDebut,ie.heureFin,ie.periode,ma.intituleMatiere,e.dateDebut,e.dateFin')
            ->innerJoin('ie.emploi', 'e')
            ->innerJoin('ie.matiere', 'ma')
            ->innerJoin('ie.module', 'mo')   
            ->where('e.id = :Id')
            ->setParameter('Id', $id )
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }

}
