<?php

namespace App\Controller;
use App\Constante\Constantes;
use App\Entity\Matiere;
use App\Form\EditMatiereType;
use App\Form\MatiereType;
use App\Repository\MatiereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MatiereController extends AbstractController
{
    private $matiereRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $matiereManager;

    

    public function __construct(MatiereRepository $matiereRepository, EntityManagerInterface $entityManager)
    {
        $this->matiereRepository = $matiereRepository;
        $this->entityManager = $entityManager;
       
    }


     /**
     * @Route("/matiere", name="list_matiere")
     */
    public function matiere(): Response
    {
        $user = $this->getUser();
        $matieres = $this->matiereRepository->getMatieres();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if(!$matieres){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('matiere/matiere.html.twig', [
            'matieres' => $matieres,'resultat' => $resultat,'classe' => $classe
        ]);
    }
     /**
     * @Route("/matiere/new", name="create_matiere")
     */
    public function createMatiere(Request $request): Response
    {
        $users = $this->getUser();
        $matiere = new Matiere();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(MatiereType::class, $matiere);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $matiere->setStatut(1);
            $this->entityManager->persist($matiere);
            $this->entityManager->flush();
            if ($matiere) {
                return $this->redirectToRoute('list_matiere');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('matiere/matiere-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('matiere/matiere-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }
      /**
     * @Route("/matiere/edit/{id}", name="edit_matiere")
     */
    public function editMatiere(Request $request, Matiere $matiere): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(EditMatiereType::class,$matiere);
       
        if($role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('matiere/matiere-form.html.twig', [
                'action' => 'edit',
                'matiere' => $matiere,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($matiere);
            $this->entityManager->flush();
            if ($matiere) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_ADMIN_SECONDAIRE || $primaryRole === Constantes::ROLE_ADMIN_PRIMAIRE){
                    return $this->redirectToRoute('list_matiere');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('matiere/matiere-form.html.twig', [
                    'action' => 'edit',
                    'matiere' => $matiere,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('matiere/matiere-form.html.twig', [
            'action' => 'edit',
            'matiere' => $matiere,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

    /**
     * @Route("/desactivermatiere/{id}", name="desabled_matiere")
     */
    public function desactiverMatiere(Request $request, matiere $matiere_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE){            
                return $this->render('matiere/matiere.html.twig', [
                    'matiere' => null,
                ]);
            }
            $matiere_entity->setStatut(0);
            $entityManager->persist($matiere_entity);
            $entityManager->flush();
            $matieres = $this->matiereRepository->getMatieres();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_matiere');
            } else {
                return $this->render('matiere/matiere.html.twig', [
                    'matieres' => $matieres,
                ]);
            }
        
            return $this->render('matiere/matiere.html.twig', [
                'matieres' => $matieres,
            ]);
    }
  /**
     * @Route("/activermatiere/{id}", name="enable_matiere")
     */

    public function activerMatiere(Request $request, matiere $matiere_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE){
                return $this->render('matiere/matiere.html.twig', [
                    'matiere' => null,
                ]);
            }
            $matiere_entity->setStatut(1);
            $entityManager->persist($matiere_entity);
            $entityManager->flush();
            $matieres = $this->matiereRepository->getMatieres();

            
            if ($entityManager) {
                return $this->redirectToRoute('list_matiere');
            } else {
                return $this->render('matiere/matiere.html.twig', [
                    'matieres' => $matieres,
                ]);
            }
        
            return $this->render('matiere/matiere.html.twig', [
                'matieres' => $matieres,
            ]);
    }
    
}
