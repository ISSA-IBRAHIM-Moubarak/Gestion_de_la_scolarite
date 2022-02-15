<?php

namespace App\Controller;
use DateTime;
use App\Entity\Cantine;
use App\Form\CantineType;
use App\Constante\Constantes;
use App\Form\EditCantineType;
use App\Repository\CantineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CantineController extends AbstractController
{
    private $cantineRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $cantineManager;

    public function __construct(CantineRepository $cantineRepository, EntityManagerInterface $entityManager)
    {
        $this->cantineRepository = $cantineRepository;
        $this->entityManager = $entityManager;
       
    }


    /**
     * @Route("/Cantine", name="list_Cantine")
     */
    public function Cantine(): Response
    {
        $user = $this->getUser();
        $cantines = $this->cantineRepository->getGerantByCantine($user->getStructure()->getId());
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if(!$cantines){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('cantine/cantine.html.twig', [
            'cantines' => $cantines,'resultat' => $resultat,'classe' => $classe
        ]);
    }

     /**
     * @Route("/Cantine/new", name="create_Cantine")
     */
    public function createCantine(Request $request): Response
    {
        $users = $this->getUser();
        $cantine = new Cantine();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(CantineType::class, $cantine);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $cantine->setStatut(1);
            $cantine->setUser($users);
            $this->entityManager->persist($cantine);
            $this->entityManager->flush();
            if ($cantine) {
                return $this->redirectToRoute('list_Cantine');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('cantine/cantine-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('cantine/cantine-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

      /**
     * @Route("/Cantine/edit/{id}", name="edit_Cantine")
     */
    public function editCantine(Request $request, Cantine $cantine): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(EditCantineType::class,$cantine);
       
        if($role !== Constantes::ROLE_GERANT_CANTINE_SUPERIEUR && $role !== Constantes::ROLE_GERANT_CANTINE_SECONDAIRE && $role !== Constantes::ROLE_GERANT_CANTINE_PRIMAIRE ){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('cantine/cantine-form-edit.html.twig', [
                'action' => 'edit',
                'cantine' => $cantine,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($cantine);
            $this->entityManager->flush();
            if ($cantine) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_GERANT_CANTINE_SUPERIEUR || $primaryRole === Constantes::ROLE_GERANT_CANTINE_SECONDAIRE || $primaryRole === Constantes::ROLE_GERANT_CANTINE_PRIMAIRE ){
                    return $this->redirectToRoute('list_Cantine');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('cantine/cantine-form-edit.html.twig', [
                    'action' => 'edit',
                    'cantine' => $cantine,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('cantine/cantine-form.html.twig', [
            'action' => 'edit',
            'cantine' => $cantine,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

        /**
     * @Route("/desactiverCantine/{id}", name="desabled_Cantine")
     */

    public function desactiverCantine(Request $request, cantine $cantine_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_GERANT_CANTINE_SUPERIEUR && $role !== Constantes::ROLE_GERANT_CANTINE_SECONDAIRE && $role !== Constantes::ROLE_GERANT_CANTINE_PRIMAIRE ){            
                return $this->render('cantine/cantine.html.twig', [
                    'cantine' => null,
                ]);
            }
            $cantine_entity->setStatut(0);
            $entityManager->persist($cantine_entity);
            $entityManager->flush();
            $cantines = $this->cantineRepository->getCantines();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_Cantine');
            } else {
                return $this->render('cantine/cantine.html.twig', [
                    'cantines' => $cantines,
                ]);
            }
        
            return $this->render('cantine/cantine.html.twig', [
                'cantines' => $cantines,
            ]);
    }

  /**
      * @Route("/activerCantine/{id}", name="enable_Cantine")
      */

      public function activerCantine(Request $request, cantine $cantine_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_GERANT_CANTINE_SUPERIEUR && $role !== Constantes::ROLE_GERANT_CANTINE_SECONDAIRE && $role !== Constantes::ROLE_GERANT_CANTINE_PRIMAIRE ){
                return $this->render('cantine/cantine.html.twig', [
                    'cantine' => null,
                ]);
            }
            $cantine_entity->setStatut(1);
            $entityManager->persist($cantine_entity);
            $entityManager->flush();
            $cantines = $this->cantineRepository->getCantines();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_Cantine');
            } else {
                return $this->render('cantine/cantine.html.twig', [
                    'cantines' => $cantines,
                ]);
            }
        
            return $this->render('cantine/cantine.html.twig', [
                'cantines' => $cantines,
            ]);
    }

    public function nombreCantine(): Response
    {
        $user = $this->getUser();
        $response = 0;
        $roles = $user->getRoles();
        $role = $roles[0];
        $day = (new DateTime())->setTime(0,0);

        // return new Response('ok');
        $cantines = $this->cantineRepository->getNombreCantineByGerant($user->getStructure()->getId(),$day,$user->getId());

        if($cantines){
           $nombre = intval($cantines[0]['nombre']);
            $response = new Response($nombre);
            return $response;
        }
        return new Response($response);
    }

    public function montantCantine(): Response
    {
        $user = $this->getUser();
        $response = 0;
        $roles = $user->getRoles();
        $role = $roles[0];
        $day = (new DateTime())->setTime(0,0);

        // return new Response('ok');
        $cantines = $this->cantineRepository->getMontantCantineByGerant($user->getStructure()->getId(),$day,$user->getId());

        if($cantines){
            $nombre = intval($cantines[0]['nombre']);
            $response = new Response($nombre);
            return $response;
        }
        return new Response($response);
    }
}
