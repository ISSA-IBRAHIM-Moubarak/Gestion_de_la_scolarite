<?php

namespace App\Controller;
use App\Constante\Constantes;
use App\Entity\Apprenant;
use App\Form\EditApprenantType;
use App\Form\ApprenantType;
use App\Repository\ApprenantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ApprenantController extends AbstractController
{
    private $apprenantRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $apprenantManager;

    public function __construct(ApprenantRepository $apprenantRepository, EntityManagerInterface $entityManager)
    {
        $this->apprenantRepository = $apprenantRepository;
        $this->entityManager = $entityManager;
       
    }


    /**
     * @Route("/apprenant", name="list_apprenant")
     */
    public function Apprenant(): Response
    {
        $user = $this->getUser();
        $apprenants = $this->apprenantRepository->getApprenantByCaissiers($user->getStructure()->getId());
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if(!$apprenants){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('apprenant/apprenant.html.twig', [
            'apprenants' => $apprenants,'resultat' => $resultat,'classe' => $classe
        ]);
    }

     /**
     * @Route("/apprenant/new", name="create_apprenant")
     */
    public function createApprenant(Request $request): Response
    {
        $users = $this->getUser();
        $apprenant = new Apprenant();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(ApprenantType::class, $apprenant);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $dateEncours = new \DateTime();// date actuelle
            $dateNaissance = new \DateTime($data['DateNaissance']);
            $annee = $dateEncours->diff($dateNaissance, true)->y;
            //return new Response($annee);
            if($annee<18){
                $resultat = "La date de naissance renseignée n'est pas autorisée !!!";
                $classe = "alert alert-danger";
                return $this->render('apprenant/apprenant-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
            $apprenant->setStatut(1);
            $apprenant->setUser($users);
            $apprenant->upload();
            $this->entityManager->persist($apprenant);
            $this->entityManager->flush();
            if ($apprenant) {
                return $this->redirectToRoute('list_apprenant');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('apprenant/apprenant-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('apprenant/apprenant-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

      /**
     * @Route("/apprenant/edit/{id}", name="edit_apprenant")
     */
    public function editApprenant(Request $request, Apprenant $apprenant): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(ApprenantType::class,$apprenant);
       
        if($role !== Constantes::ROLE_CAISSIER_SUPERIEUR && $role !== Constantes::ROLE_CAISSIER_SECONDAIRE && $role !== Constantes::ROLE_CAISSIER_PRIMAIRE){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('apprenant/apprenant-form.html.twig', [
                'action' => 'edit',
                'apprenant' => $apprenant,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $apprenant->setUser($users);
            $apprenant->upload();
            $this->entityManager->persist($apprenant);
            $this->entityManager->flush();
            if ($apprenant) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_CAISSIER_SUPERIEUR || $primaryRole === Constantes::ROLE_CAISSIER_SECONDAIRE || $primaryRole === Constantes::ROLE_CAISSIER_PRIMAIRE ){
                    return $this->redirectToRoute('list_apprenant');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('apprenant/apprenant-form.html.twig', [
                    'action' => 'edit',
                    'apprenant' => $apprenant,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('apprenant/apprenant-form.html.twig', [
            'action' => 'edit',
            'apprenant' => $apprenant,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

        /**
     * @Route("/desactiverapprenant/{id}", name="desabled_apprenant")
     */

    public function desactiverApprenant(Request $request, apprenant $apprenant_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_CAISSIER_SUPERIEUR && $role !== Constantes::ROLE_CAISSIER_SECONDAIRE && $role !== Constantes::ROLE_CAISSIER_PRIMAIRE){

            
                return $this->render('apprenant/apprenant.html.twig', [
                    'apprenant' => null,
                ]);
            }
            $apprenant_entity->setStatut(0);
            $entityManager->persist($apprenant_entity);
            $entityManager->flush();
            $apprenants = $this->apprenantRepository->getApprenants();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_apprenant');
            } else {
                return $this->render('apprenant/apprenant.html.twig', [
                    'apprenants' => $apprenants,
                ]);
            }
        
            return $this->render('apprenant/apprenant.html.twig', [
                'apprenants' => $apprenants,
            ]);
    }

  /**
      * @Route("/activerapprenant/{id}", name="enable_apprenant")
      */

      public function activerApprenant(Request $request, apprenant $apprenant_entity): Response
    
      {
          $user = $this->getUser();
          $roles = $user->getRoles();
          $role = $roles[0];    
          $entityManager = $this->getDoctrine()->getManager();
          if($role !== Constantes::ROLE_CAISSIER_SUPERIEUR && $role !== Constantes::ROLE_CAISSIER_SECONDAIRE && $role !== Constantes::ROLE_CAISSIER_PRIMAIRE){

              
                  return $this->render('apprenant/apprenant.html.twig', [
                      'apprenant' => null,
                  ]);
              }
              $apprenant_entity->setStatut(1);
              $entityManager->persist($apprenant_entity);
              $entityManager->flush();
              $apprenants = $this->apprenantRepository->getApprenants();
              
              if ($entityManager) {
                  return $this->redirectToRoute('list_apprenant');
              } else {
                  return $this->render('apprenant/apprenant.html.twig', [
                      'apprenants' => $apprenants,
                  ]);
              }
          
              return $this->render('apprenant/apprenant.html.twig', [
                  'apprenants' => $apprenants,
              ]);
      }
    /**
     * Botton detail sur apprenant
     * @Route("/apprenant/{id}", name="detail_apprenant")
     */
    public function detailApprenant(Apprenant $id): Response
    {
        $user = $this->getUser();
        $apprenants = $this->apprenantRepository->findOneById($id);
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if(!$apprenants){
            $resultat = "Echec d'operation !.";
                $classe = "alert alert-danger";
        }
        return $this->render('apprenant/apprenant-detail.html.twig', [
            'apprenants' => $apprenants,'resultat' => $resultat,'classe' => $classe
        ]);
    }
    //Cette methode me permet de compter le nombre des apprenants 

    public function nombreApprenant(): Response
    {
        $user = $this->getUser();
        $response = 0;
        $roles = $user->getRoles();
        $role = $roles[0];
        $apprenants = $this->apprenantRepository->getNombreApprenant($user->getStructure()->getId());
        if($apprenants){
            $nombre = intval($apprenants[0]['nombre']);
            $response = new Response($nombre);
            return $response;
        }
        return new Response($response);
    }
}
