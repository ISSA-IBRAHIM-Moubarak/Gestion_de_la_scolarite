<?php

namespace App\Controller;
use App\Constante\Constantes;
use App\Entity\UniteEnseignement;
use App\Form\UniteEnseignementType;
use App\Form\EditUniteEnseignementType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UniteEnseignementRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UniteEnseignementController extends AbstractController
{
    public $uniteEnseignementRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $uniteEnseignementManager;

    

    public function __construct(UniteEnseignementRepository $uniteEnseignementRepository, EntityManagerInterface $entityManager)
    {
        $this->uniteEnseignementRepository = $uniteEnseignementRepository;
        $this->entityManager = $entityManager;
       
    }


     /**
     * @Route("/unite", name="list_unite")
     */

    public function UniteEnseignement(): Response
    {
        $user = $this->getUser();
        $unites = $this->uniteEnseignementRepository->getUniteEnseignements();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if($unites){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('unite/unite.html.twig', [
            'unites' => $unites,'resultat' => $resultat,'classe' => $classe
        ]);
    }
     /**
     * @Route("/unite/new", name="create_unite")
     */
    public function createUniteEnseignement(Request $request): Response
    {
        $users = $this->getUser();
        $uniteEnseignement = new UniteEnseignement();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(UniteEnseignementType::class, $uniteEnseignement);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
          $uniteEnseignement->setStatut(1);
            $this->entityManager->persist($uniteEnseignement);
            $this->entityManager->flush();
            if ($uniteEnseignement) {
                return $this->redirectToRoute('list_unite');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('unite/unite-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('unite/unite-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }
      /**
     * @Route("/unite/edit/{id}", name="edit_unite")
     */
    public function editUniteEnseignement(Request $request, UniteEnseignement $uniteEnseignement): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(EditUniteEnseignementType::class,$uniteEnseignement);
       
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('unite/unite-form.html.twig', [
                'action' => 'edit',
                'uniteEnseignement' => $uniteEnseignement,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($uniteEnseignement);
            $this->entityManager->flush();
            if ($uniteEnseignement) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_ADMIN_SUPERIEUR){
                    return $this->redirectToRoute('list_unite');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('unite/unite-form.html.twig', [
                    'action' => 'edit',
                    'uniteEnseignement' => $uniteEnseignement,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('unite/unite-form.html.twig', [
            'action' => 'edit',
            'uniteEnseignement' => $uniteEnseignement,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }



    /**
     * @Route("/desactiverunite/{id}", name="desabled_unite")
     */

    public function desactiverUniteEnseignement(Request $request, uniteEnseignement $uniteEnseignement_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR){
                return $this->render('unite/unite.html.twig', [
                    'unite' => null,
                ]);
            }
            $uniteEnseignement_entity->setStatut(0);
            $entityManager->persist($uniteEnseignement_entity);
            $entityManager->flush();
            $uniteEnseignement = $this->uniteEnseignementRepository->getUniteEnseignements();

            
            if ($entityManager) {
                return $this->redirectToRoute('list_unite');
            } else {
                return $this->render('unite/unite.html.twig', [
                    'uniteEnseignement' => $uniteEnseignement,
                ]);
            }
        
            return $this->render('unite/unite.html.twig', [
                'uniteEnseignement' => $uniteEnseignement,
            ]);
    }
    
  /**
     * @Route("/activerunite/{id}", name="enable_unite")
     */

    public function activerUniteEnseignement(Request $request, uniteEnseignement $uniteEnseignement_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR){            
                return $this->render('unite/unite.html.twig', [
                    'unite' => null,
                ]);
            }
            $uniteEnseignement_entity->setStatut(1);
            $entityManager->persist($uniteEnseignement_entity);
            $entityManager->flush();
            $uniteEnseignement = $this->uniteEnseignementRepository->getUniteEnseignements();

            
            if ($entityManager) {
                return $this->redirectToRoute('list_unite');
            } else {
                return $this->render('unite/unite.html.twig', [
                    'uniteEnseignement' => $uniteEnseignement,
                ]);
            }
        
            return $this->render('unite/unite.html.twig', [
                'uniteEnseignement' => $uniteEnseignement,
            ]);
    }
   
}
