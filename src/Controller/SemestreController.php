<?php

namespace App\Controller;
use App\Constante\Constantes;
use App\Entity\Semestre;
use App\Form\EditSemestreType;
use App\Form\SemestreType;
use App\Repository\SemestreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class SemestreController extends AbstractController
{
    private $semestreRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $semestreManager;

    public function __construct(SemestreRepository $semestreRepository, EntityManagerInterface $entityManager)
    {
        $this->semestreRepository = $semestreRepository;
        $this->entityManager = $entityManager;
       
    }


    /**
     * @Route("/semestre", name="list_semestre")
     */
    public function Semestre(): Response
    {
        $user = $this->getUser();
        $semestres = $this->semestreRepository->getSemestres();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if(!$semestres){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('semestre/semestre.html.twig', [
            'semestres' => $semestres,'resultat' => $resultat,'classe' => $classe
        ]);
    }

     /**
     * @Route("/semestre/new", name="create_semestre")
     */
    public function createSemestre(Request $request): Response
    {
        $users = $this->getUser();
        $semestre = new Semestre();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(SemestreType::class, $semestre);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
          $semestre->setStatut(1);
            $this->entityManager->persist($semestre);
            $this->entityManager->flush();
            if ($semestre) {
                return $this->redirectToRoute('list_semestre');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('semestre/semestre-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('semestre/semestre-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

      /**
     * @Route("/semestre/edit/{id}", name="edit_semestre")
     */
    public function editSemestre(Request $request, Semestre $semestre): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(EditSemestreType::class,$semestre);
       
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE){              
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('semestre/semestre-form-edit.html.twig', [
                'action' => 'edit',
                'semestre' => $semestre,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($semestre);
            $this->entityManager->flush();
            if ($semestre) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_ADMIN_SUPERIEUR || $primaryRole === Constantes::ROLE_ADMIN_SECONDAIRE){
                    return $this->redirectToRoute('list_semestre');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('semestre/semestre-form-edit.html.twig', [
                    'action' => 'edit',
                    'semestre' => $semestre,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('semestre/semestre-form.html.twig', [
            'action' => 'edit',
            'semestre' => $semestre,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

        /**
     * @Route("/desactiversemestre/{id}", name="desabled_semestre")
     */

    public function desactiverSemestre(Request $request, semestre $semestre_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE){                          
                return $this->render('semestre/semestre.html.twig', [
                    'semestre' => null,
                ]);
            }
            $semestre_entity->setStatut(0);
            $entityManager->persist($semestre_entity);
            $entityManager->flush();
            $semestres = $this->semestreRepository->getSemestres();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_semestre');
            } else {
                return $this->render('semestre/semestre.html.twig', [
                    'semestres' => $semestres,
                ]);
            }
        
            return $this->render('semestre/semestre.html.twig', [
                'semestres' => $semestres,
            ]);
    }

  /**
      * @Route("/activersemestre/{id}", name="enable_semestre")
      */

      public function activerSemestre(Request $request, semestre $semestre_entity): Response
    
      {
          $user = $this->getUser();
          $roles = $user->getRoles();
          $role = $roles[0];    
          $entityManager = $this->getDoctrine()->getManager();
          if($role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE){              
                  return $this->render('semestre/semestre.html.twig', [
                      'semestre' => null,
                  ]);
              }
              $semestre_entity->setStatut(1);
              $entityManager->persist($semestre_entity);
              $entityManager->flush();
              $semestres = $this->semestreRepository->getSemestres();
              
              if ($entityManager) {
                  return $this->redirectToRoute('list_semestre');
              } else {
                  return $this->render('semestre/semestre.html.twig', [
                      'semestres' => $semestres,
                  ]);
              }
          
              return $this->render('semestre/semestre.html.twig', [
                  'semestres' => $semestres,
              ]);
      }
  

    //TODOO permettre apres de tenir compte du Semestre
    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request) {
        //throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
         $request->getSession()->invalidate();
         //return $this->redirectToRoute('login');
    }



}
