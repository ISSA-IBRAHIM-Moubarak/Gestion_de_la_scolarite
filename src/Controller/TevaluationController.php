<?php

namespace App\Controller;
use App\Constante\Constantes;
use App\Entity\Tevaluation;
use App\Form\EditTevaluationType;
use App\Form\TevaluationType;
use App\Repository\TevaluationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class TevaluationController extends AbstractController
{

    private $tevaluationRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $tevaluationManager;


    public function __construct(TevaluationRepository $tevaluationRepository, EntityManagerInterface $entityManager)
    {
        $this->tevaluationRepository = $tevaluationRepository;
        $this->entityManager = $entityManager;
       
    }


     /**
     * @Route("/tevaluation", name="list_tevaluation")
     */

    public function tevaluation(): Response
    {
        $user = $this->getUser();
        $tevaluations = $this->tevaluationRepository->getTevaluations();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];

        if(!$tevaluations){
            $resultat = "";
            $classe = "";                
        }
        return $this->render('tevaluation/index.html.twig', [
            'tevaluations' => $tevaluations,'resultat' => $resultat,'classe' => $classe
        ]);
    }
     /**
     * @Route("/tevaluation/new", name="create_tevaluation")
     */
    public function createTevaluation(Request $request): Response
    {
        $users = $this->getUser();
        $tevaluation = new Tevaluation();
        $resultat = "";
        $classe = "";
        
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($role === Constantes::ROLE_CENSEUR_SUPERIEUR) {
            $form = $this->createForm(TevaluationType::class,$tevaluation);
        }else {
            $form = $this->createForm(EditTevaluationType::class,$tevaluation);
        }

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $tevaluation->setStatut(1);
            
            $this->entityManager->persist($tevaluation);
            $this->entityManager->flush();
            if ($tevaluation) {
                return $this->redirectToRoute('list_tevaluation');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('tevaluation/tevaluation-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('tevaluation/tevaluation-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }
    /**
     * @Route("/tevaluation/edit/{id}", name="edit_tevaluation")
    */
    public function editTevaluation(Request $request, Tevaluation $tevaluation): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];

        
        if ($role === Constantes::ROLE_CENSEUR_SUPERIEUR) {
            $form = $this->createForm(TevaluationType::class,$tevaluation);
        //return new Response($role);

        }else {
        //return new Response($role);
            $form = $this->createForm(EditTevaluationType::class,$tevaluation);
        }
       
        if($role !== Constantes::ROLE_CENSEUR_SUPERIEUR && $role !== Constantes::ROLE_CENSEUR_SECONDAIRE && $role !== Constantes::ROLE_CENSEUR_PRIMAIRE ){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('tevaluation/tevaluation-form.html.twig', [
                'action' => 'edit',
                'tevaluation' => $tevaluation,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $tevaluation->upload();
            $this->entityManager->persist($tevaluation);
            $this->entityManager->flush();
            if ($tevaluation) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_CENSEUR_SUPERIEUR || $primaryRole === Constantes::ROLE_CENSEUR_SECONDAIRE || $primaryRole === Constantes::ROLE_CENSEUR_PRIMAIRE){
                    return $this->redirectToRoute('list_tevaluation');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('tevaluation/tevaluation-form.html.twig', [
                    'action' => 'edit',
                    'tevaluation' => $tevaluation,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('tevaluation/tevaluation-form.html.twig', [
            'action' => 'edit',
            'tevaluation' => $tevaluation,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }



    /**
     * @Route("/desactivertevaluation/{id}", name="desabled_tevaluation")
     */

    public function desactiverTevaluation(Request $request, tevaluation $tevaluation_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_CENSEUR_SUPERIEUR && $role !== Constantes::ROLE_CENSEUR_SECONDAIRE && $role !== Constantes::ROLE_CENSEUR_PRIMAIRE ){            
                return $this->render('tevaluation/tevaluation.html.twig', [
                    'tevaluation' => null,
                ]);
            }
            $evaluation_entity->setStatut(0);
            $entityManager->persist($evaluation_entity);
            $entityManager->flush();
            if ($entityManager) {
                return $this->redirectToRoute('list_tevaluation');
            } else {
                return $this->render('tevaluation/index.html.twig', [
                    'tevaluations' => $tevaluation_entity,
                ]);
            }
        
            return $this->render('tevaluation/index.html.twig', [
                'tevaluations' => $tevaluation_entity,
            ]);
    }
  /**
     * @Route("/activertevaluation/{id}", name="enable_tevaluation")
     */

    public function activerTevaluation(Request $request, Tevaluation $tevaluation_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_CENSEUR_SUPERIEUR && $role !== Constantes::ROLE_CENSEUR_SECONDAIRE && $role !== Constantes::ROLE_CENSEUR_PRIMAIRE ){
                return $this->render('tevaluation/index.html.twig', [
                    'tevaluation' => null,
                ]);
            }
            $tevaluation_entity->setStatut(1);
            $tevaluation_entity->upload();
            $entityManager->persist($tevaluation_entity);
            $entityManager->flush();
           
            if ($entityManager) {
                return $this->redirectToRoute('list_tevaluation');
            } else {
                return $this->render('tevaluation/index.html.twig', [
                    'tevaluations' => $tevaluation_entity,
                ]);
            }
        
            return $this->render('tevaluation/index.html.twig', [
                'tevaluations' => $tevaluation_entity,
            ]);
    } 
}

