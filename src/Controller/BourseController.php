<?php

namespace App\Controller;
use App\Constante\Constantes;
use App\Entity\Bourse;
use App\Form\EditBourseType;
use App\Form\BourseType;
use App\Repository\BourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class BourseController extends AbstractController
{

    private $bourseRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $bourseManager;


    public function __construct(BourseRepository $bourseRepository, EntityManagerInterface $entityManager)
    {
        $this->bourseRepository = $bourseRepository;
        $this->entityManager = $entityManager;
       
    }


    /**
     * @Route("/bourse", name="list_bourse")
     */
    public function bourse(): Response
    {
        $user = $this->getUser();
        $bourses = $this->bourseRepository->getBourses();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];

        if(!$bourses){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('bourse/bourse.html.twig', [
            'bourses' => $bourses,'resultat' => $resultat,'classe' => $classe
        ]);
    }

    /**
     * @Route("/bourse/new", name="create_bourse")
     */
    public function createBourse(Request $request): Response
    {
        $users = $this->getUser();
        $bourse = new Bourse();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(BourseType::class, $bourse);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
          $bourse->setStatut(1);
            $this->entityManager->persist($bourse);
            $this->entityManager->flush();
            if ($bourse) {
                return $this->redirectToRoute('list_bourse');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('bourse/bourse-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('bourse/bourse-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }
    /**
     * @Route("/bourse/edit/{id}", name="edit_bourse")
     */
    public function editBourse(Request $request, Bourse $bourse): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(BourseType::class,$bourse);
       
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('bourse/bourse-form.html.twig', [
                'action' => 'edit',
                'bourse' => $bourse,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($bourse);
            $this->entityManager->flush();
            if ($bourse) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_ADMIN_SUPERIEUR || $primaryRole === Constantes::ROLE_ADMIN_SECONDAIRE || $primaryRole === Constantes::ROLE_ADMIN_PRIMAIRE ){
                    return $this->redirectToRoute('list_bourse');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('bourse/bourse-form.html.twig', [
                    'action' => 'edit',
                    'bourse' => $bourse,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('bourse/bourse-form.html.twig', [
            'action' => 'edit',
            'bourse' => $bourse,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

    /**
     * @Route("/desactiverbourse/{id}", name="desabled_bourse")
     */

    public function desactiverbourse(Request $request, bourse $bourse_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE){
            
                return $this->render('bourse/bourse.html.twig', [
                    'bourse' => null,
                ]);
            }
            $bourse_entity->setStatut(0);
            $entityManager->persist($bourse_entity);
            $entityManager->flush();
            $bourses = $this->bourseRepository->getBourses();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_bourse');
            } else {
                return $this->render('bourse/bourse.html.twig', [
                    'bourses' => $bourses,
                ]);
            }
        
            return $this->render('bourse/bourse.html.twig', [
                'bourses' => $bourses,
            ]);
    }

    /**
     * @Route("/activerbourse/{id}", name="enable_bourse")
     */

    public function activerbourse(Request $request, bourse $bourse_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE){
            
                return $this->render('bourse/bourse.html.twig', [
                    'bourse' => null,
                ]);
            }
            $bourse_entity->setStatut(1);
            $entityManager->persist($bourse_entity);
            $entityManager->flush();
            $bourses = $this->bourseRepository->getBourses();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_bourse');
            } else {
                return $this->render('bourse/bourse.html.twig', [
                    'bourses' => $bourses,
                ]);
            }
        
            return $this->render('bourse/bourse.html.twig', [
                'bourses' => $bourses,
            ]);
    }

}
