<?php

namespace App\Controller;
use App\Constante\Constantes;
use App\Entity\Structure;
use App\Form\EditStructureType;
use App\Form\StructureType;
use App\Repository\StructureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class StructureController extends AbstractController
{
    private $structureRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $structureManager;

    public function __construct(StructureRepository $structureRepository, EntityManagerInterface $entityManager)
    {
        $this->structureRepository = $structureRepository;
        $this->entityManager = $entityManager;
       
    }


    /**
     * @Route("/structure", name="list_structure")
     */
    public function Structure(): Response
    {
        $user = $this->getUser();
        $structures = $this->structureRepository->getStructures();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if(!$structures){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('structure/structure.html.twig', [
            'structures' => $structures,'resultat' => $resultat,'classe' => $classe
        ]);
    }

     /**
     * @Route("/Structure/new", name="create_structure")
     */
    public function createStructure(Request $request): Response
    {
        $users = $this->getUser();
        $structure = new Structure();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(StructureType::class, $structure);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $structure->setStatut(1);
            $structure->upload();
            $this->entityManager->persist($structure);
            $this->entityManager->flush();
            if ($structure) {
                return $this->redirectToRoute('list_structure');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('structure/structure-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('structure/structure-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

      /**
     * @Route("/Structure/edit/{id}", name="edit_structure")
     */
    public function editStructure(Request $request, Structure $structure): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(StructureType::class,$structure);
       
        if($role !== Constantes::ROLE_SUPER_ADMIN){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('structure/structure-form.html.twig', [
                'action' => 'edit',
                'structure' => $structure,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($structure);
            $this->entityManager->flush();
            if ($structure) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_SUPER_ADMIN){
                    return $this->redirectToRoute('list_structure');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('structure/structure-form.html.twig', [
                    'action' => 'edit',
                    'structure' => $structure,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('structure/structure-form.html.twig', [
            'action' => 'edit',
            'structure' => $structure,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

        /**
     * @Route("/desactiverstructure/{id}", name="desabled_structure")
     */

    public function desactiverStructure(Request $request, structure $structure_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
            if($role !== Constantes::ROLE_SUPER_ADMIN){
            
                return $this->render('structure/structure.html.twig', [
                    'structure' => null,
                ]);
            }
            $structure_entity->setStatut(0);
            $entityManager->persist($structure_entity);
            $entityManager->flush();
            $structures = $this->structureRepository->getStructures();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_structure');
            } else {
                return $this->render('structure/structure.html.twig', [
                    'structures' => $structures,
                ]);
            }
        
            return $this->render('structure/structure.html.twig', [
                'structures' => $structures,
            ]);
    }

  /**
      * @Route("/activerstructure/{id}", name="enable_structure")
      */

      public function activerStructure(Request $request, structure $structure_entity): Response
    
      {
          $user = $this->getUser();
          $roles = $user->getRoles();
          $role = $roles[0];    
          $entityManager = $this->getDoctrine()->getManager();
              if($role !== Constantes::ROLE_SUPER_ADMIN){
              
                  return $this->render('structure/structure.html.twig', [
                      'structure' => null,
                  ]);
              }
              $structure_entity->setStatut(1);
              $entityManager->persist($structure_entity);
              $entityManager->flush();
              $structures = $this->structureRepository->getStructures();
              
              if ($entityManager) {
                  return $this->redirectToRoute('list_structure');
              } else {
                  return $this->render('structure/structure.html.twig', [
                      'structures' => $structures,
                  ]);
              }
          
              return $this->render('structure/structure.html.twig', [
                  'structures' => $structures,
              ]);
      }

}
