<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Form\UserType;
use App\Form\EditUserType;
use App\Constante\Constantes;
use App\Form\EditPictureType;
use App\Form\EditPasswordType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPasswordValidator;

class UserController extends AbstractController
{
    private $userRepository;
    private $entityManager;
    private $passwordEncoder;
    private $tokenManager;
    private $formFactory;
    private $userManager;
    private $userPasswordValidator;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager,UserManagerInterface $userManager, UserPasswordEncoderInterface $passwordEncoder,CsrfTokenManagerInterface $tokenManager = null)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenManager = $tokenManager;
        $this->userManager = $userManager;
        
    }

    /**
     * @Route("/user", name="list_user")
     */
    public function user(): Response
    {
        $user = $this->getUser();
        $users = $this->userRepository->getUsersByAdmin($user->getStructure()->getId());
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if($role !== Constantes:: ROLE_ADMIN_PRIMAIRE && $role !== Constantes:: ROLE_ADMIN_SECONDAIRE && $role !== Constantes:: ROLE_ADMIN_SUPERIEUR ){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('user/user.html.twig', [
                'users' => null,'resultat' => $resultat,'classe' => $classe
            ]);
        }
        if(!$users){
            $resultat = "";
            $classe = "";
                
        }
        return $this->render('user/user.html.twig', [
            'users' => $users,'resultat' => $resultat,'classe' => $classe
        ]);
    }

    /**
     * @Route("/user/new", name="create_user")
     */
    public function createUser(Request $request): Response
    {
        $users = $this->getUser();
        $user = new User();
        $resultat = "";
        $classe = "";
               
        $form = $this->createForm(UserType::class, $user);
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $roleSelection = $data['roles'];
            if ($role === Constantes::ROLE_ADMIN_PRIMAIRE) {
                $profil ='_'.'PRIMAIRE';
                $roles = ['ROLE_USER', $roleSelection[0].$profil];
                # return new Response($roles);
            }elseif ($role === Constantes::ROLE_ADMIN_SECONDAIRE ) {
                $profil ='_'.'SECONDAIRE';
                $roles = ['ROLE_USER', $roleSelection[0].$profil];
                # return new Response($roles);
            }elseif ($role === Constantes::ROLE_ADMIN_SUPERIEUR) {
                $profil ='_'.'SUPERIEUR';
                $roles = ['ROLE_USER', $roleSelection[0].$profil];
                # return new Response($roles);
            }else{
                $roles = ['ROLE_USER', $roleSelection[0]];
            }
            $user->setPassword($this->passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData()));
            $user->setRoles($roles);
            $user->setEnabled(true);
            $user->setStructure($users->getStructure());
          
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            if ($user) {
                return $this->redirectToRoute('list_user');
            } else {
                $resultat = "Echec de la creation!.";
                $classe = "alert alert-danger";
                return $this->render('user/user-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('user/user-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

    /**
     * @Route("/user/edit/{id}", name="edit_user")
     */
    public function editUser(Request $request, User $user): Response
    {
        /**
         * $users est l'utilisateur connecté et $roles son role
         * par exemple ici on n'a l'utilisateur=Administrateur(Superieur,Secondaire ou Primaire)
         */
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(EditUserType::class,$user);
       
        if($role !== Constantes:: ROLE_ADMIN_PRIMAIRE && $role !== Constantes:: ROLE_ADMIN_SECONDAIRE && $role !== Constantes:: ROLE_ADMIN_SUPERIEUR){
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('user/user-form.html.twig', [
                'action' => 'edit',
                'user' => $user,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            if ($user) {
                //pour recuperer l'utilisateur connecter
                //return new Response($role);
                if($role === Constantes::ROLE_ADMIN_SUPERIEUR || $role === Constantes::ROLE_ADMIN_SECONDAIRE || $role === Constantes::ROLE_ADMIN_PRIMAIRE){
                    return $this->redirectToRoute('list_user');
                }
                
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";
               
                return $this->render('user/user-form-edit.html.twig', [
                    'action' => 'edit',
                    'user' => $user,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('user/user-form-edit.html.twig', [
            'action' => 'edit',
            'user' => $user,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }
    
    /**
     * @Route("/desactiver/{id}", name="desabled_user")
     */
    public function desactiverUser(Request $request, User $user_entity): Response
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
            if($role !== Constantes:: ROLE_ADMIN_PRIMAIRE && $role !== Constantes:: ROLE_ADMIN_SECONDAIRE && $role !== Constantes:: ROLE_ADMIN_SUPERIEUR){
            
                return $this->render('user/user.html.twig', [
                    'users' => null,
                ]);
            }
            $user_entity->setEnabled(0);
            $entityManager->persist($user_entity);
            $entityManager->flush();
            $users = $this->userRepository->getUsers();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_user');
            } else {
                return $this->render('user/user.html.twig', [
                    'users' => $users,
                ]);
            }
        
            return $this->render('user/user.html.twig', [
                'users' => $users,
            ]);
    }

    /**
     * @Route("/activer/{id}", name="enable_user")
     */
    public function activerUser(Request $request, User $user_entity): Response
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];    
        $entityManager = $this->getDoctrine()->getManager();
            if($role !== Constantes:: ROLE_ADMIN_PRIMAIRE && $role !== Constantes:: ROLE_ADMIN_SECONDAIRE && $role !== Constantes:: ROLE_ADMIN_SUPERIEUR){
            
                return $this->render('user/user.html.twig', [
                    'users' => null,
                ]);
            }
            $user_entity->setEnabled(1);
            $entityManager->persist($user_entity);
            $entityManager->flush();
            $users = $this->userRepository->getUsers();
            
            if ($entityManager) {
                return $this->redirectToRoute('list_user');
            } else {
                return $this->render('user/user.html.twig', [
                    'users' => $users,
                ]);
            }
        
            return $this->render('user/user.html.twig', [
                'users' => $users,
            ]);
    }

    //TODOO permettre apres de tenir compte du fos_user
    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request) {
        //throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
         $request->getSession()->invalidate();
         //return $this->redirectToRoute('login');
    }

    /**
     * @Route("/user/editpassword", name="edit_password")
     */
    public function editPassword(Request $request,UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        if(!$user){
           //     
        }
        $form = $this->createForm(EditPasswordType::class, null);
        $resultat = "";
        $classe = "";
            
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $user_entity = $userRepository->findOneBy(['id'=>$user->getId()]);
            $password = $data['ancienpassword'];
            
            $motdepasse=  $this->passwordEncoder->isPasswordValid($user_entity, $password);
            
            if($motdepasse){
               
                $motdepassarray = $data['plainPassword'];
                $motdepass = $motdepassarray['first'];    
                $user_entity->setPlainPassword($motdepass);
               $this->userManager->updateUser($user_entity);
              
            }
            
            if ($user) {
                return $this->redirectToRoute('list_user');
            } else {
                $resultat = "Echec de la modification.";
                $classe = "alert alert-danger";
            
                return $this->render('user/password-form-edit.html.twig', [
                    'action' => 'edit',
                    'user' => $user,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('user/password-form-edit.html.twig', [
            'action' => 'edit',
            'user' => $user,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request)
    {
        $session = $request->getSession();

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);
        $message = "Les informations d'indentification sont invalides";
        $csrfToken = $this->tokenManager
            ? $this->tokenManager->getToken('authenticate')->getValue()
            : null;

        return $this->render('login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,
            'message' => $message
        ]);
    }

    

   


    /**
     * @Route("/user/resetpassword", name="reset_password")
     */
    public function resetPassword(Request $request,UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
       
        if(!$user){
           //     
        }
        $form = $this->createForm(ResetPasswordType::class, null);
        $resultat = "";
        $classe = "";
        if($role !== Constantes:: ROLE_ADMIN_PRIMAIRE && $role !== Constantes:: ROLE_ADMIN_SECONDAIRE && $role !== Constantes:: ROLE_ADMIN_SUPERIEUR){
            $resultat = "Vous n'avez pas l'autorisation sur cette page.";
            $classe = "alert alert-danger";
            return $this->render('user/password-form-reset.html.twig', [
                'action' => 'edit',
                'user' => $user,
                'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
            ]);
        }
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $username = $data['username'];
            $user_entity = $this->userManager->findUserByUsernameOrEmail($username);
            //return new Response($user_entity->getUsername());
            if(!$user_entity){
                $resultat = "Cet utilisateur n'a pas de compte.";
                $classe = "alert alert-danger";
            
                return $this->render('user/password-form-reset.html.twig', [
                    'action' => 'edit',
                    'user' => $user,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
                $motdepassarray = $data['plainPassword'];
                $motdepass = $motdepassarray['first'];    
                $user_entity->setPlainPassword($motdepass);
                //$userManager = $this->get('fos_user.resetting.form.factory');
               // $userManager->updateUser($user_entity);
                $this->userManager->updateUser($user_entity);
               // $user->setPassword($this->passwordEncoder->encodePassword($user, $request->get('plainPassword')));
               // $this->userManager->flush();
            
            
            if ($this->userManager) {
                $resultat = "Réinitialisation réussi, voici le mot de passe à communiquer: $motdepass .";
                $classe = "alert alert-info";
            
                return $this->render('user/password-form-reset.html.twig', [
                    'action' => 'edit',
                    'user' => $user,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            } else {
                $resultat = "Echec de la réinitialisation.";
                $classe = "alert alert-danger";
            
                return $this->render('user/password-form-reset.html.twig', [
                    'action' => 'edit',
                    'user' => $user,
                    'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
                ]);
            }
        }
        return $this->render('user/password-form-reset.html.twig', [
            'action' => 'edit',
            'user' => $user,
            'form' => $form->createView(),'resultat' => $resultat,'classe' => $classe
        ]);
    }

    /**
     * @Route("/photo_profile/edit", name="edit_picture")
     */
    public function editPicture(Request $request): Response
    {
        /**
         * $users est l'utilisateur connecté et $roles son role
         * par exemple ici on n'a l'utilisateur=Administrateur(Superieur,Secondaire ou Primaire)
         */
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];
          
        $form = $this->createForm(EditPictureType::class,$users);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            //pour le logo de l'utilisateur
            $users->upload();
            $this->entityManager->persist($users);
            $this->entityManager->flush();
            if ($users) {
                //pour recuperer l'utilisateur connecter
                //return new Response($role);
                //return $this->redirectToRoute('list_user');
            }
        }
        return $this->render('user/picture-form-edit.html.twig', [
            'action' => 'edit',
            'users' => $users,
            'form' => $form->createView()]);
    }
    
}
