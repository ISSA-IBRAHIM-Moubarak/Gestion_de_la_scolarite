<?php

namespace App\Controller;

use DateTime;
use App\Constante\Constantes;
use App\Repository\InscriptionRepository;
use App\Repository\VersementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AccueilController extends AbstractController
{
    private $tokenManager;

    public function __construct(CsrfTokenManagerInterface $tokenManager = null, InscriptionRepository $inscriptionRepository, VersementRepository $versementRepository)
    {
        $this->tokenManager = $tokenManager;
        $this->inscriptionRepository = $inscriptionRepository;
        $this->versementRepository = $versementRepository;
    }

    /**
     * @Route("/", name="accueil")
     */
    public function accueil()
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
        $dataInscription = [];
        $dataVersement = [];
        $day = (new DateTime())->setTime(0, 0);

        if (
            $role === Constantes::ROLE_ADMIN_SUPERIEUR || $role === Constantes::ROLE_ADMIN_SECONDAIRE || $role === Constantes::ROLE_ADMIN_PRIMAIRE
            || $role === Constantes::ROLE_DIRECTEUR_SUPERIEUR || $role === Constantes::ROLE_DIRECTEUR_SECONDAIRE || $role === Constantes::ROLE_DIRECTEUR_PRIMAIRE
        ) {
            $grapheInscriptions = $this->inscriptionRepository->getGrapheInscription($user->getStructure()->getId(), $day);
            $grapheVersements = $this->versementRepository->getGrapheVersement($user->getStructure()->getId(), $day);

            if (!$grapheInscriptions) {
                //$montantGrapheInscription = intval($grapheInscriptions[0]['nombre']);
                //$response = new Response($montant);
                //return $response;
                $resultat = "";
                $classe = "";
            }
            $dataInscription = array();
            foreach ($grapheInscriptions as $value) {
                $dataInscription[] = array(
                    'nombre' => $value['nombreGrapheInscription'],
                    'montant' => $value['montantGrapheInscription'],
                    'date' => $value['dateGrapheInscription']
                );
            }

            if (!$grapheVersements) {
                //$montantGrapheInscription = intval($grapheInscriptions[0]['nombre']);
                //$response = new Response($montant);
                //return $response;
                $resultat = "";
                $classe = "";
            }
            $dataVersement = array();
            foreach ($grapheVersements as $value) {
                $dataVersement[] = array(
                    'nombre' => $value['nombreGrapheVersement'],
                    'montant' => $value['montantGrapheVersement'],
                    'date' => $value['dateGrapheVersement']
                );
            }
        }else{
            $grapheInscriptions = null;
            $grapheVersements = null;
        }

        return $this->render('user/accueil.html.twig', [
            'grapheInscriptions' => $dataInscription,
            'grapheVersements' => $dataVersement,
            'controller_name' => 'AccueilController'
        ]);
    }
}
