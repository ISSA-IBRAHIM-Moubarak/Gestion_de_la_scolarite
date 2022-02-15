<?php

namespace App\Controller;
use App\Constante\Constantes;
use App\Entity\Filiere;
use App\Form\EditFiliereType;
use App\Form\FiliereType;
use App\Repository\FiliereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class FiliereController extends AbstractController
{
    private $filiereRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $filiereManager;

    public function __construct(FiliereRepository $filiereRepository, EntityManagerInterface $entityManager)
    {
        $this->filiereRepository = $filiereRepository;
        $this->entityManager = $entityManager;
       
    }


    /**
     * @Route("/filiere", name="list_filiere")
     */
    public function filiere(): Response
    {
        $user = $this->getUser();
        $filieres = $this->filiereRepository->getFilieres();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if(!$filieres){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('filiere/filiere.html.twig', [
            'filieres' => $filieres,'resultat' => $resultat,'classe' => $classe
        ]);
    }

     /**
     * @Route("/filiere/new", name="create_filiere")
     */
    public function createFiliere(Request $request): Response
    {
        $users = $this->getUser();
        $filiere = new Filiere();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(FiliereType::class, $filiere);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
          $filiere->setStatut(1);
            $this->entityManager->persist($filiere);
            $this->entityManager->flush();
            if ($filiere) {
                return $this->redirectToRoute('list_filiere');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('filiere/filiere-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('filiere/filiere-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

      /**
     * @Route("/filiere/edit/{id}", name="edit_filiere")
     */
    public function editFiliere(Request $request, Filiere $filiere): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(EditFiliereType::class,$filiere);
       
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('filiere/filiere-form.html.twig', [
                'action' => 'edit',
                'filiere' => $filiere,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($filiere);
            $this->entityManager->flush();
            if ($filiere) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_ADMIN_SUPERIEUR){
                    return $this->redirectToRoute('list_filiere');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('filiere/filiere-form.html.twig', [
                    'action' => 'edit',
                    'filiere' => $filiere,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('filiere/filiere-form.html.twig', [
            'action' => 'edit',
            'filiere' => $filiere,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

        /**
     * @Route("/desactiverfiliere/{id}", name="desabled_filiere")
     */

    public function desactiverFiliere(Request $request, filiere $filiere_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR){
                return $this->render('filiere/filiere.html.twig', [
                    'filiere' => null,
                ]);
            }
            $filiere_entity->setStatut(0);
            $entityManager->persist($filiere_entity);
            $entityManager->flush();
            $filieres = $this->filiereRepository->getFilieres();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_filiere');
            } else {
                return $this->render('filiere/filiere.html.twig', [
                    'filieres' => $filieres,
                ]);
            }
        
            return $this->render('filiere/filiere.html.twig', [
                'filieres' => $filieres,
            ]);
    }

  /**
      * @Route("/activerfiliere/{id}", name="enable_filiere")
      */

    public function activerFiliere(Request $request, filiere $filiere_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR){            
                return $this->render('filiere/filiere.html.twig', [
                    'filiere' => null,
                ]);
            }
            $filiere_entity->setStatut(1);
            $entityManager->persist($filiere_entity);
            $entityManager->flush();
            $filieres = $this->filiereRepository->getFilieres();

            
            if ($entityManager) {
                return $this->redirectToRoute('list_filiere');
            } else {
                return $this->render('filiere/filiere.html.twig', [
                    'filieres' => $filieres,
                ]);
            }
        
            return $this->render('filiere/filiere.html.twig', [
                'filieres' => $filieres,
            ]);
    }

}
