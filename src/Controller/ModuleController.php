<?php

namespace App\Controller;
use App\Constante\Constantes;
use App\Entity\Module;
use App\Form\EditModuleType;
use App\Form\ModuleType;
use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ModuleController extends AbstractController
{
    private $moduleRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $moduleManager;

    

    public function __construct(ModuleRepository $moduleRepository, EntityManagerInterface $entityManager)
    {
        $this->moduleRepository = $moduleRepository;
        $this->entityManager = $entityManager;
       
    }


     /**
     * @Route("/module", name="list_module")
     */

    public function module(): Response
    {
        $user = $this->getUser();
        $modules = $this->moduleRepository->getModules();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
/*        if($role !== Constantes:: ROLE_DIRECTEUR ){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('module/module.html.twig', [
                'module' => null,'resultat' => $resultat,'classe' => $classe
            ]);
        }*/
        if(!$modules){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('module/module.html.twig', [
            'modules' => $modules,'resultat' => $resultat,'classe' => $classe
        ]);
    }
     /**
     * @Route("/module/new", name="create_module")
     */
    public function createmodule(Request $request): Response
    {
        $users = $this->getUser();
        $module = new Module();
        $module->setStatut(1);
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(ModuleType::class, $module);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $module->setStatut(1);
            $this->entityManager->persist($module);
            $this->entityManager->flush();
            if ($module) {
                return $this->redirectToRoute('list_module');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('module/module-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('module/module-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }
      /**
     * @Route("/module/edit/{id}", name="edit_module")
     */
    public function editModule(Request $request, Module $module): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(EditModuleType::class,$module);
       
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('module/module-form-edit.html.twig', [
                'action' => 'edit',
                'module' => $module,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($module);
            $this->entityManager->flush();
            if ($module) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_ADMIN_SUPERIEUR){
                    return $this->redirectToRoute('list_module');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('module/module-form.html.twig', [
                    'action' => 'edit',
                    'module' => $module,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('module/module-form.html.twig', [
            'action' => 'edit',
            'module' => $module,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }



    /**
     * @Route("/desactivermodule/{id}", name="desabled_module")
     */

    public function desactiverModule(Request $request, module $module_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR){            
                return $this->render('module/module.html.twig', [
                    'module' => null,
                ]);
            }
            $module_entity->setStatut(0);
            $entityManager->persist($module_entity);
            $entityManager->flush();
            $modules = $this->moduleRepository->getModules();            
            if ($entityManager) {
                return $this->redirectToRoute('list_module');
            } else {
                return $this->render('module/module.html.twig', [
                    'modules' => $modules,
                ]);
            }
        
            return $this->render('module/module.html.twig', [
                'modules' => $modules,
            ]);
    }
 /**
     * @Route("/activermodule/{id}", name="enabled_module")
     */

    public function activerModule(Request $request, module $module_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_ADMIN_SUPERIEUR){            
                return $this->render('module/module.html.twig', [
                    'module' => null,
                ]);
            }
            $module_entity->setStatut(1);
            $entityManager->persist($module_entity);
            $entityManager->flush();
            $modules = $this->moduleRepository->getModules();            
            if ($entityManager) {
                return $this->redirectToRoute('list_module');
            } else {
                return $this->render('module/module.html.twig', [
                    'modules' => $modules,
                ]);
            }
        
            return $this->render('module/module.html.twig', [
                'modules' => $modules,
            ]);
    }

}
