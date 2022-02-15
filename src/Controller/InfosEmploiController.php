<?php

namespace App\Controller;

use App\Entity\Emploi;
use App\Entity\InfosEmploi;
use App\Constante\Constantes;
use App\Form\InfosEmploiType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InfosEmploiRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InfosEmploiController extends AbstractController
{
    private $infosEmploiRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $infosEmploiManager;

    public function __construct(InfosEmploiRepository $infosEmploiRepository, EntityManagerInterface $entityManager)
    {
        $this->infosEmploiRepository = $infosEmploiRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/InfosEmploi", name="list_infosEmploi")
     */
    public function InfosEmploi(): Response
    {
        $user = $this->getUser();
        $infosEmploi = $this->infosEmploiRepository->getInfosEmploi();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if (!$infosEmploi) {
            $resultat = "";
            $classe = "";
        }
        return $this->render('infosEmploi/infosEmploi.html.twig', [
            'infosEmploi' => $infosEmploi, 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * @Route("/Infos/{id}/new", name="create_infosEmploi")
     */
    public function createInfosEmploi(Request $request, Emploi $emploi): Response
    {
        $users = $this->getUser();
        $infosEmploi = new InfosEmploi();
        $resultat = "";
        $classe = "";

        $form = $this->createForm(InfosEmploiType::class, $infosEmploi);
        $roles = $users->getRoles();
        $role = $roles[0];

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $infosEmploi->setEmploi($emploi);
            $infosEmploi->setStatut(1);
            $this->entityManager->persist($infosEmploi);
            $this->entityManager->flush();
            if ($infosEmploi) {
                return $this->redirectToRoute('list_infosEmploi');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('infosEmploi/infosEmploi-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
                ]);
            }
        }
        return $this->render('infosEmploi/infosEmploi-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * @Route("/Infos/Emploi/edit/{id}", name="edit_infosEmploi")
     */
    public function editInfosEmploi(Request $request, InfosEmploi $infosEmploi): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];

        $form = $this->createForm(InfosEmploiType::class, $infosEmploi);

        if ($role !== Constantes::ROLE_CENSEUR_SUPERIEUR && $role !== Constantes::ROLE_CENSEUR_SECONDAIRE && $role !== Constantes::ROLE_CENSEUR_PRIMAIRE) {
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('infosEmploi/infosEmploi-form.html.twig', [
                'action' => 'edit',
                'infosEmploi' => $infosEmploi,
                'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($infosEmploi);
            $this->entityManager->flush();
            if ($infosEmploi) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if ($primaryRole === Constantes::ROLE_CENSEUR_SUPERIEUR || $primaryRole === Constantes::ROLE_CENSEUR_SECONDAIRE || $primaryRole === Constantes::ROLE_CENSEUR_PRIMAIRE) {
                    return $this->redirectToRoute('list_infosEmploi');
                }
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";

                return $this->render('infosEmploi/infosEmploi-form.html.twig', [
                    'action' => 'edit',
                    'infosEmploi' => $infosEmploi,
                    'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
                ]);
            }
        }
        return $this->render('infosEmploi/infosEmploi-form.html.twig', [
            'action' => 'edit',
            'infosEmploi' => $infosEmploi,
            'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * @Route("/Infos/desactiverEmploi/{id}", name="desabled_infosEmploi")
     */

    public function desactiverInfosEmploi(Request $request, infosEmploi $infosEmploi_entity): Response

    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
        $entityManager = $this->getDoctrine()->getManager();
        if ($role !== Constantes::ROLE_CENSEUR_SUPERIEUR && $role !== Constantes::ROLE_CENSEUR_SECONDAIRE && $role !== Constantes::ROLE_CENSEUR_PRIMAIRE) {
            return $this->render('infosEmploi/infosEmploi.html.twig', [
                'infosEmploi' => null,
            ]);
        }
        $infosEmploi_entity->setStatut(0);
        $entityManager->persist($infosEmploi_entity);
        $entityManager->flush();
        $infosEmploi = $this->infosEmploiRepository->getInfosEmploi();

        if ($entityManager) {
            return $this->redirectToRoute('list_infosEmploi');
        } else {
            return $this->render('infosEmploi/infosEmploi.html.twig', [
                'infosEmploi' => $infosEmploi,
            ]);
        }

        return $this->render('infosEmploi/infosEmploi.html.twig', [
            'infosEmploi' => $infosEmploi,
        ]);
    }

    /**
     * @Route("/Infos/activerEmploi/{id}", name="enable_infosEmploi")
     */

    public function activerInfosEmploi(Request $request, infosEmploi $infosEmploi_entity): Response

    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
        $entityManager = $this->getDoctrine()->getManager();
        if ($role !== Constantes::ROLE_CENSEUR_SUPERIEUR && $role !== Constantes::ROLE_CENSEUR_SECONDAIRE && $role !== Constantes::ROLE_CENSEUR_PRIMAIRE) {
            return $this->render('infosEmploi/infosEmploi.html.twig', [
                'infosEmploi' => null,
            ]);
        }
        $infosEmploi_entity->setStatut(1);
        $entityManager->persist($infosEmploi_entity);
        $entityManager->flush();
        $infosEmploi = $this->infosEmploiRepository->getInfosEmploi();

        if ($entityManager) {
            return $this->redirectToRoute('list_infosEmploi');
        } else {
            return $this->render('infosEmploi/infosEmploi.html.twig', [
                'infosEmploi' => $infosEmploi,
            ]);
        }

        return $this->render('infosEmploi/infosEmploi.html.twig', [
            'infosEmploi' => $infosEmploi,
        ]);
    }

    /**
     * Botton detail sur emploi
     * @Route("/infosEmploi/sup/{id}", name="emploi_imprimer_superieur")
     */
    public function imprimerEmploiSuperieur(InfosEmploi $id): Response
    {
        $user = $this->getUser();
        $infosEmploi = $this->infosEmploiRepository->getImprimerEmploiSuperieur($id);
        //return new Response($emplois->getId());
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if (!$infosEmploi) {
            $resultat = "";
            $classe = "";
        }
        $data = array();
        foreach ($infosEmploi as $value) {
            $data [] = array(
                'id' => $value['id'],
                'heureDebut' => $value['heureDebut'],
                'heureFin' => $value['heureFin'],
                'dateDebut' => $value['dateDebut'],
                'dateDebut' => $value['dateFin'],
                'periode' => $value['periode'],
                'intituleModule' => $value['intituleModule']
            );
        }
         //return new Response($data['intituleModule']);
        return $this->render('emploi/emploi_imprimer1.html.twig', [
            'infosEmploi' => $data, 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * Botton detail sur emploi
     * @Route("/infosEmploi/{id}", name="emploi_imprimer")
     */
    public function imprimerEmploi(InfosEmploi $id): Response
    {
        $user = $this->getUser();
        $infosEmploi = $this->infosEmploiRepository->getImprimerEmploi($id);
        //return new Response($emplois->getId());
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if (!$infosEmploi) {
            $resultat = "";
            $classe = "";
        }
        $data = array();
        foreach ($infosEmploi as $value) {
            $data [] = array(
                'id' => $value['id'],
                'heureDebut' => $value['heureDebut'],
                'heureFin' => $value['heureFin'],
                'dateDebut' => $value['dateDebut'],
                'dateDebut' => $value['dateFin'],
                'periode' => $value['periode'],
                'intituleMatiere' => $value['intituleMatiere']
            );
        }
         //return new Response($data['intituleModule']);
        return $this->render('emploi/emploi_imprimer2.html.twig', [
            'infosEmploi' => $data, 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

}
