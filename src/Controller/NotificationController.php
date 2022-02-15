<?php

namespace App\Controller;
use App\Constante\Constantes;
use App\Entity\Notification;
use App\Form\EditNotificationType;
use App\Form\NotificationType;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class NotificationController extends AbstractController
{
    private $notificationRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $notificationManager;

    

    public function __construct(NotificationRepository $notificationRepository, EntityManagerInterface $entityManager)
    {
        $this->notificationRepository = $notificationRepository;
        $this->entityManager = $entityManager;
       
    }


     /**
     * @Route("/notification", name="list_notification")
     */

    public function notification(): Response
    {
        $user = $this->getUser();
        $notifications = $this->notificationRepository->getNotificationBySurveillants($user->getStructure()->getId());
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if(!$notifications){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('notification/notification.html.twig', [
            'notifications' => $notifications,'resultat' => $resultat,'classe' => $classe
        ]);
    }
     /**
     * @Route("/notification/new", name="create_notification")
     */
    public function createNotification(Request $request): Response
    {
        $users = $this->getUser();
        $notification = new notification();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(NotificationType::class, $notification);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $notification->setStatut(1);
            $notification->setUser($users);
            $this->entityManager->persist($notification);
            $this->entityManager->flush();
            if ($notification) {
                return $this->redirectToRoute('list_notification');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('notification/notification-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('notification/notification-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }
      /**
     * @Route("/notification/edit/{id}", name="edit_notification")
     */
    public function editNotification(Request $request, Notification $notification): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(NotificationType::class,$notification);
       
        if($role !== Constantes::ROLE_SURVEILLANT_SECONDAIRE && $role !== Constantes::ROLE_SURVEILLANT_PRIMAIRE){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('notification/notification-form.html.twig', [
                'action' => 'edit',
                'notification' => $notification,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($notification);
            $this->entityManager->flush();
            if ($notification) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_SURVEILLANT_SECONDAIRE || $primaryRole === Constantes::ROLE_SURVEILLANT_PRIMAIRE){
                    return $this->redirectToRoute('list_notification');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('notification/notification-form.html.twig', [
                    'action' => 'edit',
                    'notification' => $notification,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('notification/notification-form.html.twig', [
            'action' => 'edit',
            'notification' => $notification,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }



    /**
     * @Route("/desactivernotification/{id}", name="desabled_notification")
     */

    public function desactiverNotification(Request $request, notification $notification_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_SURVEILLANT_SECONDAIRE && $role !== Constantes::ROLE_SURVEILLANT_PRIMAIRE){            
                return $this->render('notification/notification.html.twig', [
                    'notification' => null,
                ]);
            }
            $notification_entity->setStatut(0);
            $entityManager->persist($notification_entity);
            $entityManager->flush();
            $notifications = $this->notificationRepository->getNotifications();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_notification');
            } else {
                return $this->render('notification/notification.html.twig', [
                    'notification' => $notifications,
                ]);
            }
        
            return $this->render('notification/notification.html.twig', [
                'notifications' => $notifications,
            ]);
    }
  /**
     * @Route("/activernotification/{id}", name="enable_notification")
     */

    public function activerNotification(Request $request, notification $notification_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_SURVEILLANT_SECONDAIRE && $role !== Constantes::ROLE_SURVEILLANT_PRIMAIRE){
                return $this->render('notification/notification.html.twig', [
                    'notification' => null,
                ]);
            }
            $notification_entity->setStatut(1);
            $entityManager->persist($notification_entity);
            $entityManager->flush();
            $notifications = $this->notificationRepository->getNotifications();

            
            if ($entityManager) {
                return $this->redirectToRoute('list_notification');
            } else {
                return $this->render('notification/notification.html.twig', [
                    'notifications' => $notifications,
                ]);
            }
        
            return $this->render('notification/notifications.html.twig', [
                'notifications' => $notifications,
            ]);
    }
    
}
