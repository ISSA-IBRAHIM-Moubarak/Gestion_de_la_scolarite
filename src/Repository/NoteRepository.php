<?php

namespace App\Repository;

use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    // /**
    //  * @return Note[] Returns an array of Note objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Note
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getNotes()
    {
        $qb = $this->createQueryBuilder('n')
            ->select('n')
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }

    public function getbulletin($structure,$apprenant) {
        $qb = $this->createQueryBuilder('n')
        ->select('m.intituleModule,t.typeEvaluation,
        m.coeficient,sum(n.noteApprenant) as note,
        count(i.apprenant) as effectif,a.NomApprenant,
        a.PrenomApprenant,a.DateNaissance,a.LieuNaissance,
        f.intituleFiliere, sa.id as idSalle, m.id as idModule')
        ->innerJoin('n.tevaluation', 't')
        ->innerJoin('n.inscription', 'i')
        ->innerJoin('i.filiere', 'f')
        ->innerJoin('i.apprenant', 'a')
        ->innerJoin('n.modulesemestre', 'd')
        ->innerJoin('d.module', 'm')
        ->innerJoin('n.salle', 'sa')
        ->innerJoin('n.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId and n.statut = :statut and a.id = :apprenant')
        ->setParameter('structureId', $structure)
        ->setParameter('apprenant', $apprenant)
        ->setParameter('statut', 1) 
        ->groupBy ('m.intituleModule');

        
       return $qb
        ->getQuery()
        ->getResult();
    } 

    public function getMoyenneClasseByModule($structure,$salle,$module) {
        $qb = $this->createQueryBuilder('n')
        ->select('m.coeficient,sum(n.noteApprenant) as noteClasse,count(a.id) as effectif, sa.id as idSalle')
        ->innerJoin('n.tevaluation', 't')
        ->innerJoin('n.inscription', 'i')
        ->innerJoin('i.apprenant', 'a')
        ->innerJoin('n.modulesemestre', 'd')
        ->innerJoin('d.module', 'm')
        ->innerJoin('n.salle', 'sa')
        ->innerJoin('n.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId and sa.id = :salle and m.id = :moduleId')
        ->setParameter('structureId', $structure)
        ->setParameter('salle', $salle)
        ->setParameter('moduleId', $module);
        //->groupBy ( 'a.NomApprenant');

        
       return $qb
        ->getQuery()
        ->getResult(); 
    } 
    
    public function getNoteBymodule( $structure) {
        $qb = $this->createQueryBuilder('n')
        ->select('a.NomApprenant,a.PrenomApprenant,m.intituleModule,m.coeficient,n.noteApprenant')
        ->innerJoin('n.apprenant', 'a')
        ->leftJoin('n.apprenant', 'a')
        ->innerJoin('n.module', 'm')
        ->innerJoin('i.user', 'u')
        ->innerJoin('u.structure', 's')
        ->where('s.id = :structureId and i.statut = :statut and a.id = :apprenant')
        ->setParameter('structureId', $structure)
        ->setParameter('statut', 1)
        ->setParameter('apprenant', $apprenant);
        
       return $qb
        ->getQuery()
        ->getResult();
    } 

}
