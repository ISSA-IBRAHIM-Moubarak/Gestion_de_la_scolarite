<?php

namespace App\Controller;
use App\Entity\EmploiTemps;
use App\Constante\Constantes;
use App\Form\EmploiTempsType;
use App\Form\EditEmploiTempsType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EmploiTempsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmploiTempsController extends AbstractController
{
    private $emploiTempsRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $emploiTempsManager;

    public function __construct(EmploiTempsRepository $emploiTempsRepository, EntityManagerInterface $entityManager)
    {
        $this->emploiTempsRepository = $emploiTempsRepository;
        $this->entityManager = $entityManager;
       
    }


    /**
     * @Route("/EmploiTemps", name="list_EmploiTemps")
     */
    public function EmploiTemps(): Response
    {
        $user = $this->getUser();
        $emploiTempss = $this->emploiTempsRepository->getEmploiTempss();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if(!$emploiTempss){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('emploiTemps/emploiTemps.html.twig', [
            'emploiTempss' => $emploiTempss,'resultat' => $resultat,'classe' => $classe
        ]);
    }

     /**
     * @Route("/EmploiTemps/new", name="create_EmploiTemps")
     */
    public function createEmploiTemps(Request $request): Response
    {
        $users = $this->getUser();
        $emploiTemps = new EmploiTemps();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(EmploiTempsType::class, $emploiTemps);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
          $emploiTemps->setStatut(1);
            $this->entityManager->persist($emploiTemps);
            $this->entityManager->flush();
            if ($emploiTemps) {
                return $this->redirectToRoute('list_EmploiTemps');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('emploiTemps/emploiTemps-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('emploiTemps/emploiTemps-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

      /**
     * @Route("/EmploiTemps/edit/{id}", name="edit_EmploiTemps")
     */
    public function editEmploiTemps(Request $request, EmploiTemps $emploiTemps): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(EditEmploiTempsType::class,$emploiTemps);
       
        if($role !== Constantes::ROLE_CAISSIER){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('emploiTemps/emploiTemps-form-edit.html.twig', [
                'action' => 'edit',
                'emploiTemps' => $emploiTemps,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($emploiTemps);
            $this->entityManager->flush();
            if ($emploiTemps) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_CAISSIER){
                    return $this->redirectToRoute('list_EmploiTemps');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('emploiTemps/emploiTemps-form-edit.html.twig', [
                    'action' => 'edit',
                    'emploiTemps' => $emploiTemps,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('emploiTemps/emploiTemps-form.html.twig', [
            'action' => 'edit',
            'emploiTemps' => $emploiTemps,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

        /**
     * @Route("/desactiverEmploiTemps/{id}", name="desabled_EmploiTemps")
     */

    public function desactiverEmploiTemps(Request $request, emploiTemps $emploiTemps_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
            if($role !== Constantes::ROLE_CAISSIER){
            
                return $this->render('emploiTemps/emploiTemps.html.twig', [
                    'emploiTemps' => null,
                ]);
            }
            $emploiTemps_entity->setStatut(0);
            $entityManager->persist($emploiTemps_entity);
            $entityManager->flush();
            $emploiTempss = $this->emploiTempsRepository->getEmploiTempss();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_EmploiTemps');
            } else {
                return $this->render('emploiTemps/emploiTemps.html.twig', [
                    'emploiTempss' => $emploiTempss,
                ]);
            }
        
            return $this->render('emploiTemps/emploiTemps.html.twig', [
                'emploiTempss' => $emploiTempss,
            ]);
    }

  /**
      * @Route("/activerEmploiTemps/{id}", name="enable_EmploiTemps")
      */

      public function activerEmploiTemps(Request $request, emploiTemps $emploiTemps_entity): Response
    
      {
          $user = $this->getUser();
          $roles = $user->getRoles();
          $role = $roles[0];    
          $entityManager = $this->getDoctrine()->getManager();
              if($role !== Constantes::ROLE_CAISSIER){
              
                  return $this->render('emploiTemps/emploiTemps.html.twig', [
                      'emploiTemps' => null,
                  ]);
              }
              $emploiTemps_entity->setStatut(1);
              $entityManager->persist($emploiTemps_entity);
              $entityManager->flush();
              $emploiTempss = $this->emploiTempsRepository->getEmploiTempss();
              
              if ($entityManager) {
                  return $this->redirectToRoute('list_EmploiTemps');
              } else {
                  return $this->render('emploiTemps/emploiTemps.html.twig', [
                      'emploiTempss' => $emploiTempss,
                  ]);
              }
          
              return $this->render('emploiTemps/emploiTemps.html.twig', [
                  'emploiTempss' => $emploiTempss,
              ]);
      }
  
    //TODOO permettre apres de tenir compte du EmploiTemps
    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request) {
        //throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
         $request->getSession()->invalidate();
         //return $this->redirectToRoute('login');
    }



}
