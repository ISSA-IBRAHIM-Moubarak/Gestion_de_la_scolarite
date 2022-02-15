<?php

namespace App\Controller;
use App\Constante\Constantes;
use App\Entity\Evaluation;
use App\Form\EditEvaluationType;
use App\Form\EvaluationType;
use App\Repository\EvaluationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class EvaluationController extends AbstractController
{

    private $evaluationRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $evaluationManager;


    public function __construct(EvaluationRepository $evaluationRepository, EntityManagerInterface $entityManager)
    {
        $this->evaluationRepository = $evaluationRepository;
        $this->entityManager = $entityManager;
       
    }


     /**
     * @Route("/evaluation", name="list_evaluation")
     */

    public function evaluation(): Response
    {
        $user = $this->getUser();
        $evaluations = $this->evaluationRepository->getEvaluations();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];

        if(!$evaluations){
            $resultat = "";
            $classe = "";                
        }
        return $this->render('evaluation/evaluation.html.twig', [
            'evaluations' => $evaluations,'resultat' => $resultat,'classe' => $classe
        ]);
    }
     /**
     * @Route("/evaluation/new", name="create_evaluation")
     */
    public function createEvaluation(Request $request): Response
    {
        $users = $this->getUser();
        $evaluation = new Evaluation();
        $resultat = "";
        $classe = "";
        
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($role === Constantes::ROLE_CENSEUR_SUPERIEUR) {
            $form = $this->createForm(EvaluationType::class,$evaluation);
        }else {
            $form = $this->createForm(EditEvaluationType::class,$evaluation);
        }

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $evaluation->setStatut(1);
            $evaluation->upload();
            $this->entityManager->persist($evaluation);
            $this->entityManager->flush();
            if ($evaluation) {
                return $this->redirectToRoute('list_evaluation');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('evaluation/evaluation-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('evaluation/evaluation-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }
    /**
     * @Route("/evaluation/edit/{id}", name="edit_evaluation")
    */
    public function editEvaluation(Request $request, Evaluation $evaluation): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];

        
        if ($role === Constantes::ROLE_CENSEUR_SUPERIEUR) {
            $form = $this->createForm(EvaluationType::class,$evaluation);
        //return new Response($role);

        }else {
        //return new Response($role);
            $form = $this->createForm(EditEvaluationType::class,$evaluation);
        }
       
        if($role !== Constantes::ROLE_CENSEUR_SUPERIEUR && $role !== Constantes::ROLE_CENSEUR_SECONDAIRE && $role !== Constantes::ROLE_CENSEUR_PRIMAIRE ){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('evaluation/evaluation-form.html.twig', [
                'action' => 'edit',
                'evaluation' => $evaluation,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $evaluation->upload();
            $this->entityManager->persist($evaluation);
            $this->entityManager->flush();
            if ($evaluation) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_CENSEUR_SUPERIEUR || $primaryRole === Constantes::ROLE_CENSEUR_SECONDAIRE || $primaryRole === Constantes::ROLE_CENSEUR_PRIMAIRE){
                    return $this->redirectToRoute('list_evaluation');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('evaluation/evaluation-form.html.twig', [
                    'action' => 'edit',
                    'evaluation' => $evaluation,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('evaluation/evaluation-form.html.twig', [
            'action' => 'edit',
            'evaluation' => $evaluation,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }



    /**
     * @Route("/desactiverevaluation/{id}", name="desabled_evaluation")
     */

    public function desactiverEvaluation(Request $request, evaluation $evaluation_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_CENSEUR_SUPERIEUR && $role !== Constantes::ROLE_CENSEUR_SECONDAIRE && $role !== Constantes::ROLE_CENSEUR_PRIMAIRE ){            
                return $this->render('evaluation/evaluation.html.twig', [
                    'evaluation' => null,
                ]);
            }
            $evaluation_entity->setStatut(0);
            $entityManager->persist($evaluation_entity);
            $entityManager->flush();
            if ($entityManager) {
                return $this->redirectToRoute('list_evaluation');
            } else {
                return $this->render('evaluation/evaluation.html.twig', [
                    'evaluations' => $evaluation_entity,
                ]);
            }
        
            return $this->render('evaluation/evaluation.html.twig', [
                'evaluations' => $evaluation_entity,
            ]);
    }
  /**
     * @Route("/activerevaluation/{id}", name="enable_evaluation")
     */

    public function activerEvaluation(Request $request, evaluation $evaluation_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_CENSEUR_SUPERIEUR && $role !== Constantes::ROLE_CENSEUR_SECONDAIRE && $role !== Constantes::ROLE_CENSEUR_PRIMAIRE ){
                return $this->render('evaluation/evaluation.html.twig', [
                    'evaluation' => null,
                ]);
            }
            $evaluation_entity->setStatut(1);
            $evaluation_entity->upload();
            $entityManager->persist($evaluation_entity);
            $entityManager->flush();
           
            if ($entityManager) {
                return $this->redirectToRoute('list_evaluation');
            } else {
                return $this->render('evaluation/evaluation.html.twig', [
                    'evaluations' => $evaluation_entity,
                ]);
            }
        
            return $this->render('evaluation/evaluation.html.twig', [
                'evaluations' => $evaluation_entity,
            ]);
    } 
}

