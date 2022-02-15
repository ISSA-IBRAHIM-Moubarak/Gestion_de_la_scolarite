<?php

namespace App\Controller;
use DateTime;
use App\Entity\Transport;
use App\Form\TransportType;
use App\Constante\Constantes;
use App\Form\EditTransportType;
use App\Repository\TransportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TransportController extends AbstractController
{
    private $transportRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $transportManager;

    public function __construct(TransportRepository $transportRepository, EntityManagerInterface $entityManager)
    {
        $this->transportRepository = $transportRepository;
        $this->entityManager = $entityManager;
       
    }


    /**
     * @Route("/Transport", name="list_Transport")
     */
    public function Transport(): Response
    {
        $user = $this->getUser();
        $transports = $this->transportRepository->getGerantByTransport($user->getStructure()->getId());
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if(!$transports){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('transport/transport.html.twig', [
            'transports' => $transports,'resultat' => $resultat,'classe' => $classe
        ]);
    }

     /**
     * @Route("/Transport/new", name="create_Transport")
     */
    public function createTransport(Request $request): Response
    {
        $users = $this->getUser();
        $transport = new Transport();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(TransportType::class, $transport);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
          $transport->setStatut(1);
          $transport->setUser($users);
            $this->entityManager->persist($transport);
            $this->entityManager->flush();
            if ($transport) {
                return $this->redirectToRoute('list_Transport');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('transport/transport-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('transport/transport-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

      /**
     * @Route("/Transport/edit/{id}", name="edit_Transport")
     */
    public function editTransport(Request $request, Transport $transport): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(TransportType::class,$transport);
       
        if($role !== Constantes::ROLE_GERANT_TRANPORT_SUPERIEUR && $role !== Constantes::ROLE_GERANT_TRANPORT_SECONDAIRE && $role !== Constantes::ROLE_GERANT_TRANPORT_PRIMAIRE){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('Transport/Transport-form.html.twig', [
                'action' => 'edit',
                'transport' => $transport,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($transport);
            $this->entityManager->flush();
            if ($transport) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_GERANT_TRANPORT_SUPERIEUR || $primaryRole === Constantes::ROLE_GERANT_TRANPORT_SECONDAIRE || $primaryRole === Constantes::ROLE_GERANT_TRANPORT_PRIMAIRE){
                    return $this->redirectToRoute('list_Transport');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('transport/transport-form.html.twig', [
                    'action' => 'edit',
                    'transport' => $transport,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('transport/transport-form.html.twig', [
            'action' => 'edit',
            'transport' => $transport,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

        /**
     * @Route("/desactiverTransport/{id}", name="desabled_Transport")
     */

    public function desactiverTransport(Request $request, transport $transport_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_GERANT_TRANPORT_SUPERIEUR && $role !== Constantes::ROLE_GERANT_TRANPORT_SECONDAIRE && $role !== Constantes::ROLE_GERANT_TRANPORT_PRIMAIRE){
                return $this->render('transport/transport.html.twig', [
                    'transport' => null,
                ]);
            }
            $transport_entity->setStatut(0);
            $entityManager->persist($transport_entity);
            $entityManager->flush();
            $transports = $this->transportRepository->getTransports();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_Transport');
            } else {
                return $this->render('transport/transport.html.twig', [
                    'transports' => $transports,
                ]);
            }
        
            return $this->render('transport/transport.html.twig', [
                'transports' => $transports,
            ]);
    }

  /**
      * @Route("/activerTransport/{id}", name="enable_Transport")
      */

      public function activerTransport(Request $request, transport $transport_entity): Response
    
      {
          $user = $this->getUser();
          $roles = $user->getRoles();
          $role = $roles[0];    
          $entityManager = $this->getDoctrine()->getManager();
          if($role !== Constantes::ROLE_GERANT_TRANPORT_SUPERIEUR && $role !== Constantes::ROLE_GERANT_TRANPORT_SECONDAIRE && $role !== Constantes::ROLE_GERANT_TRANPORT_PRIMAIRE){
                  return $this->render('transport/transport.html.twig', [
                      'transport' => null,
                  ]);
              }
              $transport_entity->setStatut(1);
              $entityManager->persist($transport_entity);
              $entityManager->flush();
              $transports = $this->transportRepository->getTransports();
              
              if ($entityManager) {
                  return $this->redirectToRoute('list_Transport');
              } else {
                  return $this->render('transport/transport.html.twig', [
                      'transports' => $transports,
                  ]);
              }
          
              return $this->render('transport/transport.html.twig', [
                  'transports' => $transports,
              ]);
      }

      public function nombreTransport(): Response
    {
        $user = $this->getUser();
        $response = 0;
        $roles = $user->getRoles();
        $role = $roles[0];
        $day = (new DateTime())->setTime(0,0);

        if ($role === Constantes::ROLE_ADMIN_SUPERIEUR || $role === Constantes::ROLE_ADMIN_SECONDAIRE || $role === Constantes::ROLE_ADMIN_PRIMAIRE
        || $role === Constantes::ROLE_DIRECTEUR_SUPERIEUR || $role === Constantes::ROLE_DIRECTEUR_SECONDAIRE || $role === Constantes::ROLE_DIRECTEUR_PRIMAIRE) {
            $transports = $this->transportRepository->getNombreTransport($user->getStructure()->getId(),$day);
        }else {
        // return new Response('ok');
        $transports = $this->transportRepository->getNombreTransportByGerant($user->getStructure()->getId(),$day,$user->getId());
        }

        if($transports){
            $nombre = intval($transports[0]['nombre']);
            $response = new Response($nombre);
            return $response;
        }
        return new Response($response);
    }

    public function montantTransport(): Response
    {
        $user = $this->getUser();
        $response = 0;
        $roles = $user->getRoles();
        $role = $roles[0];
        $day = (new DateTime())->setTime(0,0);
        // return new Response('ok');
        $transports = $this->transportRepository->getMontantTransportByGerant($user->getStructure()->getId(),$day,$user->getId());
        if($transports){
            $nombre = intval($transports[0]['nombre']);
            $response = new Response($nombre);
            return $response;
        }
        return new Response($response);
    }
}
