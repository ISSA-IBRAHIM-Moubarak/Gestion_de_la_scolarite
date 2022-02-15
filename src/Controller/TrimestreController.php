<?php

namespace App\Controller;
use App\Constante\Constantes;
use App\Entity\Trimestre;
use App\Form\EditTrimestreType;
use App\Form\TrimestreType;
use App\Repository\TrimestreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class TrimestreController extends AbstractController
{
    private $trimestreRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $trimestreManager;

    public function __construct(TrimestreRepository $trimestreRepository, EntityManagerInterface $entityManager)
    {
        $this->trimestreRepository = $trimestreRepository;
        $this->entityManager = $entityManager;
       
    }


    /**
     * @Route("/trimestre", name="list_trimestre")
     */
    public function Trimestre(): Response
    {
        $user = $this->getUser();
        $trimestres = $this->trimestreRepository->getTrimestres();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if(!$trimestres){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('trimestre/trimestre.html.twig', [
            'trimestres' => $trimestres,'resultat' => $resultat,'classe' => $classe
        ]);
    }

     /**
     * @Route("/trimestre/new", name="create_trimestre")
     */
    public function createTrimestre(Request $request): Response
    {
        $users = $this->getUser();
        $trimestre = new Trimestre();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(TrimestreType::class, $trimestre);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
          $trimestre->setStatut(1);
            $this->entityManager->persist($trimestre);
            $this->entityManager->flush();
            if ($trimestre) {
                return $this->redirectToRoute('list_trimestre');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('trimestre/trimestre-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('trimestre/trimestre-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

      /**
     * @Route("/trimestre/edit/{id}", name="edit_trimestre")
     */
    public function editTrimestre(Request $request, Trimestre $trimestre): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(EditTrimestreType::class,$trimestre);
       
        if($role !== Constantes::ROLE_ADMIN_PRIMAIRE){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('trimestre/trimestre-form.html.twig', [
                'action' => 'edit',
                'trimestre' => $trimestre,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($trimestre);
            $this->entityManager->flush();
            if ($trimestre) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_ADMIN_PRIMAIRE){
                    return $this->redirectToRoute('list_trimestre');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('trimestre/trimestre-form-edit.html.twig', [
                    'action' => 'edit',
                    'trimestre' => $trimestre,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('trimestre/trimestre-form.html.twig', [
            'action' => 'edit',
            'trimestre' => $trimestre,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

        /**
     * @Route("/desactivertrimestre/{id}", name="desabled_trimestre")
     */

    public function desactiverTrimestre(Request $request, trimestre $trimestre_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_PRIMAIRE){
                return $this->render('trimestre/trimestre.html.twig', [
                    'trimestre' => null,
                ]);
            }
            $trimestre_entity->setStatut(0);
            $entityManager->persist($trimestre_entity);
            $entityManager->flush();
            $trimestres = $this->trimestreRepository->getTrimestres();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_trimestre');
            } else {
                return $this->render('trimestre/trimestre.html.twig', [
                    'trimestres' => $trimestres,
                ]);
            }
        
            return $this->render('trimestre/trimestre.html.twig', [
                'trimestres' => $trimestres,
            ]);
    }

  /**
      * @Route("/activertrimestre/{id}", name="enable_trimestre")
      */

    public function activerTrimestre(Request $request, trimestre $trimestre_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_PRIMAIRE){            
                return $this->render('trimestre/trimestre.html.twig', [
                    'trimestre' => null,
                ]);
            }
            $trimestre_entity->setStatut(1);
            $entityManager->persist($trimestre_entity);
            $entityManager->flush();
            $trimestres = $this->trimestreRepository->getTrimestres();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_trimestre');
            } else {
                return $this->render('trimestre/trimestre.html.twig', [
                    'trimestres' => $trimestres,
                ]);
            }
        
            return $this->render('trimestre/trimestre.html.twig', [
                'trimestres' => $trimestres,
            ]);
    }

}
