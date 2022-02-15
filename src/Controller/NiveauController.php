<?php

namespace App\Controller;

use App\Entity\Niveau;
use App\Form\NiveauType;
use App\Form\EditNiveauType;
use App\Constante\Constantes;
use App\Form\RapportNiveauType;
use App\Repository\NiveauRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NiveauController extends AbstractController
{


    private $niveauRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $niveauManager;

    public function __construct(NiveauRepository $niveauRepository, EntityManagerInterface $entityManager)
    {
        $this->niveauRepository = $niveauRepository;
        $this->entityManager = $entityManager;
       
    }

    /**
     * @Route("/niveau", name="list_niveau")
     */
    public function niveau(): Response
    {
        $user = $this->getUser();
        $niveaus = $this->niveauRepository->getNiveaus();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if(!$niveaus){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('niveau/niveau.html.twig', [
            'controller_name' => 'NiveauController',
            'niveaus'=> $niveaus
        ]);
    }

    /**
     * @Route("/niveau/new", name="create_niveau")
     */
    public function createNiveau(Request $request): Response
    {
        $users = $this->getUser();
        $niveau = new Niveau();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(NiveauType::class, $niveau);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $montant = $data['montant'];
            $montantNiveau = intval($montant);
            # return new Response($montant);
            $niveau->setMontant($montantNiveau);
            $niveau->setStructure($users->getStructure());
            $niveau->setStatut(1);
            $this->entityManager->persist($niveau);
            $this->entityManager->flush();
            if ($niveau) {
                return $this->redirectToRoute('list_niveau');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('niveau/niveau-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('niveau/niveau-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }
     /**
     * @Route("/niveau/edit/{id}", name="edit_niveau")
     */
    public function editNiveau(Request $request, Niveau $niveau): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(NiveauType::class,$niveau);
       
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('niveau/niveau-form.html.twig', [
                'action' => 'edit',
                'niveau' => $niveau,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            # return new Response($data['montant']);
            $this->entityManager->persist($niveau);
            $this->entityManager->flush();
            if ($niveau) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_ADMIN_SUPERIEUR || $primaryRole === Constantes::ROLE_ADMIN_SECONDAIRE || $primaryRole === Constantes::ROLE_ADMIN_PRIMAIRE ){
                    return $this->redirectToRoute('list_niveau');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('niveau/niveau-form.html.twig', [
                    'action' => 'edit',
                    'niveau' => $niveau,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('niveau/niveau-form.html.twig', [
            'action' => 'edit',
            'niveau' => $niveau,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

    /**
     * @Route("/desactiverniveau/{id}", name="desabled_niveau")
     */

    public function desactiverNiveau(Request $request, niveau $niveau_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE){
                return $this->render('niveau/niveau.html.twig', [
                    'niveau' => null,
                ]);
            }
            $niveau_entity->setStatut(0);
            $entityManager->persist($niveau_entity);
            $entityManager->flush();
            $niveaus = $this->niveauRepository->getNiveaus();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_niveau');
            } else {
                return $this->render('niveau/niveau.html.twig', [
                    'niveaus' => $niveaus,
                ]);
            }
        
            return $this->render('niveau/niveau.html.twig', [
                'niveaus' => $niveaus,
            ]);
    }

    /**
     * @Route("/activerniveau/{id}", name="enabled_niveau")
     */
    public function activerNiveau(Request $request, niveau $niveau_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE){            
                return $this->render('niveau/niveau.html.twig', [
                    'niveau' => null,
                ]);
            }
            $niveau_entity->setStatut(1);
            $entityManager->persist($niveau_entity);
            $entityManager->flush();
            $niveaus = $this->niveauRepository->getNiveaus();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_niveau');
            } else {
                return $this->render('niveau/niveau.html.twig', [
                    'niveaus' => $niveaus,
                ]);
            }
        
            return $this->render('niveau/niveau.html.twig', [
                'niveaus' => $niveaus,
            ]);
    }

}
