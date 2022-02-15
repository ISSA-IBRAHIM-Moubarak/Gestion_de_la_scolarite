<?php

namespace App\Controller;
use App\Entity\Salle;
use App\Form\SalleType;
use App\Form\EditSalleType;
use App\Form\EditClasseType;
use App\Constante\Constantes;
use App\Repository\SalleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SalleController extends AbstractController
{
    private $salleRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $salleManager;

    public function __construct(SalleRepository $salleRepository, EntityManagerInterface $entityManager)
    {
        $this->salleRepository = $salleRepository;
        $this->entityManager = $entityManager;
       
    }




    /**
     * @Route("/salle", name="list_salle")
     */

    public function salle(): Response
    {
        $user = $this->getUser();
        $salles = $this->salleRepository->getSalles();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if(!$salles){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('salle/salle.html.twig', [
            'salles' => $salles,'resultat' => $resultat,'classe' => $classe
        ]);
    }

    /**
     * @Route("/salle/new", name="create_salle")
     */
    public function createSalle(Request $request): Response
    {
        $users = $this->getUser();
        $salle = new Salle();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(SalleType::class, $salle);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
           // $montantSalle = $data['montantSalle'];
           // $montant = intval($montantSalle);
            //$salle->setMontantSalle($montant);
          $salle->setStatut(1);
            $this->entityManager->persist($salle);
            $this->entityManager->flush();
            if ($salle) {
                return $this->redirectToRoute('list_salle');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('salle/salle-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('salle/salle-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

     /**
     * @Route("/salle/edit/{id}", name="edit_salle")
     */
    public function editSalle(Request $request, Salle $salle): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(SalleType::class,$salle);
       
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('salle/salle-form-edit.html.twig', [
                'action' => 'edit',
                'salle' => $salle,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($salle);
            $this->entityManager->flush();
            if ($salle) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_ADMIN_SUPERIEUR || $primaryRole === Constantes::ROLE_ADMIN_SECONDAIRE || $primaryRole === Constantes::ROLE_ADMIN_PRIMAIRE ){
                    return $this->redirectToRoute('list_salle');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('salle/salle-form-edit.html.twig', [
                    'action' => 'edit',
                    'salle' => $salle,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('salle/salle-form.html.twig', [
            'action' => 'edit',
            'salle' => $salle,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

      /**
     * @Route("/desactiversalle/{id}", name="desabled_salle")
     */

    public function desactiverSalle(Request $request, salle $salle_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE){            
                return $this->render('salle/salle.html.twig', [
                    'salle' => null,
                ]);
            }
            $salle_entity->setStatut(0);
            $entityManager->persist($salle_entity);
            $entityManager->flush();
            $salles = $this->salleRepository->getSalles();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_salle');
            } else {
                return $this->render('salle/salle.html.twig', [
                    'salles' => $salles,
                ]);
            }
        
            return $this->render('salle/salle.html.twig', [
                'salles' => $salles,
            ]);
    }

     /**
     * @Route("/activersalle/{id}", name="enabled_salle")
     */

    public function activerSalle(Request $request, salle $salle_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE){
                return $this->render('salle/salle.html.twig', [
                    'salle' => null,
                ]);
            }
            $salle_entity->setStatut(1);
            $entityManager->persist($salle_entity);
            $entityManager->flush();
            $salles = $this->salleRepository->getSalles();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_salle');
            } else {
                return $this->render('salle/salle.html.twig', [
                    'salles' => $salles,
                ]);
            }
        
            return $this->render('salle/salle.html.twig', [
                'salles' => $salles,
            ]);
    }

}
