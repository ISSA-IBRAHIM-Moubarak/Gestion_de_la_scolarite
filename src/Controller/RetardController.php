<?php

namespace App\Controller;
use DateTime;
use App\Entity\User;
use App\Entity\Retard;
use App\Form\RetardType;
use App\Form\EditRetardType;
use App\Constante\Constantes;
use App\Repository\RetardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RetardController extends AbstractController

{
    private $retardRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $retardManager;

    

    public function __construct(RetardRepository $retardRepository, EntityManagerInterface $entityManager)
    {
        $this->retardRepository = $retardRepository;
        $this->entityManager = $entityManager;
       
    }


     /**
     * @Route("/retard", name="list_retard")
     */

    public function Retard(): Response
    {
        $user = $this->getUser();
        $retards = $this->retardRepository->getRetardBySurveillants($user->getStructure()->getId());
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];

        if(!$retards){
            $resultat = "";
            $classe = "";                
        }
        return $this->render('retard/retard.html.twig', [
            'retards' => $retards,'resultat' => $resultat,'classe' => $classe
        ]);
    }
     /**
     * @Route("/retard/new", name="create_retard")
     */
    public function createRetard(Request $request, \Swift_Mailer $mailer): Response
    {
        $users = $this->getUser();
        $retard = new Retard();
        $resultat = "";
        $classe = "";
        
        $roles = $users->getRoles();
        $role = $roles[0];
        $infosSurveillant = "moubarak vient d'accumuler 3 retards !";

        
        if ($role === Constantes::ROLE_SURVEILLANT_SUPERIEUR) {
            $form = $this->createForm(RetardType::class,$retard);
        }else {
            $form = $this->createForm(EditRetardType::class,$retard);
        }
        $dateDay = date('d-m-Y');
        $dateTime = date('H:i:s');
        $dateJourTime = \DateTime::createFromFormat('H:i:s', $dateTime);
        $dateJourDay = \DateTime::createFromFormat('d-m-Y', $dateDay);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $retard->setDateRetard($dateJourDay);
            $retard->setHeureRetard($dateJourTime);
            $retard->setStatut(1);
            //cela me permet de lier l'utilisateur en question avec sa structure
            $retard->setUser($users);
            $this->entityManager->persist($retard);
            $this->entityManager->flush();

             // ici je recupere le motif, le nom de l'apprenant et j'envoie le message du surveillant
             $motif = $retard->getMotifRetard();
             $heureDebut = $retard->getHeureRetard()->format('H:i:s');
             $dateDebut = $retard->getDateRetard()->format('d-m-Y');
             $monApprenant = $retard->getApprenant()->getNomApprenant();
             if ($role === Constantes::ROLE_SURVEILLANT_SUPERIEUR) {
                $moduleRetard = $retard->getModule()->getIntituleModule();
                $infosSurveillant = "$monApprenant vient de rater le cours de $moduleRetard à $heureDebut pour le motif : $motif !!!
                ";
            }else {
                $matiereRetard = $retard->getMatiere()->getIntituleMatiere();
                $infosSurveillant = "$monApprenant vient de rater le cours de $matiereRetard à $heureDebut pour le motif : $motif !!!";
            }
            
             // ici je recupere l'email de l'apprenant selectionné
             // return new Response($absence->getApprenant()->getEmail());
             $email = $retard->getApprenant()->getEmail();
            if ($retard) {
                $to = array(
                    "$email" => "$monApprenant",
                );
                $message = (new \Swift_Message('message'))
                    ->setFrom($users->getEmail())
                    ->setTo($to)
                    ->setBody($infosSurveillant);
                    $mailer->send($message);
                return $this->redirectToRoute('list_retard');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('retard/retard-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('retard/retard-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }
    /**
     * @Route("/retard/edit/{id}", name="edit_retard")
    */
    public function editRetard(Request $request, Retard $retard): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];

        
        if ($role === Constantes::ROLE_SURVEILLANT_SUPERIEUR) {
            $form = $this->createForm(RetardType::class,$retard);
        //return new Response($role);

        }else {
        //return new Response($role);
            $form = $this->createForm(EditRetardType::class,$retard);
        }
       
        if($role !== Constantes::ROLE_SURVEILLANT_SUPERIEUR && $role !== Constantes::ROLE_SURVEILLANT_SECONDAIRE && $role !== Constantes::ROLE_SURVEILLANT_PRIMAIRE ){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('retard/retard-form.html.twig', [
                'action' => 'edit',
                'retard' => $retard,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($retard);
            $this->entityManager->flush();
            if ($retard) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if($primaryRole === Constantes::ROLE_SURVEILLANT_SUPERIEUR || $primaryRole === Constantes::ROLE_SURVEILLANT_SECONDAIRE || $primaryRole === Constantes::ROLE_SURVEILLANT_PRIMAIRE){
                    return $this->redirectToRoute('list_retard');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('retard/retard-form.html.twig', [
                    'action' => 'edit',
                    'retard' => $retard,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('retard/retard-form.html.twig', [
            'action' => 'edit',
            'retard' => $retard,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }



    /**
     * @Route("/desactiverRetard/{id}", name="desabled_retard")
     */

    public function desactiverRetarde(Request $request, Retard $retard_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_SURVEILLANT_SUPERIEUR && $role !== Constantes::ROLE_SURVEILLANT_SECONDAIRE && $role !== Constantes::ROLE_SURVEILLANT_PRIMAIRE ){            
                return $this->render('retard/retard.html.twig', [
                    'retard' => null,
                ]);
            }
            $retard_entity->setStatut(0);
            $entityManager->persist($retard_entity);
            $entityManager->flush();
            if ($entityManager) {
                return $this->redirectToRoute('list_retard');
            } else {
                return $this->render('retard/retard.html.twig', [
                    'retards' => $retard_entity,
                ]);
            }
        
            return $this->render('retard/retard.html.twig', [
                'retards' => $retard_entity,
            ]);
    }
  /**
     * @Route("/activerRetard/{id}", name="enable_retard")
     */

    public function activerRetarde(Request $request, Retard $retard_entity): Response
    
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
        if($role !== Constantes::ROLE_SURVEILLANT_SUPERIEUR && $role !== Constantes::ROLE_SURVEILLANT_SECONDAIRE && $role !== Constantes::ROLE_SURVEILLANT_PRIMAIRE ){            
                return $this->render('retard/retard.html.twig', [
                    'retard' => null,
                ]);
            }
            $retard_entity->setStatut(1);
            $entityManager->persist($retard_entity);
            $entityManager->flush();
            if ($entityManager) {
                return $this->redirectToRoute('list_retard');
            } else {
                return $this->render('retard/retard.html.twig', [
                    'retards' => $retard_entity,
                ]);
            }
        
            return $this->render('retard/retard.html.twig', [
                'retards' => $retard_entity,
            ]);
    }

    //Cette methode me permet de compter le nombre retards par apprenant

    public function nombreRetard(): Response
    {
        $user = $this->getUser();
        $response = 0;
        $roles = $user->getRoles();
        $role = $roles[0];
        $retards = $this->retardRepository->getNombreRetard($user->getStructure()->getId());
        if($retards){
            $nombre = intval($retards[0]['nombre']);
            $response = new Response($nombre);
            return $response;
        }
        return new Response($response);
    }
}

