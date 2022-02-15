<?php

namespace App\Controller;

use App\Constante\Constantes;

use App\Repository\ModuleRepository;
use App\Repository\MatiereRepository;
use App\Repository\SemestreRepository;

use App\Entity\Module;
use App\Entity\Matiere;
use App\Entity\Semestre;

use App\Entity\ModuleSemestre;
use App\Entity\MatiereSemestre;

use App\Form\EditModuleSemestreType;

use App\Form\ModuleSemestreType;
use App\Form\MatiereSemestreType;

use App\Repository\ModuleSemestreRepository;
use App\Repository\MatiereSemestreRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class ModuleSemestreController extends AbstractController
{

    private $moduleSemestreRepository;
    private $matiereSemestreRepository;
    private $moduleRepository;
    private $matiereRepository;
    private $semestreRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $moduleSemestreManager;
    private $matiereSemestreManager;




    public function __construct(ModuleSemestreRepository $moduleSemestreRepository, MatiereSemestreRepository $matiereSemestreRepository, EntityManagerInterface $entityManager, ModuleRepository $moduleRepository, MatiereRepository $matiereRepository, SemestreRepository $semestreRepository)
    {
        $this->moduleSemestreRepository = $moduleSemestreRepository;
        $this->matiereSemestreRepository = $matiereSemestreRepository;
        $this->semestreRepository = $semestreRepository;
        $this->moduleRepository = $moduleRepository;
        $this->matiereRepository = $matiereRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/module_semestre", name="list_module_semestre")
     */
    public function moduleSemestre(): Response
    {
        $user = $this->getUser();
        $moduleSemestres = $this->moduleSemestreRepository->getModuleSemestres();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if (!$moduleSemestres) {
            $resultat = "";
            $classe = "";
        }
        return $this->render('module_semestre/modulesemestre.html.twig', [
            'moduleSemestres' => $moduleSemestres, 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * @Route("/matiere_semestre", name="list_matiere_semestre")
     */
    public function matiereSemestre(): Response
    {
        $user = $this->getUser();
        $matiereSemestres = $this->matiereSemestreRepository->getMatiereSemestres();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if (!$matiereSemestres) {
            $resultat = "";
            $classe = "";
        }
        return $this->render('module_semestre/matieresemestre.html.twig', [
            'matiereSemestres' => $matiereSemestres, 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * @Route("/module_semestre/new/{id}", name="create_moduleSemestre")
     */
    public function createmoduleSemestre(EntityManagerInterface $em, Request $request)
    {
        $users = $this->getUser();
        $moduleSemestre = new ModuleSemestre();
        $semestre = $this->semestreRepository->findOneById($request->get('id'));

        $resultat = "";
        $classe = "";

        $form = $this->createForm(ModuleSemestreType::class, $moduleSemestre);
        $roles = $users->getRoles();
        $role = $roles[0];

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $data = $request->request->get($form->getName());
            $module = $data['module'];

            foreach ($module as $value) {
                $module_entity = $this->moduleRepository->findOneById($value);
                $moduleSemestre = new ModuleSemestre();
                $moduleSemestre->setStatut(1);
                $moduleSemestre->setSemestre($semestre);
                $moduleSemestre->setModule($module_entity);
                $em->persist($moduleSemestre);
                $em->flush();
            }

            if ($moduleSemestre) {
                return $this->redirectToRoute('list_module_semestre');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('module_semestre/moduleSemestre-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
                ]);
            }
        }
        return $this->render('module_semestre/moduleSemestre-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * @Route("/matiere_semestre/new/{id}", name="create_matiereSemestre")
     */
    public function creatematiereSemestre(EntityManagerInterface $em, Request $request)
    {
        $users = $this->getUser();
        $matiereSemestre = new MatiereSemestre();
        $semestre = $this->semestreRepository->findOneById($request->get('id'));

        $resultat = "";
        $classe = "";

        $form = $this->createForm(MatiereSemestreType::class, $matiereSemestre);
        $roles = $users->getRoles();
        $role = $roles[0];

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $data = $request->request->get($form->getName());

            $matiere = $data['matiere'];

            foreach ($matiere as $value) {
                $matiereSemestre = new MatiereSemestre();
                //Sreturn new response($value);

                $matiere_entity = $this->matiereRepository->findOneById($value);

                $matiereSemestre->setStatut(1);
                $matiereSemestre->setSemestre($semestre);
                $matiereSemestre->setMatiere($matiere_entity);
                $em->persist($matiereSemestre);
                $em->flush();
            }

            if ($matiereSemestre) {
                return $this->redirectToRoute('list_matiere_semestre');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('module_semestre/matiereSemestre-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
                ]);
            }
        }
        return $this->render('module_semestre/matiereSemestre-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
        ]);
    }




    /**
     * @Route("/{id}/semestre/detailmodule", name="detail_semestre")
     */
    public function detailsemestre(Semestre $semestre): Response
    {
        $user = $this->getUser();
        $moduleSemestres = $this->moduleSemestreRepository->ModuleSemestre($semestre);
        $resultat = "";
        $classe = "";

        if (!$moduleSemestres) {
            $resultat = "";
            $classe = "";
        }
        return $this->render('module_semestre/detail.html.twig', [
            'moduleSemestres' => $moduleSemestres, 'resultat' => $resultat, 'classe' => $classe, 'semestre' => $semestre->getLibelleSemestre()
        ]);
    }


    /**
     * @Route("/module_semestre/edit/{id}", name="edit_moduleSemestre")
     */
    public function editModuleSemestre(Request $request, ModuleSemestre $moduleSemestre): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];

        $form = $this->createForm(EditModuleSemestreType::class, $moduleSemestre);

        if ($role !== Constantes::ROLE_DIRECTEUR_SUPERIEUR) {
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('module_semestre/moduleSemestre-form-edit.html.twig', [
                'action' => 'edit',
                'moduleSemestre' => $moduleSemestre,
                'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($moduleSemestre);
            $this->entityManager->flush();
            if ($moduleSemestre) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if ($primaryRole === Constantes::ROLE_DIRECTEUR_SUPERIEUR) {
                    return $this->redirectToRoute('list_module_semestre');
                }
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";

                return $this->render('module_semestre/moduleSemestre-form-edit.html.twig', [
                    'action' => 'edit',
                    'moduleSemestre' => $moduleSemestre,
                    'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
                ]);
            }
        }
        return $this->render('module_semestre/moduleSemestre-form-edit.html.twig', [
            'action' => 'edit',
            'moduleSemestre' => $moduleSemestre,
            'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    
    /**
     * @Route("/module_semestre/edit/{id}", name="edit_matiereSemestre")
     */
    public function editMatiereSemestre(Request $request, MatiereSemestre $matiereSemestre): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];

        $form = $this->createForm(EditMatiereSemestreType::class, $matiereSemestre);

        if ($role !== Constantes::ROLE_DIRECTEUR_SUPERIEUR) {
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('module_semestre/moduleSemestre-form-edit.html.twig', [
                'action' => 'edit',
                'matiereSemestre' => $matiereSemestre,
                'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($matiereSemestre);
            $this->entityManager->flush();
            if ($matiereSemestre) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if ($primaryRole === Constantes::ROLE_DIRECTEUR_SUPERIEUR) {
                    return $this->redirectToRoute('list_module_semestre');
                }
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";

                return $this->render('module_semestre/moduleSemestre-form-edit.html.twig', [
                    'action' => 'edit',
                    'matiereSemestre' => $matiereSemestre,
                    'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
                ]);
            }
        }
        return $this->render('module_semestre/moduleSemestre-form-edit.html.twig', [
            'action' => 'edit',
            'matiereSemestre' => $matiereSemestre,
            'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
        ]);
    }



    /**
     * @Route("/desactivermodule/{id}", name="desabled_moduleSemestre")
     */

    public function desactiverModuleSemestre(Request $request, moduleSemestre $moduleSemestre_entity): Response

    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
        $entityManager = $this->getDoctrine()->getManager();
        if ($role !== Constantes::ROLE_DIRECTEUR_SUPERIEUR) {
            return $this->render('module_semestre/index.html.twig', [
                'moduleSemestre' => null,
            ]);
        }
        $moduleSemestre_entity->setStatut(0);
        $entityManager->persist($moduleSemestre_entity);
        $entityManager->flush();
        $moduleSemestres = $this->moduleSemestreRepository->getModuleSemestres();
        if ($entityManager) {
            return $this->redirectToRoute('list_module_semestre');
        } else {
            return $this->render('module_semestre/index.html.twig', [
                'moduleSemestres' => $moduleSemestres,
            ]);
        }

        return $this->render('module_semestre/index.html.twig', [
            'modules' => $moduleSemestres,
        ]);
    }

      /**
     * @Route("/desactivermatiere/{id}", name="desabled_matiereSemestre")
     */

    public function desactiverMatiereSemestre(Request $request, matiereSemestre $matiereSemestre_entity): Response

    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
        $entityManager = $this->getDoctrine()->getManager();
        if ($role !== Constantes::ROLE_DIRECTEUR_SUPERIEUR) {
            return $this->render('module_semestre/index.html.twig', [
                'matiereSemestre' => null,
            ]);
        }
        $matiereSemestre_entity->setStatut(0);
        $entityManager->persist($matiereSemestre_entity);
        $entityManager->flush();
        $matiereSemestres = $this->matiereSemestreRepository->getMatiereSemestres();
        if ($entityManager) {
            return $this->redirectToRoute('list_matiere_semestre');
        } else {
            return $this->render('module_semestre/index.html.twig', [
                'matiereSemestres' => $matiereSemestres,
            ]);
        }

        return $this->render('module_semestre/index.html.twig', [
            'matieres' => $matiereSemestres,
        ]);
    }

    /**
     * @Route("/activermoduleSemestre/{id}", name="enabled_moduleSemestre")
     */

    public function activerModuleSemestre(Request $request, moduleSemestre $moduleSemestre_entity): Response

    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
        $entityManager = $this->getDoctrine()->getManager();
        if ($role !== Constantes::ROLE_DIRECTEUR_SUPERIEUR) {
            return $this->render('module_semestre/index.html.twig', [
                'moduleSemestre' => null,
            ]);
        }
        $moduleSemestre_entity->setStatut(1);
        $entityManager->persist($moduleSemestre_entity);
        $entityManager->flush();
        $moduleSemestres = $this->moduleSemestreRepository->getModuleSemestres();
        if ($entityManager) {
            return $this->redirectToRoute('list_module_semestre');
        } else {
            return $this->render('module_semestre/index.html.twig', [
                'moduleSemestres' => $moduleSemestres,
            ]);
        }

        return $this->render('module_semestre/index.html.twig', [
            'modules' => $moduleSemestres,
        ]);
    }

     /**
     * @Route("/activermatiere/{id}", name="enabled_matiereSemestre")
     */

    public function activerMatiereSemestre(Request $request, matiereSemestre $matiereSemestre_entity): Response

    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
        $entityManager = $this->getDoctrine()->getManager();
        if ($role !== Constantes::ROLE_DIRECTEUR_SUPERIEUR) {
            return $this->render('module_semestre/index.html.twig', [
                'matiereSemestre' => null,
            ]);
        }
        $matiereSemestre_entity->setStatut(1);
        $entityManager->persist($matiereSemestre_entity);
        $entityManager->flush();
        $matiereSemestres = $this->matiereSemestreRepository->getMatiereSemestres();
        if ($entityManager) {
            return $this->redirectToRoute('list_matiere_semestre');
        } else {
            return $this->render('module_semestre/index.html.twig', [
                'matiereSemestres' => $matiereSemestres,
            ]);
        }

        return $this->render('module_semestre/index.html.twig', [
            'matieres' => $matiereSemestres,
        ]);
    }
}
