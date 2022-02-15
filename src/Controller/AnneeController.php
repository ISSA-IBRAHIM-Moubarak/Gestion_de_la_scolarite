<?php

namespace App\Controller;
use App\Constante\Constantes;
use App\Entity\Annee;
use App\Form\EditAnneeType;
use App\Form\AnneeType;
use App\Repository\AnneeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AnneeController extends AbstractController
{

    private $anneeRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $anneeManager;


    public function __construct(AnneeRepository $anneeRepository, EntityManagerInterface $entityManager)
    {
        $this->anneeRepository = $anneeRepository;
        $this->entityManager = $entityManager;
       
    }


    /**
     * @Route("/annee", name="list_annee")
     */
    public function annee(): Response
    {
        $user = $this->getUser();
        $annees = $this->anneeRepository->getAnnees();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];

        if(!$annees){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('annee/annee.html.twig', [
            'annees' => $annees,'resultat' => $resultat,'classe' => $classe
        ]);
    }

    /**
     * @Route("/annee/new", name="create_annee")
     */
    public function createAnnee(Request $request): Response
    {
        $users = $this->getUser();
        $annee = new Annee();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(AnneeType::class, $annee);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
          $annee->setStatut(1);
            $this->entityManager->persist($annee);
            $this->entityManager->flush();
            if ($annee) {
                return $this->redirectToRoute('list_annee');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('annee/annee-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('annee/annee-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }
    /**
     * @Route("/annee/edit/{id}", name="edit_annee")
     */
    public function editAnnee(Request $request, Annee $annee): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(AnneeType::class,$annee);
       
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('annee/annee-form.html.twig', [
                'action' => 'edit',
                'annee' => $annee,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($annee);
            $this->entityManager->flush();
            if ($annee) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_ADMIN_SUPERIEUR || $primaryRole === Constantes::ROLE_ADMIN_SECONDAIRE || $primaryRole === Constantes::ROLE_ADMIN_PRIMAIRE ){
                    return $this->redirectToRoute('list_annee');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('annee/annee-form.html.twig', [
                    'action' => 'edit',
                    'annee' => $annee,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('annee/annee-form.html.twig', [
            'action' => 'edit',
            'annee' => $annee,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

    /**
     * @Route("/desactiverannee/{id}", name="desabled_annee")
     */

    public function desactiverAnnee(Request $request, annee $annee_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE){
            
                return $this->render('annee/annee.html.twig', [
                    'annee' => null,
                ]);
            }
            $annee_entity->setStatut(0);
            $entityManager->persist($annee_entity);
            $entityManager->flush();
            $annees = $this->anneeRepository->getAnnees();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_annee');
            } else {
                return $this->render('annee/annee.html.twig', [
                    'annees' => $annees,
                ]);
            }
        
            return $this->render('annee/annee.html.twig', [
                'annees' => $annees,
            ]);
    }

    /**
     * @Route("/activerannee/{id}", name="enable_annee")
     */

    public function activerAnnee(Request $request, annee $annee_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE){
                return $this->render('annee/annee.html.twig', [
                    'annee' => null,
                ]);
            }
            $annee_entity->setStatut(1);
            $entityManager->persist($annee_entity);
            $entityManager->flush();
            $annees = $this->anneeRepository->getAnnees();

            
            if ($entityManager) {
                return $this->redirectToRoute('list_annee');
            } else {
                return $this->render('annee/annee.html.twig', [
                    'annees' => $annees,
                ]);
            }
        
            return $this->render('annee/annee.html.twig', [
                'annees' => $annees,
            ]);
    }

}
