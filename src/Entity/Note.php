<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 */
class Note
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

   
    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

    /**
     * @ORM\ManyToOne(targetEntity=Inscription::class, inversedBy="notes")
     */
    private $inscription;

     /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="notes")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=ModuleSemestre::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=true)
     */
    private $modulesemestre;

     /**
     * @ORM\ManyToOne(targetEntity=MatiereSemestre::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=true)
     */
    private $matieresemestre;

    /**
     * @ORM\ManyToOne(targetEntity=Tevaluation::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tevaluation;

    /**
     * @ORM\Column(type="float")
     */
    private $noteApprenant;

    /**
     * @ORM\ManyToOne(targetEntity=Salle::class, inversedBy="notes")
     */
    private $salle;

 
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNoteApprenant(): ?int
    {
        return $this->noteApprenant;
    }

    public function setNoteApprenant(int $noteApprenant): self
    {
        $this->noteApprenant = $noteApprenant;

        return $this;
    }

    public function getStatut(): ?int
    {
        return $this->statut;
    }

    public function setStatut(int $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getInscription(): ?Inscription
    {
        return $this->inscription;
    }

    public function setInscription(?Inscription $inscription): self
    {
        $this->inscription = $inscription;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getModulesemestre(): ?ModuleSemestre
    {
        return $this->modulesemestre;
    }

    public function setModulesemestre(?ModuleSemestre $modulesemestre): self
    {
        $this->modulesemestre = $modulesemestre;

        return $this;
    }

    public function getMatieresemestre(): ?MatiereSemestre
    {
        return $this->matieresemestre;
    }

    public function setMatieresemestre(?MatiereSemestre $matieresemestre): self
    {
        $this->matieresemestre = $matieresemestre;

        return $this;
    }

    public function getTevaluation(): ?Tevaluation
    {
        return $this->tevaluation;
    }

    public function setTevaluation(?Tevaluation $tevaluation): self
    {
        $this->tevaluation = $tevaluation;

        return $this;
    }

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): self
    {
        $this->salle = $salle;

        return $this;
    }


}
