<?php

namespace App\Repository;

use App\Entity\Inscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Inscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inscription[]    findAll()
 * @method Inscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscription::class);
    }

    // /**
    //  * @return Inscription[] Returns an array of Inscription objects
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
    public function findOneBySomeField($value): ?Inscription
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getInscriptions($useId)
    {
        $qb = $this->createQueryBuilder('i')
            ->select('i')
            ->where('i.user = :userId')
            ->setParameter('userId',$useId)
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }

    public function findById()
    {
        $qb = $this->createQueryBuilder('i')
            ->select('i')
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }

    public function getUsersByCaissier($use)
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u')
            
            ->where('u.structure = :inscriptionId and (
               u.roles LIKE :role_caissier_superieur
            or u.roles LIKE :role_caissier_secondaire
            or u.roles LIKE :role_caissier_primaire
             )' )
            ->getQuery()
            ->getResult();
        
            return $qb;
        
    }

    public function getInscriptionByCaissiers($structure, $annee)
    {
        $qb = $this->createQueryBuilder('i')
            ->select('i')
            ->innerJoin('i.user', 'u')
            ->innerJoin('i.annee', 'a')
            ->where('u.structure = :structureId and a.libelleAnneeScolaire = :annee')
            ->setParameter('structureId', $structure )
            ->setParameter('annee', $annee )
            ->getQuery()
            ->getResult();
        
            return $qb;
        
    }

    public function getNombreInscription($structure,$day){
        $qb = $this->createQueryBuilder('i')
            ->select('sum(i.montantInscription) as nombre')
            ->innerJoin('i.user', 'u')
            ->innerJoin('u.structure', 's')
            ->where('s.id = :structureId')
            ->setParameter('structureId', $structure);
            if ($day) {
                $qb->andwhere('i.dateVersement >= :dateFrom ');
                $qb->setParameter('dateFrom', $day);
            }
            return 
            $qb->getQuery()->getResult();
        ;
        return $qb;
    } 

    public function getNombreInscriptionByCaissier($structure,$day,$user){
        $qb = $this->createQueryBuilder('i')
        ->select('sum(i.montantInscription) as nombre')
        ->innerJoin('i.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId and u.id = :userId')
        ->setParameter('structureId', $structure)
        ->setParameter('userId', $user);
        if ($day) {
            $qb->andwhere('i.dateVersement >= :dateFrom ');
            $qb->setParameter('dateFrom', $day);
        }
        return 
        $qb->getQuery()->getResult();
    }
    
    public function getRapportDateInterval($dateFrom,$dateTo,$structure) {
        $qb = $this->createQueryBuilder('i')
        ->select('a.NomApprenant,a.PrenomApprenant,f.intituleFiliere,n.libelleNiveau, i.dateVersement')
        ->innerJoin('i.apprenant', 'a')
        ->leftJoin('i.filiere', 'f')
        ->innerJoin('i.niveau', 'n')
        ->innerJoin('i.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId and i.statut = :statut')
        ->setParameter('structureId', $structure)
        ->setParameter('statut', 1)
        ->groupBy('i.dateVersement');
        if ($dateFrom) {
            $qb->andwhere('i.dateVersement >= :dateFrom ');
            $qb->setParameter('dateFrom', $dateFrom);
        }
        if ($dateTo) {
            $qb->andwhere('i.dateVersement <= :dateTo ');
            $qb->setParameter('dateTo', $dateTo);
        }
        return $qb
        ->getQuery()
        ->getResult();
    }

    public function getApprenantByStructureSup($niveauId,$bourseId,$structure,$annee){
        $qb = $this->createQueryBuilder('i')
        ->select('a.NomApprenant,a.PrenomApprenant,a.DateNaissance,a.LieuNaissance,a.Nationalite,a.Genre,a.Contact,a.Adresse,n.libelleNiveau,b.libelleBourse,an.libelleAnneeScolaire')
        ->innerJoin('i.apprenant', 'a')
        ->innerJoin('i.niveau', 'n')
        ->innerJoin('i.bourse', 'b')
        ->leftJoin('i.filiere', 'f')
        ->innerJoin('i.annee', 'an')
        ->innerJoin('i.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId')
        ->setParameter('structureId', $structure)
        ->groupBy('a.id');
        if ($niveauId) {
            $qb->andwhere('n.id = :niveauId');
            $qb->setParameter('niveauId', $niveauId);
        }
        if ($bourseId) {
            $qb->andwhere('b.id = :bourseId');
            $qb->setParameter('bourseId', $bourseId);
        }
        if ($annee) {
            $qb->andwhere('an.id = :annee');
            $qb->setParameter('annee', $annee);
        }
    
        return 
        $qb->getQuery()->getResult();
    }

    public function getApprenantByStructure($niveauId,$structure,$annee){
        $qb = $this->createQueryBuilder('i')
        ->select('a.NomApprenant,a.PrenomApprenant,a.DateNaissance,a.LieuNaissance,a.Nationalite,a.Genre,a.Contact,a.Adresse,n.libelleNiveau,an.libelleAnneeScolaire')
        ->innerJoin('i.apprenant', 'a')
        ->innerJoin('i.niveau', 'n')
        ->innerJoin('i.annee', 'an')
        ->innerJoin('i.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId')
        ->setParameter('structureId', $structure)
        ->groupBy('a.id');
        if ($niveauId) {
            $qb->andwhere('n.id = :niveauId');
            $qb->setParameter('niveauId', $niveauId);
        }
        if ($annee) {
            $qb->andwhere('an.id = :annee');
            $qb->setParameter('annee', $annee);
        }
    
        return 
        $qb->getQuery()->getResult();
    }


    public function getBourseByApprenant($bourseId,$structure){
        $qb = $this->createQueryBuilder('i')
        ->select('a.NomApprenant,a.PrenomApprenant,a.DateNaissance,a.LieuNaissance,a.Nationalite,a.Genre,a.Contact,a.Adresse,b.libelleBourse')
        ->innerJoin('i.apprenant', 'a')
        ->innerJoin('i.bourse', 'b')
        ->innerJoin('i.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId')
        ->setParameter('structureId', $structure)
        ->groupBy('a.PrenomApprenant');
        if ($bourseId) {
            $qb->andwhere('b.id = :bourseId');
            $qb->setParameter('bourseId', $bourseId);
        }
    
        return 
        $qb->getQuery()->getResult();
    }

    public function getGrapheInscription($structure){
        $qb = $this->createQueryBuilder('i')
            ->select('count(i.id) as nombreGrapheInscription,sum(i.montantInscription) as montantGrapheInscription,i.dateVersement as dateGrapheInscription')
            ->innerJoin('i.apprenant', 'a')
            ->innerJoin('i.user', 'u')
            ->innerJoin('u.structure', 's')
            ->where('s.id = :structureId')
            ->setParameter('structureId', $structure)
            ->groupBy('i.dateVersement');
            return 
            $qb->getQuery()->getResult();
        ;
        return $qb;
    } 

    public function getEtatInscriptionDateIntervalle($dateFrom,$dateTo,$structure) {
        $qb = $this->createQueryBuilder('i')
        ->select('a.NomApprenant,a.PrenomApprenant,f.intituleFiliere,n.libelleNiveau, i.dateVersement, i.montantInscription, sum(i.montantInscription) as montantTotale')
        ->innerJoin('i.apprenant', 'a')
        ->leftJoin('i.filiere', 'f')
        ->innerJoin('i.niveau', 'n')
        ->innerJoin('i.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId and i.statut = :statut')
        ->setParameter('structureId', $structure)
        ->setParameter('statut', 1)
        ->groupBy('i.dateVersement');
        if ($dateFrom) {
            $qb->andwhere('i.dateVersement >= :dateFrom ');
            $qb->setParameter('dateFrom', $dateFrom);
        }
        if ($dateTo) {
            $qb->andwhere('i.dateVersement <= :dateTo ');
            $qb->setParameter('dateTo', $dateTo);
        }
        return $qb
        ->getQuery()
        ->getResult();
    } 

    public function getInscritsByNiveau($niveauId)
    {
        $qb = $this->createQueryBuilder('i')
            ->select('i')
            ->innerJoin('i.niveau', 'n')
            ->where('n.id = :niveauId')
            ->setParameter('niveauId',$niveauId)
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }


    /*public function getBonEmisConsommee($status,$day){
        $qb = $this->createQueryBuilder('b')
        ->select('count(b.id) as nombre')
        ->where('b.status = :status')
        ->setParameter('status', $status);
        if ($day) {
            $qb->andwhere('b.date >= :dateFrom ');
            $qb->setParameter('dateFrom', $day);
        }
            return $qb->getQuery()
            ->getResult();
        }*/


}
