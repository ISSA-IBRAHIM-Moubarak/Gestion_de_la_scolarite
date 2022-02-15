<?php

namespace App\Entity;

use App\Repository\AbsenceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AbsenceRepository::class)
 */
class Absence
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $motif;

    /**
     * @ORM\Column(type="date")
     */
    private $periode;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;


    /**
     * @ORM\ManyToOne(targetEntity=Apprenant::class, inversedBy="absences")
     */
    private $apprenant;

    /**
     * @ORM\ManyToOne(targetEntity=Matiere::class, inversedBy="absences")
     */
    private $matiere;

    /**
     * @ORM\ManyToOne(targetEntity=Module::class, inversedBy="absences")
     */
    private $module;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="absences")
     */
    private $user;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getPeriode(): ?\DateTimeInterface
    {
        return $this->periode;
    }

    public function setPeriode( \DateTimeInterface $periode): self
    {
        $this->periode = $periode;

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


    public function getApprenant(): ?Apprenant
    {
        return $this->apprenant;
    }

    public function setApprenant(?Apprenant $apprenant): self
    {
        $this->apprenant = $apprenant;

        return $this;
    }

    public function getMatiere()
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): self
    {
        $this->matiere = $matiere;

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): self
    {
        $this->module = $module;

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

}
