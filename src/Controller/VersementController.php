<?php

namespace App\Controller;

use DateTime;
use DateInterval;
use App\Entity\Niveau;
use App\Entity\Filiere;
use App\Entity\Apprenant;
use App\Entity\Versement;
use App\Entity\Inscription;
use App\Form\VersementType;
use App\Constante\Constantes;
use App\Form\EtatIntervalType;
use App\Form\AutreInscriptionType;
use App\Form\VersementIntervalType;
use App\Repository\AnneeRepository;
use App\Repository\VersementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VersementController extends AbstractController
{
    private $versementRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $versementManager;

    public function __construct(VersementRepository $versementRepository, EntityManagerInterface $entityManager, AnneeRepository $anneeRepository)
    {
        $this->versementRepository = $versementRepository;
        $this->entityManager = $entityManager;
        $this->anneeRepository = $anneeRepository;
    }
    /**
     * @Route("/Versement", name="list_versement")
     */
    public function Versement(): Response
    {
        $user = $this->getUser();
        //ici j'ai declare la variable annee tout en initiant par l'annee en cours
        $annee = date('Y');
        // pour voir l'annee qui est en cours
        //return new Response($annee);
        $versements = $this->versementRepository->getMontant($user->getStructure()->getId(), $annee);
        $resultat = "";
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
        if (!$versements) {
            $resultat = "";
            $classe = "";
        }
        return $this->render('versement/versement.html.twig', [
            'versements' => $versements, 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * @Route("/Versement/{id}/new", name="create_versement")
     */
    public function createVersement(Request $request, Inscription $inscription, \Swift_Mailer $mailer): Response
    {
        $users = $this->getUser();
        $versement = new Versement();
        $resultat = "";
        $classe = "";

        $form = $this->createForm(VersementType::class, $versement);
        $roles = $users->getRoles();
        $role = $roles[0];
        $annee = date('Y');

        $form = $this->createForm(VersementType::class, $versement);
        $date = date('d-m-Y');
        $dateJour = \DateTime::createFromFormat('d-m-Y', $date);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $versement->setStatut(1);
            $versement->setDateVersements($dateJour);
            $versement->setInscription($inscription);
            $versement->setUser($users);
            $annees_entity = $this->anneeRepository->findOneByLibelleAnneeScolaire($annee);
            $versement->setAnnee($annees_entity);
            $this->entityManager->persist($versement);
            $this->entityManager->flush();

            // ici je recupere le motif, le nom de l'apprenant et j'envoie le message du surveillant
            $versements = $this->versementRepository->getVersementRestant($inscription->getApprenant()->getId());

            foreach ($versements as $value) {
                $montantRestant =  $value['montant'] - $value['montantVersement'];
                $data[] = array(
                    'montantVersement' => $value['montantVersement'],
                    'montant' => $value['montant'],
                    'montantRestant' => $montantRestant
                );
            }

            $montantTotalVerser = $data[0]['montantVersement'];
            $montantVerser = $versement->getMontantVersement();
            //return new Response($montantVerser);
            $dateVersement = $versement->getDateVersements()->format('d-m-Y');
            $monApprenant = $versement->getInscription()->getApprenant()->getNomApprenant();
            //return new Response($dateAbsence);

            if ($role === Constantes::ROLE_CAISSIER_SUPERIEUR) {
                $niveau = $versement->getInscription()->getNiveau()->getLibelleNiveau();
                $infosCaissier = $dateVersement . ' ' . $montantVerser . ' ' . $montantTotalVerser . ' ' . $montantRestant;
            } else {
                $niveau = $versement->getInscription()->getNiveau()->getLibelleNiveau();
                $infosCaissier = '';
            }
            // ici je recupere l'email de l'apprenant selectionné
            // return new Response($absence->getApprenant()->getEmail());
            $email = $versement->getInscription()->getApprenant()->getEmail();
            if ($versement) {
                $to = array(
                    "$email" => "$monApprenant",
                );
                $message = (new \Swift_Message('message'))
                    ->setFrom($users->getEmail())
                    ->setTo($to)
                    ->setBody($infosCaissier);
                $mailer->send($message);
                return $this->redirectToRoute('list_versement');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('versement/versement-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
                ]);
            }
        }
        return $this->render('versement/versement-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * @Route("/Versement/edit/{id}", name="edit_versement")
     */
    public function editVersement(Request $request, Versement $versement): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];

        $form = $this->createForm(VersementType::class, $versement);
        if ($role !== Constantes::ROLE_CAISSIER_SUPERIEUR && $role !== Constantes::ROLE_CAISSIER_SECONDAIRE && $role !== Constantes::ROLE_CAISSIER_PRIMAIRE) {
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('versement/versement-form-edit.html.twig', [
                'action' => 'edit',
                'versement' => $versement,
                'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($versement);
            $this->entityManager->flush();
            if ($versement) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if ($primaryRole === Constantes::ROLE_CAISSIER_SUPERIEUR || $primaryRole === Constantes::ROLE_CAISSIER_SECONDAIRE || $primaryRole === Constantes::ROLE_CAISSIER_PRIMAIRE) {
                    return $this->redirectToRoute('list_versement');
                }
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";

                return $this->render('versement/versement-form-edit.html.twig', [
                    'action' => 'edit',
                    'versement' => $versement,
                    'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
                ]);
            }
        }
        return $this->render('versement/versement-form.html.twig', [
            'action' => 'edit',
            'versement' => $versement,
            'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * @Route("/desactiverVersement/{id}", name="desabled_versement")
     */

    public function desactiverVersement(Request $request, versement $versement_entity): Response

    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
        $entityManager = $this->getDoctrine()->getManager();
        if ($role !== Constantes::ROLE_CAISSIER_SUPERIEUR && $role !== Constantes::ROLE_CAISSIER_SECONDAIRE && $role !== Constantes::ROLE_CAISSIER_PRIMAIRE) {
            return $this->render('versement/versement.html.twig', [
                'versement' => null,
            ]);
        }
        $versement_entity->setStatut(0);
        $entityManager->persist($versement_entity);
        $entityManager->flush();
        $versements = $this->versementRepository->getVersements();

        if ($entityManager) {
            return $this->redirectToRoute('list_versement');
        } else {
            return $this->render('versement/versement.html.twig', [
                'versements' => $versements,
            ]);
        }

        return $this->render('versement/versement.html.twig', [
            'versements' => $versements,
        ]);
    }

    /**
     * @Route("/activerVersement/{id}", name="enable_versement")
     */

    public function activerVersement(Request $request, versement $versement_entity): Response

    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
        $entityManager = $this->getDoctrine()->getManager();
        if ($role !== Constantes::ROLE_CAISSIER_SUPERIEUR && $role !== Constantes::ROLE_CAISSIER_SECONDAIRE && $role !== Constantes::ROLE_CAISSIER_PRIMAIRE) {

            return $this->render('versement/versement.html.twig', [
                'versement' => null,
            ]);
        }
        $versement_entity->setStatut(1);
        $entityManager->persist($versement_entity);
        $entityManager->flush();
        $versements = $this->versementRepository->getVersements();

        if ($entityManager) {
            return $this->redirectToRoute('list_versement');
        } else {
            return $this->render('versement/versement.html.twig', [
                'versements' => $versements,
            ]);
        }

        return $this->render('versement/versement.html.twig', [
            'versements' => $versements,
        ]);
    }
    /**
     * Botton detail sur versement
     * @Route("/versement/{id}", name="detail_versement")
     */
    public function detailVersement(Versement $id): Response
    {
        $user = $this->getUser();
        $versements = $this->versementRepository->getDetail($id);

        # return new Response($versements->getId());
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if (!$versements) {
            $resultat = "";
            $classe = "";
        }
        $data = array();
        $montantRestant = 0;
        foreach ($versements as $value) {
            $montantRestant =  $value['montant'] - $value['montantVersement'];
            $data = array(
                'id' => $value['id'],
                'NomApprenant' => $value['NomApprenant'],
                'PrenomApprenant' => $value['PrenomApprenant'],
                'montantVersement' => $value['montantVersement'],
                'DateVersements' => $value['DateVersements'],
                'montant' => $value['montant'],
                'Adresse' => $value['Adresse'],
                'montantRestant' => $montantRestant,
                'libelleNiveau' => $value['libelleNiveau']
            );
        }
        // return new Response($data[0]['id']);

        return $this->render('versement/detail-versement.html.twig', [
            'versements' => $data, 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    //Cette methode me permet de compter le nombre des versements de apprenant 
    public function nombreVersement(): Response
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
            $versements = $this->versementRepository->getNombreVersement($user->getStructure()->getId(), $day);
        } else {
            $versements = $this->versementRepository->getNombreVersementByCaissier($user->getStructure()->getId(), $day, $user->getId());
        }

        if ($versements) {
            $nombre = intval($versements[0]['nombre']);
            $response = new Response($nombre);
            return $response;
        }
        return new Response($response);
    }

    /**
     * @Route("/rapport/versement", name="list_raportDateIntervalVersement")
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

        $form = $this->createForm(VersementIntervalType::class, null);

        if (
            $role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE
            && $role !== Constantes::ROLE_DIRECTEUR_SUPERIEUR && $role !== Constantes::ROLE_DIRECTEUR_SECONDAIRE && $role !== Constantes::ROLE_DIRECTEUR_PRIMAIRE
            && $role !== Constantes::ROLE_CAISSIER_SUPERIEUR && $role !== Constantes::ROLE_CAISSIER_SECONDAIRE && $role !== Constantes::ROLE_CAISSIER_PRIMAIRE
        ) {
            $resultat = "Vous n'avez pas l'autorisation sur cette page.";
            $classe = "alert alert-danger";
            return $this->render('rapport/rapport-versement-form.html.twig', [
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
            $versements = $this->versementRepository->getRapportDateInterval($dateFrom, $dateTo, $user->getStructure()->getId());
            //return new Response($versements);
            return $this->render('rapport/rapport-versement-form.html.twig', [
                'versements' => $versements, 'resultat' => $resultat, 'classe' => $classe,
                'form' => $form->createView()
            ]);
        } else {
            return $this->render('rapport/rapport-versement-form.html.twig', [
                'versements' => null, 'resultat' => $resultat, 'classe' => $classe,
                'form' => $form->createView()
            ]);
        }
    }

    /**
     * @Route("/rapport/etat/versement", name="list_etatDateIntervalle")
     */

    public function EtatDateIntervalle(Request $request): Response
    {
        $user = $this->getUser();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        $dateTo = "";
        $dateFrom = "";

        $niveauId = "";
        $form = $this->createForm(EtatIntervalType::class, null);

        if (
            $role !== Constantes::ROLE_ADMIN_SUPERIEUR && $role !== Constantes::ROLE_ADMIN_SECONDAIRE && $role !== Constantes::ROLE_ADMIN_PRIMAIRE
            && $role !== Constantes::ROLE_DIRECTEUR_SUPERIEUR && $role !== Constantes::ROLE_DIRECTEUR_SECONDAIRE && $role !== Constantes::ROLE_DIRECTEUR_PRIMAIRE
            && $role !== Constantes::ROLE_CAISSIER_SUPERIEUR && $role !== Constantes::ROLE_CAISSIER_SECONDAIRE && $role !== Constantes::ROLE_CAISSIER_PRIMAIRE
        ) {
            $resultat = "Vous n'avez pas l'autorisation sur cette page.";
            $classe = "alert alert-danger";
            return $this->render('rapport/rapport-etat-form.html.twig', [
                'inscriptions' => null,
                'resultat' => $resultat, 'total' => 0,
                'classe' => $classe,
                'form' => $form->createView(),

            ]);
        }
        $total = 0;
        $totalNiveau = 0;
        $totalRestant = 0;
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
            $versements = $this->versementRepository->getEtatDateIntervalle($dateFrom, $dateTo, $user->getStructure()->getId(), $niveau);
            $etat = array();
            foreach ($versements as $value) {
                $montantRestant =  $value['montant'] - $value['montantVersement'];
                $total += $value['montantVersement'];
                $totalNiveau += $value['montant'];
                $totalRestant += $montantRestant;
                $etat[] = array(
                    'montantVersement' => $value['montantVersement'],
                    'montant' => $value['montant'],
                    'totalNiveau' => $totalNiveau,
                    'totalRestant' => $totalRestant,
                    'total' => $total,
                    'montantRestant' => $montantRestant,
                    'NomApprenant' => $value['NomApprenant'],
                    'PrenomApprenant' => $value['PrenomApprenant'],
                    'intituleFiliere' => $value['intituleFiliere'],
                    'libelleNiveau' => $value['libelleNiveau'],
                    'DateVersements' => $value['DateVersements'],

                );
            }
            //return new Response($data[]['libelleNiveau']);
            return $this->render('rapport/rapport-etat-form.html.twig', [
                'versements' => $etat, 'resultat' => $resultat, 'classe' => $classe, 'total' => $total, 'totalNiveau' => $total, 'totalNiveau' => $totalNiveau, 'totalRestant' => $totalRestant,
                'form' => $form->createView()
            ]);
        } else {
            return $this->render('rapport/rapport-etat-form.html.twig', [
                'versements' => null, 'resultat' => $resultat, 'classe' => $classe, 'total' => 0, 'totalNiveau' => $total, 'totalNiveau' => $totalNiveau, 'totalRestant' => $totalRestant,
                'form' => $form->createView()
            ]);
        }
    }
}
