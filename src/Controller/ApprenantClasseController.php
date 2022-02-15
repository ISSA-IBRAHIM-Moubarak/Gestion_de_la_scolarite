<?php

namespace App\Controller;
use App\Entity\Inscription;
use App\Constante\Constantes;
use App\Entity\ApprenantClasse;
use App\Form\ApprenantClasseType;
use App\Form\EditApprenantClasseType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ApprenantClasseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApprenantClasseController extends AbstractController
{

    private $apprenantClasseRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $apprenantClasseManager;


    public function __construct(ApprenantClasseRepository $apprenantClasseRepository, EntityManagerInterface $entityManager)
    {
        $this->apprenantClasseRepository = $apprenantClasseRepository;
        $this->entityManager = $entityManager;
       
    }


    /**
     * @Route("/Classe", name="list_apprenantClasse")
     */
    public function ApprenantClasse(): Response
    {
        $user = $this->getUser();
        $apprenantClasses = $this->apprenantClasseRepository->getApprenantClasses();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];

        if(!$apprenantClasses){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('apprenantClasse/apprenantClasse.html.twig', [
            'apprenantClasses' => $apprenantClasses,'resultat' => $resultat,'classe' => $classe
        ]);
    }

    /**
     * @Route("/apprenantClasse/{id}/new", name="create_apprenantClasse")
     */
    public function createApprenantClasse(Request $request, Inscription $inscription): Response
    {
        $users = $this->getUser();
        $apprenantClasse = new ApprenantClasse();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(ApprenantClasseType::class, $apprenantClasse);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $apprenantClasse->setStatut(1);
            $apprenantClasse->setInscription($inscription);
         // $inscription->setApprenant($apprenant);
            $this->entityManager->persist($apprenantClasse);
            $this->entityManager->flush();
            if ($apprenantClasse) {
                return $this->redirectToRoute('list_apprenantClasse');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('apprenantClasse/apprenantClasse-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('apprenantClasse/apprenantClasse-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }
    /**
     * @Route("/apprenantClasse/edit/{id}", name="edit_apprenantClasse")
     */
    public function editApprenantClasse(Request $request, ApprenantClasse $apprenantClasse): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(ApprenantClasseType::class,$apprenantClasse);
       
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('apprenantClasse/apprenantClasse-form.html.twig', [
                'action' => 'edit',
                'apprenantClasse' => $apprenantClasse,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($apprenantClasse);
            $this->entityManager->flush();
            if ($apprenantClasse) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_ADMIN_SUPERIEUR || $primaryRole === Constantes::ROLE_ADMIN_SECONDAIRE || $primaryRole === Constantes::ROLE_ADMIN_PRIMAIRE ){
                    return $this->redirectToRoute('list_apprenantClasse');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('apprenantClasse/apprenantClasse-form.html.twig', [
                    'action' => 'edit',
                    'apprenantClasse' => $apprenantClasse,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('apprenantClasse/apprenantClasse-form.html.twig', [
            'action' => 'edit',
            'apprenantClasse' => $apprenantClasse,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

    /**
     * @Route("/desactiverapprenantClasse/{id}", name="desabled_apprenantClasse")
     */

    public function desactiverApprenantClasse(Request $request, apprenantClasse $apprenantClasse_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE){
            
                return $this->render('apprenantClasse/apprenantClasse.html.twig', [
                    'apprenantClasse' => null,
                ]);
            }
            $apprenantClasse_entity->setStatut(0);
            $entityManager->persist($apprenantClasse_entity);
            $entityManager->flush();
            $apprenantClasses = $this->apprenantClasseRepository->getApprenantClasses();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_apprenantClasse');
            } else {
                return $this->render('apprenantClasse/apprenantClasse.html.twig', [
                    'apprenantClasses' => $apprenantClasses,
                ]);
            }
        
            return $this->render('apprenantClasse/apprenantClasse.html.twig', [
                'apprenantClasses' => $apprenantClasses,
            ]);
    }

    /**
     * @Route("/activerapprenantClasse/{id}", name="enable_apprenantClasse")
     */

    public function activerApprenantClasse(Request $request, apprenantClasse $apprenantClasse_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE){
            
                return $this->render('apprenantClasse/apprenantClasse.html.twig', [
                    'apprenantClasse' => null,
                ]);
            }
            $apprenantClasse_entity->setStatut(1);
            $entityManager->persist($apprenantClasse_entity);
            $entityManager->flush();
            $apprenantClasses = $this->apprenantClasseRepository->getApprenantClasses();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_apprenantClasse');
            } else {
                return $this->render('apprenantClasse/apprenantClasse.html.twig', [
                    'apprenantClasses' => $apprenantClasses,
                ]);
            }
        
            return $this->render('apprenantClasse/apprenantClasse.html.twig', [
                'apprenantClasses' => $apprenantClasses,
            ]);
    }


}
