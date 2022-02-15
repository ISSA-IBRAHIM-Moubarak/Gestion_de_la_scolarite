<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Absence;
use App\Form\AbsenceType;
use App\Constante\Constantes;
use App\Form\EditAbsenceType;
use App\Repository\AbsenceRepository;
use App\Repository\ApprenantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AbsenceController extends AbstractController

{
    private $absenceRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $absenceManager;



    public function __construct(AbsenceRepository $absenceRepository, EntityManagerInterface $entityManager, ApprenantRepository $apprenantRepository)
    {
        $this->absenceRepository = $absenceRepository;
        $this->entityManager = $entityManager;
        $this->apprenantRepository  = $apprenantRepository;
    }


    /**
     * @Route("/absence", name="list_absence")
     */

    public function absence(): Response
    {
        $user = $this->getUser();
        $absences = $this->absenceRepository->getAbsenceBySurveillants($user->getStructure()->getId());
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];

        if (!$absences) {
            $resultat = "";
            $classe = "";
        }
        return $this->render('absence/absence.html.twig', [
            'absences' => $absences, 'resultat' => $resultat, 'classe' => $classe
        ]);
    }
//fonction pour le sms simple
    function sendsms($num, $message)
    {
        $message = urlencode($message);
        $num = urlencode($num);
        $url = "sms.itechbusiness.ne/index.php?app=webservices&h=4304af58d09be6850330a36f5f6a60b7&u=stagiairesiai2&op=pv&to=$num&msg=$message";
        exec("wget -O- -q \"$url\"");
    }

    /**
     * @Route("/absence/new", name="create_absence")
     */
    public function createAbsence(Request $request, \Swift_Mailer $mailer): Response
    {
        $users = $this->getUser();
        $absence = new Absence();
        $resultat = "";
        $classe = "";

        $roles = $users->getRoles();
        $role = $roles[0];
        $user = "";

        if ($role === Constantes::ROLE_SURVEILLANT_SUPERIEUR) {
            $form = $this->createForm(AbsenceType::class, $absence);
        } else {
            $form = $this->createForm(EditAbsenceType::class, $absence);
        }
        $dateDay = date('d-m-Y');
        $dateJourDay = \DateTime::createFromFormat('d-m-Y', $dateDay);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $absence->setPeriode($dateJourDay);
            //return new Response($absence);
            $absence->setStatut(1);
            $absence->setUser($users);
            $this->entityManager->persist($absence);
            $this->entityManager->flush();
            $dateDay = date('d-m-Y');
            $dateJourDay = \DateTime::createFromFormat('d-m-Y', $dateDay);
            // ici je recupere le motif, le nom de l'apprenant et j'envoie le message du surveillant
            $motif = $absence->getMotif();
            $dateAbsence = $absence->getPeriode()->format('d-m-Y');
            $monApprenant = $absence->getApprenant()->getNomApprenant();
            //return new Response($dateAbsence);
            
            if ($role === Constantes::ROLE_SURVEILLANT_SUPERIEUR) {
                $moduleAbsence = $absence->getModule()->getIntituleModule();
                $infosSurveillant = "$monApprenant vient d'absenter le $dateAbsence pour le motif : $motif et a rate son cours de $moduleAbsence";
            } else {
                $matiereAbsence = $absence->getMatiere()->getIntituleMatiere();
                $infosSurveillant = "$monApprenant vient d'absenter le $dateAbsence pour le motif : $motif et a rate son cours de $matiereAbsence";
            }
            // ici je recupere l'email de l'apprenant selectionné
            // return new Response($absence->getApprenant()->getEmail());
            $email = $absence->getApprenant()->getEmail();
            $sms = $absence->getApprenant()->getContact();
             //return new Response($sms);
            if ($absence) {
                //envoie du sms simple
                $this->sendsms($sms, $infosSurveillant);

                $to = array(
                    "$email" => "$monApprenant",
                );
                $message = (new \Swift_Message('message'))
                    ->setFrom($users->getEmail())
                    ->setTo($to)
                    ->setBody($infosSurveillant);
                $mailer->send($message);


                return $this->redirectToRoute('list_absence');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('absence/absence-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
                ]);
            }

        }
        return $this->render('absence/absence-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
        ]);
    }
    /**
     * @Route("/absence/edit/{id}", name="edit_absence")
     */
    public function editAbsence(Request $request, Absence $absence): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];


        if ($role === Constantes::ROLE_SURVEILLANT_SUPERIEUR) {
            $form = $this->createForm(AbsenceType::class, $absence);
            //return new Response($role);

        } else {
            //return new Response($role);
            $form = $this->createForm(EditAbsenceType::class, $absence);
        }

        if ($role !== Constantes::ROLE_SURVEILLANT_SUPERIEUR && $role !== Constantes::ROLE_SURVEILLANT_SECONDAIRE && $role !== Constantes::ROLE_SURVEILLANT_PRIMAIRE) {
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('absence/absence-form.html.twig', [
                'action' => 'edit',
                'absence' => $absence,
                'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($absence);
            $this->entityManager->flush();
            if ($absence) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if ($primaryRole === Constantes::ROLE_SURVEILLANT_SUPERIEUR || $primaryRole === Constantes::ROLE_SURVEILLANT_SECONDAIRE || $primaryRole === Constantes::ROLE_SURVEILLANT_PRIMAIRE) {
                    return $this->redirectToRoute('list_absence');
                }
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";

                return $this->render('absence/absence-form.html.twig', [
                    'action' => 'edit',
                    'absence' => $absence,
                    'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
                ]);
            }
        }
        return $this->render('absence/absence-form.html.twig', [
            'action' => 'edit',
            'absence' => $absence,
            'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
        ]);
    }



    /**
     * @Route("/desactiverabsence/{id}", name="desabled_absence")
     */

    public function desactiverAbsencee(Request $request, absence $absence_entity): Response

    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
        $entityManager = $this->getDoctrine()->getManager();
        if ($role !== Constantes::ROLE_SURVEILLANT_SUPERIEUR && $role !== Constantes::ROLE_SURVEILLANT_SECONDAIRE && $role !== Constantes::ROLE_SURVEILLANT_PRIMAIRE) {
            return $this->render('absence/absence.html.twig', [
                'absence' => null,
            ]);
        }
        $absence_entity->setStatut(0);
        $entityManager->persist($absence_entity);
        $entityManager->flush();
        if ($entityManager) {
            return $this->redirectToRoute('list_absence');
        } else {
            return $this->render('absence/absence.html.twig', [
                'absences' => $absence_entity,
            ]);
        }

        return $this->render('absence/absence.html.twig', [
            'absences' => $absence_entity,
        ]);
    }
    /**
     * @Route("/activerabsence/{id}", name="enable_absence")
     */

    public function activerAbsence(Request $request, absence $absence_entity): Response

    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
        $entityManager = $this->getDoctrine()->getManager();
        if ($role !== Constantes::ROLE_SURVEILLANT_SUPERIEUR && $role !== Constantes::ROLE_SURVEILLANT_SECONDAIRE && $role !== Constantes::ROLE_SURVEILLANT_PRIMAIRE) {
            return $this->render('absence/absence.html.twig', [
                'absence' => null,
            ]);
        }
        $absence_entity->setStatut(1);
        $entityManager->persist($absence_entity);
        $entityManager->flush();

        if ($entityManager) {
            return $this->redirectToRoute('list_absence');
        } else {
            return $this->render('absence/absence.html.twig', [
                'absences' => $absence_entity,
            ]);
        }

        return $this->render('absence/absence.html.twig', [
            'absences' => $absence_entity,
        ]);
    }

    //Cette methode me permet de compter le nombre des absences de l'apprenant 
    public function nombreAbsence(): Response
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
            $absences = $this->absenceRepository->getNombreAbsence($user->getStructure()->getId(), $day);
        } else {
            $absences = $this->absenceRepository->getNombreAbsenceBySurveillant($user->getStructure()->getId(), $day, $user->getId());
        }

        if ($absences) {
            $nombre = intval($absences[0]['nombre']);
            $response = new Response($nombre);
            return $response;
        }
        return new Response($response);
    }
}
