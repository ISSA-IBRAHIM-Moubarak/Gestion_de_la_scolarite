<?php

namespace App\Controller;
use App\Constante\Constantes;
use App\Entity\ModeVersement;
use App\Form\EditModeVersementType;
use App\Form\ModeVersementType;
use App\Repository\ModeVersementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ModeVersementController extends AbstractController
{
    private $modeVersementRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $modeVersementManager;

    public function __construct(ModeVersementRepository $modeVersementRepository, EntityManagerInterface $entityManager)
    {
        $this->modeVersementRepository = $modeVersementRepository;
        $this->entityManager = $entityManager;
       
    }


    /**
     * @Route("/modeversement", name="list_modeversement")
     */
    public function modeVersement(): Response
    {
        $user = $this->getUser();
        $modeversements = $this->modeVersementRepository->getModeVersements();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if(!$modeversements){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('modeversement/modeversement.html.twig', [
            'modeversements' => $modeversements,'resultat' => $resultat,'classe' => $classe
        ]);
    }

     /**
     * @Route("/modeversement/new", name="create_modeversement")
     */
    public function createModeVersement(Request $request): Response
    {
        $users = $this->getUser();
        $modeversement = new ModeVersement();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(ModeVersementType::class, $modeversement);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
          $modeversement->setStatut(1);
            $this->entityManager->persist($modeversement);
            $this->entityManager->flush();
            if ($modeversement) {
                return $this->redirectToRoute('list_modeversement');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('modeversement/modeversement-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('modeversement/modeversement-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

      /**
     * @Route("/modeversement/edit/{id}", name="edit_modeversement")
     */
    public function editModeVersement(Request $request, ModeVersement $modeversement): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(EditModeVersementType::class,$modeversement);
       
        if($role !== Constantes::ROLE_CAISSIER){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('modeversement/modeversement-form-edit.html.twig', [
                'action' => 'edit',
                'modeversement' => $modeversement,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($modeversement);
            $this->entityManager->flush();
            if ($modeversement) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_CAISSIER){
                    return $this->redirectToRoute('list_modeversement');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('modeversement/modeversement-form-edit.html.twig', [
                    'action' => 'edit',
                    'modeversement' => $modeversement,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('modeversement/modeversement-form.html.twig', [
            'action' => 'edit',
            'modeversement' => $modeversement,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

        /**
     * @Route("/desactivermodeversement/{id}", name="desabled_modeversement")
     */

    public function desactiverModeVersement(Request $request, modeversement $modeversement_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
            if($role !== Constantes::ROLE_CAISSIER){
            
                return $this->render('modeversement/modeversement.html.twig', [
                    'modeversement' => null,
                ]);
            }
            $modeversement_entity->setStatut(0);
            $entityManager->persist($modeversement_entity);
            $entityManager->flush();
            $modeversements = $this->modeVersementRepository->getModeVersements();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_modeversement');
            } else {
                return $this->render('modeversement/modeversement.html.twig', [
                    'modeversements' => $modeversements,
                ]);
            }
        
            return $this->render('modeversement/modeversement.html.twig', [
                'modeversements' => $modeversements,
            ]);
    }

  /**
      * @Route("/activermodeversement/{id}", name="enable_modeversement")
      */

      public function activerModeVersement(Request $request, modeversement $modeversement_entity): Response
    
      {
          $user = $this->getUser();
          $roles = $user->getRoles();
          $role = $roles[0];    
          $entityManager = $this->getDoctrine()->getManager();
              if($role !== Constantes::ROLE_CAISSIER){
              
                  return $this->render('modeversement/modeversement.html.twig', [
                      'modeversement' => null,
                  ]);
              }
              $modeversement_entity->setStatut(1);
              $entityManager->persist($modeversement_entity);
              $entityManager->flush();
              $modeversements = $this->modeVersementRepository->getModeVersements();
              
              if ($entityManager) {
                  return $this->redirectToRoute('list_modeversement');
              } else {
                  return $this->render('modeversement/modeversement.html.twig', [
                      'modeversements' => $modeversements,
                  ]);
              }
          
              return $this->render('modeversement/modeversement.html.twig', [
                  'modeversements' => $modeversements,
              ]);
      }
  
    //TODOO permettre apres de tenir compte du Eleve
    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request) {
        //throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
         $request->getSession()->invalidate();
         //return $this->redirectToRoute('login');
    }



}
