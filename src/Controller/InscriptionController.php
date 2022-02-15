<?php

namespace App\Controller;

use DateTime;
use DateInterval;
use App\Entity\Annee;
use DateTimeInterface;
use App\Entity\Apprenant;
use App\Entity\Versement;
use App\Entity\Inscription;
use App\Constante\Constantes;
use App\Form\InscriptionType;
use App\Form\AjoutByClasseType;
use App\Form\RapportBourseType;
use App\Form\RapportNiveauType;
use App\Form\EditInscriptionType;
use App\Form\AutreInscriptionType;
use App\Repository\UserRepository;
use App\Repository\AnneeRepository;
use App\Form\RapportNiveauAutreType;
use App\Repository\AnnneeRepository;
use App\Form\InscriptionIntervalType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\EtatInscriptionIntervalType;
use App\Repository\InscriptionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InscriptionController extends AbstractController
{
    private $inscriptionRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $inscriptionManager;

    public function __construct(InscriptionRepository $inscriptionRepository, EntityManagerInterface $entityManager, UserRepository $userRepository, AnneeRepository $anneeRepository)
    {
        $this->inscriptionRepository = $inscriptionRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->anneeRepository = $anneeRepository;
    }


    /**
     * @Route("/inscription", name="list_inscription")
     */
    public function Inscription(): Response
    {
        $user = $this->getUser();
        $annee = date('Y');
        //return new Response($annee);
        $inscriptions = $this->inscriptionRepository->getInscriptionByCaissiers($user->getStructure()->getId(), $annee);
        $resultat = "";
        //return new Response($annee);

        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if (
            $role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE
            && $role !== Constantes::ROLE_DIRECTEUR_SUPERIEUR && $role !== Constantes::ROLE_DIRECTEUR_SECONDAIRE && $role !== Constantes::ROLE_DIRECTEUR_PRIMAIRE
            && $role !== Constantes::ROLE_CAISSIER_SUPERIEUR && $role !== Constantes::ROLE_CAISSIER_SECONDAIRE && $role !== Constantes::ROLE_CAISSIER_PRIMAIRE
        ) {
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('user/user.html.twig', [
                'users' => null, 'resultat' => $resultat, 'classe' => $classe
            ]);
        }
        if (!$inscriptions) {
            $resultat = "";
            $classe = "";
        }
        return $this->render('inscription/inscription.html.twig', [
            'inscriptions' => $inscriptions, 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * @Route("/inscription/{id}/new", name="create_inscription")
     */
    public function createInscription(Request $request, Apprenant $apprenant): Response
    {
        $users = $this->getUser();
        $inscription = new Inscription();
        $resultat = "";
        $classe = "";
        $roles = $users->getRoles();
        $role = $roles[0];
        $annee = date('Y');

        if ($role === Constantes::ROLE_CAISSIER_SUPERIEUR) {
            $form = $this->createForm(InscriptionType::class, $inscription);
        } else {
            $form = $this->createForm(AutreInscriptionType::class, $inscription);
        }
        $date = date('d-m-Y');
        $dateJour = \DateTime::createFromFormat('d-m-Y', $date);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $inscription->setStatut(1);
            $inscription->setApprenant($apprenant);
            $inscription->setDateVersement($dateJour);
            $users_entity = $this->userRepository->findOneById($users->getId());
            $inscription->setUser($users_entity);
            $annees_entity = $this->anneeRepository->findOneByLibelleAnneeScolaire($annee);
            $inscription->setAnnee($annees_entity);
            $this->entityManager->persist($inscription);
            $this->entityManager->flush();
            if ($inscription) {
                return $this->redirectToRoute('list_inscription');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('inscription/inscription-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
                ]);
            }
        }
        return $this->render('inscription/inscription-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * @Route("/inscription/edit/{id}", name="edit_inscription")
     */
    public function editInscription(Request $request, Inscription $inscription): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];

        if ($role === Constantes::ROLE_CAISSIER_SUPERIEUR) {
            $form = $this->createForm(InscriptionType::class, $inscription);
        } else {
            $form = $this->createForm(AutreInscriptionType::class, $inscription);
        }
        if ($role !== Constantes::ROLE_CAISSIER_SUPERIEUR && $role !== Constantes::ROLE_CAISSIER_SECONDAIRE && $role !== Constantes::ROLE_CAISSIER_PRIMAIRE) {
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('inscription/inscription-form-edit.html.twig', [
                'action' => 'edit',
                'inscription' => $inscription,
                'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($inscription);
            $this->entityManager->flush();
            if ($inscription) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if ($primaryRole === Constantes::ROLE_CAISSIER_SUPERIEUR || $primaryRole === Constantes::ROLE_CAISSIER_SECONDAIRE || $primaryRole === Constantes::ROLE_CAISSIER_PRIMAIRE) {
                    return $this->redirectToRoute('list_inscription');
                }
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";

                return $this->render('inscription/inscription-form-edit.html.twig', [
                    'action' => 'edit',
                    'inscription' => $inscription,
                    'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
                ]);
            }
        }
        return $this->render('inscription/inscription-form-edit.html.twig', [
            'action' => 'edit',
            'inscription' => $inscription,
            'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * @Route("/desactiverinscription/{id}", name="desabled_inscription")
     */

    public function desactiverInscription(Request $request, inscription $inscription_entity): Response

    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
        $entityManager = $this->getDoctrine()->getManager();
        if ($role !== Constantes::ROLE_CAISSIER_SUPERIEUR && $role !== Constantes::ROLE_CAISSIER_SECONDAIRE && $role !== Constantes::ROLE_CAISSIER_PRIMAIRE) {

            return $this->render('inscription/inscription.html.twig', [
                'inscription' => null,
            ]);
        }
        $inscription_entity->setStatut(0);
        $entityManager->persist($inscription_entity);
        $entityManager->flush();
        $inscriptions = $this->inscriptionRepository->getInscriptions($user->getId());

        if ($entityManager) {
            return $this->redirectToRoute('list_inscription');
        } else {
            return $this->render('inscription/inscription.html.twig', [
                'inscriptions' => $inscriptions,
            ]);
        }

        return $this->render('inscription/inscription.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
    }

    /**
     * @Route("/activerinscription/{id}", name="enable_inscription")
     */

    public function activerInscription(Request $request, inscription $inscription_entity): Response

    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
        $entityManager = $this->getDoctrine()->getManager();
        if ($role !== Constantes::ROLE_CAISSIER_SUPERIEUR && $role !== Constantes::ROLE_CAISSIER_SECONDAIRE && $role !== Constantes::ROLE_CAISSIER_PRIMAIRE) {
            return $this->render('inscription/inscription.html.twig', [
                'inscription' => null,
            ]);
        }
        $inscription_entity->setStatut(1);
        $entityManager->persist($inscription_entity);
        $entityManager->flush();
        $inscriptions = $this->inscriptionRepository->getInscriptions($user->getId());

        if ($entityManager) {
            return $this->redirectToRoute('list_inscription');
        } else {
            return $this->render('inscription/inscription.html.twig', [
                'inscriptions' => $inscriptions,
            ]);
        }

        return $this->render('inscription/inscription.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
    }

    /**
     * Botton detail sur inscription
     * @Route("/inscription/{id}", name="detail_inscription")
     */
    public function detailInscription(Inscription $id): Response
    {
        $user = $this->getUser();
        $inscriptions = $this->inscriptionRepository->findOneById($id);

        # return new Response($inscriptions->getId());
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if (!$inscriptions) {
            $resultat = "";
            $classe = "";
        }
        return $this->render('inscription/detail-inscription.html.twig', [
            'inscriptions' => $inscriptions, 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * Botton detail sur inscription
     * @Route("/inscription/carte/{id}", name="carte_identite")
     */
    public function carteInscription(Inscription $id): Response
    {
        $user = $this->getUser();
        $inscriptions = $this->inscriptionRepository->findOneById($id);

        # return new Response($inscriptions->getId());
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if (!$inscriptions) {
            $resultat = "";
            $classe = "";
        }
        return $this->render('inscription/carte-inscription.html.twig', [
            'inscriptions' => $inscriptions, 'resultat' => $resultat, 'classe' => $classe
        ]);
    }


    public function nombreInscription(): Response
    {
        $user = $this->getUser();
        $response = 0;
        $roles = $user->getRoles();
        $role = $roles[0];
        $day = (new DateTime())->setTime(0, 0);

        if (
            $role === Constantes::ROLE_ADMIN_SUPERIEUR || $role === Constantes::ROLE_ADMIN_SECONDAIRE || $role === Constantes::ROLE_ADMIN_PRIMAIRE
            || $role === Constantes::ROLE_DIRECTEUR_SUPERIEUR || $role === Constantes::ROLE_DIRECTEUR_SECONDAIRE || $role === Constantes::ROLE_DIRECTEUR_PRIMAIRE
        ) {
            $inscriptions = $this->inscriptionRepository->getNombreInscription($user->getStructure()->getId(), $day);
        } else {
            // return new Response('ok');
            $inscriptions = $this->inscriptionRepository->getNombreInscriptionByCaissier($user->getStructure()->getId(), $day, $user->getId());
        }

        if ($inscriptions) {
            $nombre = intval($inscriptions[0]['nombre']);
            $response = new Response($nombre);
            return $response;
        }
        return new Response($response);
    }

    /**
     * @Route("/rapport", name="list_raportDateIntervalInscription")
     */

    public function RapportDateInterval(Request $request): Response
    {
        $user = $this->getUser();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        $dateTo = "";
        $dateFrom = "";

        $form = $this->createForm(InscriptionIntervalType::class, null);

        if (
            $role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE
            && $role !== Constantes::ROLE_DIRECTEUR_SUPERIEUR && $role !== Constantes::ROLE_DIRECTEUR_SECONDAIRE && $role !== Constantes::ROLE_DIRECTEUR_PRIMAIRE
            && $role !== Constantes::ROLE_CAISSIER_SUPERIEUR && $role !== Constantes::ROLE_CAISSIER_SECONDAIRE && $role !== Constantes::ROLE_CAISSIER_PRIMAIRE
        ) {
            $resultat = "Vous n'avez pas l'autorisation sur cette page.";
            $classe = "alert alert-danger";
            return $this->render('rapport/rapport-form.html.twig', [
                'inscriptions' => null,
                'resultat' => $resultat,
                'classe' => $classe,
                'form' => $form->createView(),

            ]);
        }
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $dateFrom = $data['dateFrom'];
            $dateTo = $data['dateTo'];
            if ($dateFrom) {
                $dateFrom = new DateTime($dateFrom);
            }
            if ($dateTo) {
                $dateTo = new DateTime($dateTo);
                $dateTo->add(new DateInterval('P1D'));
            }
            $inscriptions = $this->inscriptionRepository->getRapportDateInterval($dateFrom, $dateTo, $user->getStructure()->getId());
            //return new Response($inscriptions);
            return $this->render('rapport/rapport-form.html.twig', [
                'inscriptions' => $inscriptions, 'resultat' => $resultat, 'classe' => $classe,
                'form' => $form->createView()
            ]);
        } else {
            return $this->render('rapport/rapport-form.html.twig', [
                'inscriptions' => null, 'resultat' => $resultat, 'classe' => $classe,
                'form' => $form->createView()
            ]);
        }
    }

    /**
     * @Route("/rapport/niveau/sup", name="list_raportNiveau")
     */
    public function RapportNiveauSup(Request $request): Response
    {
        $user = $this->getUser();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];


        $form = $this->createForm(RapportNiveauType::class, null);
        // return new Response('ok');

        if (
            $role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE
            && $role !== Constantes::ROLE_DIRECTEUR_SUPERIEUR && $role !== Constantes::ROLE_DIRECTEUR_SECONDAIRE && $role !== Constantes::ROLE_DIRECTEUR_PRIMAIRE
            && $role !== Constantes::ROLE_CAISSIER_SUPERIEUR && $role !== Constantes::ROLE_CAISSIER_SECONDAIRE && $role !== Constantes::ROLE_CAISSIER_PRIMAIRE
        ) {
            $resultat = "Vous n'avez pas l'autorisation sur cette page.";
            $classe = "alert alert-danger";
            return $this->render('rapport/rapport.html.twig', [
                'niveaux' => null,
                'resultat' => $resultat,
                'classe' => $classe,
                'form' => $form->createView(),

            ]);
        }

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $niveau = $data['niveau'];
            $bourse = $data['bourse'];
            $annee = $data['annee'];
            //$annee = date('Y');
            $niveaux = $this->inscriptionRepository->getApprenantByStructureSup($niveau, $bourse, $user->getStructure()->getId(), $annee);
            //return new Response($bourse);
            return $this->render('rapport/rapport.html.twig', [
                'niveaux' => $niveaux,
                'resultat' => $resultat, 'classe' => $classe,
                'form' => $form->createView()
            ]);
        } else {
            // $niveaux = $this->niveauRepository->getNombreApprenantByCaissier($user->getStructure()->getId());
            return $this->render('rapport/rapport.html.twig', [
                'niveaux' => null,
                'resultat' => $resultat, 'classe' => $classe,
                'form' => $form->createView()
            ]);
        }
    }

    /**
     * @Route("/rapport/niveau/primaire", name="list_raportNiveauInf")
     */
    public function RapportNiveau(Request $request): Response
    {
        $user = $this->getUser();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];


        $form = $this->createForm(RapportNiveauAutreType::class, null);
        // return new Response('ok');

        if (
            $role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE
            && $role !== Constantes::ROLE_DIRECTEUR_SUPERIEUR && $role !== Constantes::ROLE_DIRECTEUR_SECONDAIRE && $role !== Constantes::ROLE_DIRECTEUR_PRIMAIRE
            && $role !== Constantes::ROLE_CAISSIER_SUPERIEUR && $role !== Constantes::ROLE_CAISSIER_SECONDAIRE && $role !== Constantes::ROLE_CAISSIER_PRIMAIRE
        ) {
            $resultat = "Vous n'avez pas l'autorisation sur cette page.";
            $classe = "alert alert-danger";
            return $this->render('rapport/rapport-primaire.html.twig', [
                'niveaux' => null,
                'resultat' => $resultat,
                'classe' => $classe,
                'form' => $form->createView(),

            ]);
        }

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $niveau = $data['niveau'];
            $annee = $data['annee'];
            //$annee = date('Y');
            $niveaux = $this->inscriptionRepository->getApprenantByStructure($niveau, $user->getStructure()->getId(), $annee);
            //return new Response($bourse);
            return $this->render('rapport/rapport-primaire.html.twig', [
                'niveaux' => $niveaux,
                'resultat' => $resultat, 'classe' => $classe,
                'form' => $form->createView()
            ]);
        } else {
            // $niveaux = $this->niveauRepository->getNombreApprenantByCaissier($user->getStructure()->getId());
            return $this->render('rapport/rapport-primaire.html.twig', [
                'niveaux' => null,
                'resultat' => $resultat, 'classe' => $classe,
                'form' => $form->createView()
            ]);
        }
    }
    /**
     * @Route("/rapport/bourse", name="list_raportBourse")
     */
    public function RapportBourse(Request $request): Response
    {
        $user = $this->getUser();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];


        $form = $this->createForm(RapportBourseType::class, null);

        if (
            $role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE
            && $role !== Constantes::ROLE_DIRECTEUR_SUPERIEUR && $role !== Constantes::ROLE_DIRECTEUR_SECONDAIRE && $role !== Constantes::ROLE_DIRECTEUR_PRIMAIRE
            && $role !== Constantes::ROLE_CAISSIER_SUPERIEUR && $role !== Constantes::ROLE_CAISSIER_SECONDAIRE && $role !== Constantes::ROLE_CAISSIER_PRIMAIRE
        ) {
            $resultat = "Vous n'avez pas l'autorisation sur cette page.";
            $classe = "alert alert-danger";
            return $this->render('rapport/rapport-bourse-form.html.twig', [
                'bourses' => null,
                'resultat' => $resultat,
                'classe' => $classe,
                'form' => $form->createView(),

            ]);
        }

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $bourse = $data['bourse'];
            $bourses = $this->inscriptionRepository->getBourseByApprenant($bourse, $user->getStructure()->getId());
            //return new Response($bourses);
            return $this->render('rapport/rapport-bourse-form.html.twig', [
                'bourses' => $bourses, 'resultat' => $resultat, 'classe' => $classe,
                'form' => $form->createView()
            ]);
        } else {
            // $niveaux = $this->niveauRepository->getNombreApprenantByCaissier($user->getStructure()->getId());
            return $this->render('rapport/rapport-bourse-form.html.twig', [
                'bourses' => null, 'resultat' => $resultat, 'classe' => $classe,
                'form' => $form->createView()
            ]);
        }
    }

    /**
     * @Route("/rapport/etatinscription", name="list_etatInscriptionDateInterval")
     */
    public function EtatInscriptionDateIntervalle(Request $request): Response
    {
        $user = $this->getUser();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        $dateTo = "";
        $dateFrom = "";
        $niveauId = "";
        $form = $this->createForm(EtatInscriptionIntervalType::class, null);

        if (
            $role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE
            && $role !== Constantes::ROLE_DIRECTEUR_SUPERIEUR && $role !== Constantes::ROLE_DIRECTEUR_SECONDAIRE && $role !== Constantes::ROLE_DIRECTEUR_PRIMAIRE
            && $role !== Constantes::ROLE_CAISSIER_SUPERIEUR && $role !== Constantes::ROLE_CAISSIER_SECONDAIRE && $role !== Constantes::ROLE_CAISSIER_PRIMAIRE
        ) {
            $resultat = "Vous n'avez pas l'autorisation sur cette page.";
            $classe = "alert alert-danger";
            return $this->render('rapport/rapport-etatinscription-form.html.twig', [
                'inscriptions' => null,
                'resultat' => $resultat, 'total' => 0,
                'classe' => $classe,
                'form' => $form->createView(),

            ]);
        }
        $total = 0;
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $dateFrom = $data['dateFrom'];
            $dateTo = $data['dateTo'];
            $niveau = $data['niveau'];
            if ($dateFrom) {
                $dateFrom = new DateTime($dateFrom);
            }
            if ($dateTo) {
                $dateTo = new DateTime($dateTo);
                $dateTo->add(new DateInterval('P1D'));
            }
            $inscriptions = $this->inscriptionRepository->getEtatInscriptionDateIntervalle($dateFrom, $dateTo, $user->getStructure()->getId(), $niveau);
            //return new Response($inscriptions);
            $etat = array();
            foreach ($inscriptions as $value) {
                $total += $value['montantInscription'];
                $etat[] = array(
                    'montantInscription' => $value['montantInscription'],
                    'total' => $total,
                    'NomApprenant' => $value['NomApprenant'],
                    'PrenomApprenant' => $value['PrenomApprenant'],
                    'intituleFiliere' => $value['intituleFiliere'],
                    'libelleNiveau' => $value['libelleNiveau'],
                    'dateVersement' => $value['dateVersement'],

                );
            }
            return $this->render('rapport/rapport-etatinscription-form.html.twig', [
                'inscriptions' => $inscriptions, 'resultat' => $resultat, 'classe' => $classe, 'total' => $total,
                'form' => $form->createView()
            ]);
        } else {
            return $this->render('rapport/rapport-etatinscription-form.html.twig', [
                'inscriptions' => null, 'resultat' => $resultat, 'classe' => $classe, 'total' => 0,
                'form' => $form->createView()
            ]);
        }
    }

     /**
     * @Route("/inscription/{id}/classe", name="ajout_classe")
     */
    public function inscriptionByClasse(Inscription $id): Response
    {
        $user = $this->getUser();
        $inscriptions = $this->inscriptionRepository->findOneById($id);

        # return new Response($inscriptions->getId());
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if (!$inscriptions) {
            $resultat = "";
            $classe = "";
        }
        return $this->render('apprenantClasse/create_apprenantClasse.html.twig', [
            'inscriptions' => $inscriptions, 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

}
