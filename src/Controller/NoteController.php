<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Salle;
use App\Entity\Niveau;
use App\Form\NoteType;
use App\Entity\Matiere;
use App\Form\EditNoteType;
use App\Entity\Inscription;
use App\Form\ModuleNoteType;
use PhpParser\Node\Stmt\Nop;
use App\Constante\Constantes;
use App\Form\ChoixNiveauType;
use App\Entity\ModuleSemestre;
use App\Repository\NoteRepository;
use App\Repository\SalleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InscriptionRepository;
use App\Repository\TevaluationRepository;
use App\Repository\ModuleSemestreRepository;
use App\Repository\ApprenantClasseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NoteController extends AbstractController
{
    private $noteRepository;
    private $inscriptionRepository;
    private $entityManager;
    private $tokenManager;
    private $formFactory;
    private $noteManager;
    private $inscriptionManager;



    public function __construct(NoteRepository $noteRepository, InscriptionRepository $inscriptionRepository, EntityManagerInterface $entityManager, ModuleSemestreRepository $moduleSemestreRepository, TevaluationRepository $tevaluationRepository, ApprenantClasseRepository $apprenantClasseRepository, SalleRepository $salleRepository)
    {
        $this->noteRepository = $noteRepository;
        $this->inscriptionRepository = $inscriptionRepository;
        $this->entityManager = $entityManager;
        $this->moduleSemestreRepository = $moduleSemestreRepository;
        $this->tevaluationRepository = $tevaluationRepository;
        $this->apprenantClasseRepository = $apprenantClasseRepository;
        $this->salleRepository = $salleRepository;
        
    }

    /**
     * @Route("/note", name="list_note")
     */
    public function note(): Response
    {
        $user = $this->getUser();
        $notes = $this->noteRepository->getNotes();
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if (!$notes) {
            $resultat = "";
            $classe = "";
        }
        return $this->render('note/index.html.twig', [
            'notes' => $notes, 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * Botton detail sur note
     * @Route("/note/{id}", name="detail_note")
     */
    public function detailNote(Note $id): Response
    {
        $user = $this->getUser();
        $notes = $this->noteRepository->findOneById($id);

        # return new Response($inscriptions->getId());
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        if (!$notes) {
            $resultat = "";
            $classe = "";
        }
        return $this->render('note/detailnote.html.twig', [
            'notes' => $notes, 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * @Route("/note/new/{id}/{moduleSemestre}", name="create_note")
     */
    public function createNote(Request $request, Inscription $inscriptions, ModuleSemestre $moduleSemestre): Response
    {
        
        $users = $this->getUser();
        $note = new Note();

        $resultat = "";
        $classe = "";
        $notes = $this->noteRepository->getNotes();
        $roles = $users->getRoles();
        $role = $roles[0];
       // return new Response('ok');
        if ($role === Constantes::ROLE_ENSEIGNANT_SUPERIEUR) {
            $form = $this->createForm(ModuleNoteType::class, $note);
        } else {
            $form = $this->createForm(NoteType::class, $note);
        }
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            
            $inscription = $this->inscriptionRepository->findOneByApprenant($request->get('id'));
           // return new Response($inscription->getId());
            if(!$inscription) {
                $resultat = "Echec de l'insertion !";
                $classe = "alert alert-danger";
                return $this->render('note/note-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(), 'moduleSemestre' => $request->get('moduleSemestre'), 'resultat' => $resultat, 'classe' => $classe
                ]);
            }
            
            $modulesemestre = $this->moduleSemestreRepository->findOneById($request->get('moduleSemestre'));
            $tevaluation = $data['tevaluation'];
            $tevaluation_entity = $this->tevaluationRepository->findOneById($tevaluation);
            $apprenantClasse = $this->apprenantClasseRepository->findOneByInscription($inscription);
            $noteApprenant = $data['noteApprenant'];
            
            $note->setNoteApprenant($noteApprenant);
            $note->setStatut(1);
            $note->setInscription($inscription);
            $note->setSalle($apprenantClasse->getSalle());
            $note->setUser($users);
            $note->setModulesemestre($modulesemestre);
            $note->setTevaluation($tevaluation_entity);
            $this->entityManager->persist($note);
            $this->entityManager->flush();
            if ($note || $notes) {
                return $this->redirectToRoute('list_note');
            } else {
                $resultat = "Echec de l'insertion!.";
                $classe = "alert alert-danger";
                return $this->render('note/note-form.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(), 'moduleSemestre' => $request->get('moduleSemestre'), 'resultat' => $resultat, 'classe' => $classe
                ]);
            }
        }

        return $this->render('note/note-form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(), 'moduleSemestre' => $request->get('moduleSemestre'), 'resultat' => $resultat, 'classe' => $classe
        ]);
        return $this->render('note/note-form.html.twig', [
            'notes' => $notes, 'moduleSemestre' => $request->get('moduleSemestre'), 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * @Route("/modulesemestre/new/{id}", name="choix_niveau")
     */
    public function createNiveau(Request $request): Response
    {
        $users = $this->getUser();

        $resultat = "";
        $classe = "";
        $form = $this->createForm(ChoixNiveauType::class, null, array('user' => $users));
        $roles = $users->getRoles();
        $role = $roles[0];

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $request->request->get($form->getName());
            $salle = $data['salle'];
            $apprenantClasse = $this->apprenantClasseRepository->getInscritsByClasse($salle);
           // return new Response($inscriptions[0]->getId());          
            
            if ($apprenantClasse) {
                //return $this->redirectToRoute('create_note');
                return $this->render('note/list_apprenant-niveau.html.twig', [
                    'apprenantClasse' => $apprenantClasse, 'id' => $request->get('id'), 'resultat' => $resultat, 'classe' => $classe
                ]);
            } else {
                $resultat = "Echec de l'insertion !";
                $classe = "alert alert-danger";
                return $this->render('module_semestre/choixniveau.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(), 'id' => $request->get('id'), 'resultat' => $resultat, 'classe' => $classe
                ]);
            }
        }
        return $this->render('module_semestre/choixniveau.html.twig', [
            'action' => 'create',
            'form' => $form->createView(), 'id' => $request->get('id'), 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * @Route("/note/edit/{id}", name="edit_note")
     */
    public function editNote(Request $request, Note $note): Response
    {
        $users = $this->getUser();
        $roles = $users->getRoles();
        $role = $roles[0];

        $form = $this->createForm(EditNoteType::class, $note);

        if ($role !== Constantes::ROLE_ENSEIGNANT_SUPERIEUR && $role !== Constantes::ROLE_ENSEIGNANT_SECONDAIRE && $role !== Constantes::ROLE_ENSEIGNANT_PRIMAIRE) {
            $resultat = "Vous n'avez pas l'autorisation d'accéder à cette page.";
            $classe = "alert alert-danger";
            return $this->render('note/note-form-edit.html.twig', [
                'action' => 'edit',
                'note' => $note,
                'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
            ]);
        }
        $resultat = "";
        $classe = "";

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->entityManager->persist($note);
            $this->entityManager->flush();
            if ($note) {
                $roles = $users->getRoles();
                $primaryRole = $roles[0];
                if ($primaryRole === Constantes::ROLE_ENSEIGNANT_SUPERIEUR || $primaryRole !== Constantes::ROLE_ENSEIGNANT_SECONDAIRE || $primaryRole !== Constantes::ROLE_ENSEIGNANT_PRIMAIRE) {
                    return $this->redirectToRoute('list_note');
                }
            } else {
                $resultat = "Echec de la modification!.";
                $classe = "alert alert-danger";

                return $this->render('note/note-form-edit.html.twig', [
                    'action' => 'edit',
                    'note' => $note,
                    'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
                ]);
            }
        }
        return $this->render('note/note-form-edit.html.twig', [
            'action' => 'edit',
            'note' => $note,
            'form' => $form->createView(), 'resultat' => $resultat, 'classe' => $classe
        ]);
    }

    /**
     * @Route("/desactivernote/{id}", name="desabled_note")
     */

    public function desactiverNote(Request $request, note $note_entity): Response

    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
        $entityManager = $this->getDoctrine()->getManager();
        if ($role !== Constantes::ROLE_ENSEIGNANT_SUPERIEUR && $role !== Constantes::ROLE_ENSEIGNANT_SECONDAIRE && $role !== Constantes::ROLE_ENSEIGNANT_PRIMAIRE) {

            return $this->render('note/note.html.twig', [
                'note' => null,
            ]);
        }
        $note_entity->setStatut(0);
        $entityManager->persist($note_entity);
        $entityManager->flush();
        $notes = $this->noteRepository->getNotes();

        if ($entityManager) {
            return $this->redirectToRoute('list_note');
        } else {
            return $this->render('note/note.html.twig', [
                'notes' => $notes,
            ]);
        }

        return $this->render('note/note.html.twig', [
            'notes' => $notes,
        ]);
    }

    /**
     * @Route("/activernote/{id}", name="enable_note")
     */

    public function activerNote(Request $request, note $note_entity): Response

    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
        $entityManager = $this->getDoctrine()->getManager();
        if ($role !== Constantes::ROLE_ENSEIGNANT_SUPERIEUR && $role !== Constantes::ROLE_ENSEIGNANT_SECONDAIRE && $role !== Constantes::ROLE_ENSEIGNANT_PRIMAIRE) {
            return $this->render('note/note.html.twig', [
                'note' => null,
            ]);
        }
        $note_entity->setStatut(1);
        $entityManager->persist($note_entity);
        $entityManager->flush();
        $notes = $this->noteRepository->getNotes();

        if ($entityManager) {
            return $this->redirectToRoute('list_note');
        } else {
            return $this->render('note/note.html.twig', [
                'notes' => $notes,
            ]);
        }

        return $this->render('note/note.html.twig', [
            'notes' => $notes,
        ]);
    }

     /**
     * @Route("/bulletinbyapprenant/new", name="choix_apprenant")
     */
    public function bulletinbyapprenant(Request $request): Response 
    {
        $users = $this->getUser();
        $resultat = "";
        $classe = ""; 
        $bulletinbyApprenant = "";
        $form = $this->createForm(ChoixNiveauType::class, null, array('user' => $users));
        $roles = $users->getRoles();
        $role = $roles[0];
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
           // return new Response('ok');
            $data = $request->request->get($form->getName());
            $salle = $data['salle'];
            // return new Response($inscriptions[0]->getId());          
            $bulletinbyApprenant = $this->apprenantClasseRepository->getInscritsByClasse($salle);
            
            if ($bulletinbyApprenant) {
                //return $this->redirectToRoute('create_note');
                return $this->render('note/list_apprenant_bulletin.html.twig', [
                    'bulletinbyApprenant' => $bulletinbyApprenant, 'id' => $request->get('id'), 'resultat' => $resultat, 'classe' => $classe
                ]);
            } else {
                $resultat = "Echec de l'insertion !";
                $classe = "alert alert-danger";
                return $this->render('inscription/inscription.html.twig', [
                    'action' => 'create',
                    'form' => $form->createView(), 'id' => $request->get('id'), 'resultat' => $resultat, 'classe' => $classe
                ]);
            }
        }
       // return new Response('mal fait');
        return $this->render('note/apprenant_bulletin.html.twig', [
            'action' => 'create', 'bulletinbyApprenant' => $bulletinbyApprenant,
            'form' => $form->createView(), 'id' => $request->get('id'), 'resultat' => $resultat, 'classe' => $classe
        ]);
    }
    
    /**
     * Botton bulletin
     * @Route("/note/bulletin/{id}", name="bulletin")
     */
    public function bulletin(Inscription $id,Request $request): Response
    {
        $user = $this->getUser();
        $inscription = $this->inscriptionRepository->findOneById($id);
       // return new Response($inscription->getId());
        $resultat = "";
        $classe = "";
        $roles = $user->getRoles();
        $role = $roles[0];
        $apprenant = "";
        $totalMoyenneEtudiant = 0;
        $moyenneE = 0;
        $salle = "";
        $total = 0;
        $tabnoteClasse = array();
        $totalMoyenneClasse = 0;
        $totalsommeMoyenneClasse = 0;
        $moyenneEtu = 0;
        

       // $apprenantClasse = "";
        //$apprenantClasseEffectif = "";

        $notes =  $this->noteRepository->getbulletin( $user->getStructure()->getId(),$inscription->getApprenant()->getId());
        $bulletin = array();
        //return new Response(var_dump($notes));
        if($notes){  

            foreach ($notes as $value) {  

                $apprenantClasse =  $this->noteRepository->getMoyenneClasseByModule( $user->getStructure()->getId(),$value['idSalle'], $value['idModule']);
                $apprenantClasseEffectif =  $this->apprenantClasseRepository->getEffectict( $user->getStructure()->getId(),$value['idSalle']);
                $total += $value['coeficient'];

                $moyenneE = intval(($value['note']))*$value['coeficient'] ;
                $moyenneEtudiant = intval($moyenneE/2) ;
                $totalMoyenneEtudiant += $moyenneEtudiant; 
                $noteClasse = $apprenantClasse[0]['noteClasse']/2;
                //return new Response($noteClasse.''.$moyenneEtudiant);
                $totalMoyenneClasse = $noteClasse;       
                // $moyenneClasse = ($totalMoyenneEtudiant/$apprenantClasse[0]['effectif']);
                // $moyenneEtudiant = ( $value['note'])/2 ;
                $moyenneClasse = (($totalMoyenneClasse)/$apprenantClasseEffectif[0]['effectif'])*$value['coeficient'];
                $sommeMoyenneClasse = $moyenneClasse/$apprenantClasseEffectif[0]['effectif'];
                $totalsommeMoyenneClasse += $sommeMoyenneClasse;
              // return new Response($apprenantClasseEffectif[0]['effectif']);

                $moyenneEtu = $totalMoyenneEtudiant/$total; 
                
                 
                    $bulletin[] = array(
                    'NomApprenant' => $value['NomApprenant'],
                    'PrenomApprenant' => $value['PrenomApprenant'],
                    'DateNaissance' => $value['DateNaissance'],
                    'LieuNaissance' => $value['LieuNaissance'],
                    'intituleFiliere' => $value['intituleFiliere'],
                    'intituleModule' => $value['intituleModule'],
                    'typeEvaluation' => $value['typeEvaluation'],
                    'note' => round($moyenneEtudiant,2),
                    'coeficient' => $value['coeficient'],
                    'moyenneEtudiant' => round($moyenneEtudiant,2),
                    'moyenneClasse' => round($moyenneClasse,2),
                    'effectif' => $apprenantClasseEffectif[0]['effectif']
                );
            } 
            //return new Response (var_dump($bulletin[0]));
             return $this->render('note/bulletin-note.html.twig', [
                'totalsommeMoyenneClasse' => round($totalsommeMoyenneClasse,2),
                'moyenneEtu' => round($moyenneEtu,2),
                 'notes' => $bulletin, 'resultat' => $resultat, 'classe' => $classe,'total' => $total,'totalMoyenneEtudiant' => $totalMoyenneEtudiant,
                ]);
                
            } else{
                //return new Response('okl');
                return $this->render('note/bulletin-note.html.twig', [
                    'totalsommeMoyenneClasse' => round($totalsommeMoyenneClasse,2),
                    'moyenneEtu' => round($moyenneEtu,2),
                    'notes' => $bulletin, 'resultat' => $resultat, 'classe' => $classe,'total' => $total,'totalMoyenneEtudiant' => $totalMoyenneEtudiant,
            ]);
            
        }           
        
}
}
