<?php

namespace App\Repository;

use App\Entity\Versement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Versement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Versement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Versement[]    findAll()
 * @method Versement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VersementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Versement::class);
    }

    // /**
    //  * @return Versement[] Returns an array of Versement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Versement
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getVersements()
    {
        $qb = $this->createQueryBuilder('v')
            ->select('v')
            ->getQuery()
            ->getResult();
        return $qb;
    }

    
    public function getVersementByCaissiers($structure)
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
    public function getMontant($structure,$annee)
     {
         $qb = $this->createQueryBuilder('v')
             ->select('v.id,a.NomApprenant,a.PrenomApprenant,sum(v.montantVersement) as montantVersement,v.DateVersements,n.montant, v.statut')
             ->innerJoin('v.inscription', 'i')
             ->innerJoin('i.niveau', 'n')
             ->innerJoin('i.apprenant', 'a')
             ->innerJoin('i.user', 'u')
             ->innerJoin('v.annee', 'an')
             ->where('u.structure = :structureId and an.libelleAnneeScolaire = :annee')
             ->groupBy('v.inscription')
             ->setParameter('structureId', $structure )
             ->setParameter('annee', $annee )
             ->getQuery()
             ->getResult()
         ;
         return $qb;
     }

     public function getDetail($id)
     {
         $qb = $this->createQueryBuilder('v')
             ->select('v.id,a.NomApprenant,a.PrenomApprenant,a.Adresse,sum(v.montantVersement) as montantVersement,v.DateVersements,n.montant,n.libelleNiveau, v.statut')
             ->innerJoin('v.inscription', 'i')
             ->innerJoin('i.niveau', 'n')
             ->innerJoin('i.apprenant', 'a')
             ->innerJoin('i.user', 'u')
             ->where('v.id = :Id')
             ->groupBy('v.inscription')
             ->setParameter('Id', $id )
             ->getQuery()
             ->getResult()
         ;
         return $qb;
     }

     public function getVersementRestant($apprenant)
     {
         $qb = $this->createQueryBuilder('v')
             ->select('sum(v.montantVersement) as montantVersement,n.montant')
             ->innerJoin('v.inscription', 'i')
             ->innerJoin('i.niveau', 'n')
             ->innerJoin('i.apprenant', 'a')
             ->innerJoin('i.user', 'u')
             ->where('a.id = :apprenant')
             ->setParameter('apprenant', $apprenant )
             ->getQuery()
             ->getResult()
         ;
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

    public function getNombreVersementByCaissier($structure,$day,$user){
        $qb = $this->createQueryBuilder('v')
        ->select('sum(v.montantVersement) as nombre')
        ->innerJoin('v.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId and u.id = :userId')
        ->setParameter('structureId', $structure)
        ->setParameter('userId', $user);
        if ($day) {
            $qb->andwhere('v.DateVersements >= :dateFrom ');
            $qb->setParameter('dateFrom', $day);
        }
        return 
        $qb->getQuery()->getResult();
    }
    
    public function getRapportDateInterval($dateFrom,$dateTo,$structure) {
        $qb = $this->createQueryBuilder('v')
        ->select('a.NomApprenant,a.PrenomApprenant,f.intituleFiliere,n.libelleNiveau, v.DateVersements, v.montantVersement')
        ->innerJoin('v.inscription', 'i')
        ->innerJoin('i.apprenant', 'a')
        ->leftJoin('i.filiere', 'f')
        ->innerJoin('i.niveau', 'n')
        ->innerJoin('v.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId and i.statut = :statut')
        ->setParameter('structureId', $structure)
        ->setParameter('statut', 1)
        ->groupBy('v.DateVersements');
        if ($dateFrom) {
            $qb->andwhere('v.DateVersements >= :dateFrom ');
            $qb->setParameter('dateFrom', $dateFrom);
        }
        if ($dateTo) {
            $qb->andwhere('v.DateVersements <= :dateTo ');
            $qb->setParameter('dateTo', $dateTo);
        }
        return $qb
        ->getQuery()
        ->getResult();
    } 

    public function getGrapheVersement($structure){
        $qb = $this->createQueryBuilder('v')
            ->select('count(v.id) as nombreGrapheVersement,sum(v.montantVersement) as montantGrapheVersement,v.DateVersements as dateGrapheVersement')
            ->innerJoin('v.user', 'u')
            ->innerJoin('u.structure', 's')
            ->where('s.id = :structureId')
            ->setParameter('structureId', $structure)
            ->groupBy('v.DateVersements');
            return 
            $qb->getQuery()->getResult();
        ;
        return $qb;
    } 

    public function getEtatDateIntervalle($dateFrom,$dateTo,$structure,$niveauId) {
        $qb = $this->createQueryBuilder('v')
        ->select('a.NomApprenant,a.PrenomApprenant,f.intituleFiliere,n.libelleNiveau, v.DateVersements, v.montantVersement,
        SUM(v.montantVersement)as montantVersement,n.montant')
        ->innerJoin('v.inscription', 'i')
        ->innerJoin('i.apprenant', 'a')
        ->leftJoin('i.filiere', 'f')
        ->innerJoin('i.niveau', 'n')
        ->innerJoin('v.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId and i.statut = :statut')
        ->setParameter('structureId', $structure)
        ->setParameter('statut', 1)
        ->groupBy('v.DateVersements','n.libelleNiveau ');
        if ($niveauId) {
            $qb->andwhere('n.id = :niveauId');
            $qb->setParameter('niveauId', $niveauId);
        }
        if ($dateFrom) {
            $qb->andwhere('v.DateVersements >= :dateFrom ');
            $qb->setParameter('dateFrom', $dateFrom);
        }
        if ($dateTo) {
            $qb->andwhere('v.DateVersements <= :dateTo ');
            $qb->setParameter('dateTo', $dateTo);
        }
        return $qb
        ->getQuery()
        ->getResult();
    } 


     /*public function getMontantRestant()
     {
         $qb ="SELECT apprenant.nom_apprenant, SUM(versement.montant_versement),date_versements,niveau.montant montant_Total,niveau.montant-SUM(versement.montant_versement)
         FROM versement, inscription, niveau, apprenant
         WHERE versement.inscription_id=inscription.id AND inscription.niveau_id=niveau.id AND inscription.apprenant_id=apprenant.id GROUP by inscription_id";

         $stmt = $this->getEntityManager()->getConnection()->prepare($qb);
         $stmt->execute([]);
         return $stmt->fetchAll();

         $qb = $this->createQueryBuilder('mr')
         ->select('mr')
         ->getQuery()
         ->getResult()
         ;
         return $qb;
     }*/
}
