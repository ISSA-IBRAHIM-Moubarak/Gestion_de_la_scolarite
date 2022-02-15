<?php

namespace App\Controller;
use App\Entity\Emploi;
use App\Constante\Constantes;
use App\Form\EmploiType;
use App\Form\EditEmploiType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EmploiRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmploiController extends AbstractController
{
    private $emploiRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $emploiManager;

    public function __construct(EmploiRepository $emploiRepository, EntityManagerInterface $entityManager)
    {
        $this->emploiRepository = $emploiRepository;
        $this->entityManager = $entityManager;
       
    }


    /**
     * @Route("/Emploi", name="list_emploi")
     */
    public function Emploi(): Response
    {
        $user = $this->getUser();
        $emploi = $this->emploiRepository->getEmploi();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if(!$emploi){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('emploi/emploi.html.twig', [
            'emploi' => $emploi,'resultat' => $resultat,'classe' => $classe
        ]);
    }

     /**
     * @Route("/Emploi/new", name="create_emploi")
     */
    public function createEmploi(Request $request): Response
    {
        $users = $this->getUser();
        $emploi = new Emploi();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(EmploiType::class, $emploi);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $emploi->setStatut(1);
            $this->entityManager->persist($emploi);
            $this->entityManager->flush();
            if ($emploi) {
                return $this->redirectToRoute('list_emploi');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('emploi/emploi-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('emploi/emploi-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

      /**
     * @Route("/Emploi/edit/{id}", name="edit_emploi")
     */
    public function editEmploi(Request $request, Emploi $emploi): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(EmploiType::class,$emploi);
       
        if($role !== Constantes::ROLE_CENSEUR_SUPERIEUR && $role !== Constantes::ROLE_CENSEUR_SECONDAIRE && $role !== Constantes::ROLE_CENSEUR_PRIMAIRE ){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('emploi/emploi-form-edit.html.twig', [
                'action' => 'edit',
                'emploi' => $emploi,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($emploi);
            $this->entityManager->flush();
            if ($emploi) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_CENSEUR_SUPERIEUR || $primaryRole === Constantes::ROLE_CENSEUR_SECONDAIRE || $primaryRole === Constantes::ROLE_CENSEUR_PRIMAIRE){
                    return $this->redirectToRoute('list_emploi');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('emploi/emploi-form-edit.html.twig', [
                    'action' => 'edit',
                    'emploi' => $emploi,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('emploi/emploi-form.html.twig', [
            'action' => 'edit',
            'emploi' => $emploi,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

        /**
     * @Route("/desactiverEmploi/{id}", name="desabled_emploi")
     */

    public function desactiverEmploi(Request $request, emploi $emploi_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_CENSEUR_SUPERIEUR && $role !== Constantes::ROLE_CENSEUR_SECONDAIRE && $role !== Constantes::ROLE_CENSEUR_PRIMAIRE ){
                return $this->render('emploi/emploi.html.twig', [
                    'emploi' => null,
                ]);
            }
            $emploi_entity->setStatut(0);
            $entityManager->persist($emploi_entity);
            $entityManager->flush();
            $emploi = $this->emploiRepository->getEmploi();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_emploi');
            } else {
                return $this->render('emploi/emploi.html.twig', [
                    'emploi' => $emploi,
                ]);
            }
        
            return $this->render('emploi/emploi.html.twig', [
                'emploi' => $emploi,
            ]);
    }

  /**
      * @Route("/activerEmploi/{id}", name="enable_emploi")
      */

      public function activerEmploi(Request $request, emploi $emploi_entity): Response
    
      {
          $user = $this->getUser();
          $roles = $user->getRoles();
          $role = $roles[0];    
          $entityManager = $this->getDoctrine()->getManager();
          if($role !== Constantes::ROLE_CENSEUR_SUPERIEUR && $role !== Constantes::ROLE_CENSEUR_SECONDAIRE && $role !== Constantes::ROLE_CENSEUR_PRIMAIRE ){
                  return $this->render('emploi/emploi.html.twig', [
                      'emploi' => null,
                  ]);
              }
              $emploi_entity->setStatut(1);
              $entityManager->persist($emploi_entity);
              $entityManager->flush();
              $emploi = $this->emploiRepository->getEmploi();
              
              if ($entityManager) {
                  return $this->redirectToRoute('list_emploi');
              } else {
                  return $this->render('emploi/emploi.html.twig', [
                      'emploi' => $emploi,
                  ]);
              }
          
              return $this->render('emploi/emploi.html.twig', [
                  'emploi' => $emploi,
              ]);
      }

       /**
     * Botton detail sur iformation de l'emlpoi du temps
     * @Route("/Emploi/{id}", name="infos_emploi")
     */
    public function InfosEmploi(Emploi $id): Response
    {
        $user = $this->getUser();
        $emplois = $this->emploiRepository->findOneById($id);
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if (!$emplois) {
            $resultat = "";
            $classe = "";
        }
        return $this->render('infosEmploi/infosEmploi.html.twig', [
            'emplois' => $emplois, 'resultat' => $resultat, 'classe' => $classe
        ]);
    }
}
