<?php

namespace App\Repository;

use App\Constante\Constantes;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    // /**
    //  * @return User[] Returns an array of User objects
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
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getUsers()
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.enabled = :enabled')
            ->setParameter('enabled','1')
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }

    public function getUsersByRole(string $role)
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%' . $role . '%')
            ->getQuery()
            ->getResult();
        
            return $qb;
        
    }

    public function getUsersBySuperAdmin()
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u')
            
            ->where('
            u.roles LIKE :role_admin_superieur
            or u.roles LIKE :role_admin_secondaire
            or u.roles LIKE :role_admin_primaire
            ' )
            ->setParameter('role_admin_superieur', '%' .Constantes::ROLE_ADMIN_SUPERIEUR. '%' )
            ->setParameter('role_admin_secondaire', '%' .Constantes::ROLE_ADMIN_SECONDAIRE. '%' )
            ->setParameter('role_admin_primaire', '%' .Constantes::ROLE_ADMIN_PRIMAIRE. '%' )
            ->getQuery()
            ->getResult();
        
            return $qb;
        
    }

    public function getUsersByAdmin($structure)
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u')
            
            ->where('u.structure = :structureId and (
               u.roles LIKE :role_directeur_superieur
            or u.roles LIKE :role_directeur_secondaire 
            or u.roles LIKE :role_directeur_primaire 
            or u.roles LIKE :role_censeur_superieur 
            or u.roles LIKE :role_censeur_secondaire 
            or u.roles LIKE :role_censeur_primaire 
            or u.roles LIKE :role_surveillant_superieur
            or u.roles LIKE :role_surveillant_secondaire
            or u.roles LIKE :role_surveillant_primaire
            or u.roles LIKE :role_caissier_superieur
            or u.roles LIKE :role_caissier_secondaire
            or u.roles LIKE :role_caissier_primaire
            or u.roles LIKE :role_gerant_cantine_superieur
            or u.roles LIKE :role_gerant_cantine_secondaire
            or u.roles LIKE :role_gerant_cantine_primaire
            or u.roles LIKE :role_gerant_transport_superieur
            or u.roles LIKE :role_gerant_transport_secondaire
            or u.roles LIKE :role_gerant_transport_primaire
            or u.roles LIKE :role_enseignant_superieur
            or u.roles LIKE :role_enseignant_secondaire
            or u.roles LIKE :role_enseignant_primaire
            )' )
            ->setParameter('role_directeur_superieur', '%' .Constantes::ROLE_DIRECTEUR_SUPERIEUR. '%' )
            ->setParameter('role_directeur_secondaire', '%' .Constantes::ROLE_DIRECTEUR_SECONDAIRE. '%' )
            ->setParameter('role_directeur_primaire', '%' .Constantes::ROLE_DIRECTEUR_PRIMAIRE. '%' )
            ->setParameter('role_censeur_superieur', '%' .Constantes::ROLE_CENSEUR_SUPERIEUR. '%' )
            ->setParameter('role_censeur_secondaire', '%' .Constantes::ROLE_CENSEUR_SECONDAIRE. '%' )
            ->setParameter('role_censeur_primaire', '%' .Constantes::ROLE_CENSEUR_PRIMAIRE. '%' )
            ->setParameter('role_surveillant_superieur', '%' .Constantes::ROLE_SURVEILLANT_SUPERIEUR. '%' )
            ->setParameter('role_surveillant_secondaire', '%' .Constantes::ROLE_SURVEILLANT_SECONDAIRE. '%' )
            ->setParameter('role_surveillant_primaire', '%' .Constantes::ROLE_SURVEILLANT_PRIMAIRE. '%' )
            ->setParameter('role_caissier_superieur', '%' .Constantes::ROLE_CAISSIER_SUPERIEUR. '%' )
            ->setParameter('role_caissier_secondaire', '%' .Constantes::ROLE_CAISSIER_SECONDAIRE. '%' )
            ->setParameter('role_caissier_primaire', '%' .Constantes::ROLE_CAISSIER_PRIMAIRE. '%' )
            ->setParameter('role_gerant_cantine_superieur', '%' .Constantes::ROLE_GERANT_CANTINE_SUPERIEUR. '%' )
            ->setParameter('role_gerant_cantine_secondaire', '%' .Constantes::ROLE_GERANT_CANTINE_SECONDAIRE. '%' )
            ->setParameter('role_gerant_cantine_primaire', '%' .Constantes::ROLE_GERANT_CANTINE_PRIMAIRE. '%' )
            ->setParameter('role_gerant_transport_superieur', '%' .Constantes::ROLE_GERANT_TRANPORT_SUPERIEUR. '%' )
            ->setParameter('role_gerant_transport_secondaire', '%' .Constantes::ROLE_GERANT_TRANPORT_SECONDAIRE. '%' )
            ->setParameter('role_gerant_transport_primaire', '%' .Constantes::ROLE_GERANT_TRANPORT_PRIMAIRE. '%' )
            ->setParameter('role_enseignant_superieur', '%' .Constantes::ROLE_ENSEIGNANT_SUPERIEUR. '%' )
            ->setParameter('role_enseignant_secondaire', '%' .Constantes::ROLE_ENSEIGNANT_SECONDAIRE. '%' )
            ->setParameter('role_enseignant_primaire', '%' .Constantes::ROLE_ENSEIGNANT_PRIMAIRE. '%' )
            ->setParameter('structureId', $structure )
            ->getQuery()
            ->getResult();
        
            return $qb;
        
    }

   
}
