<?php

namespace App\Entity;

use App\Repository\RetardRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RetardRepository::class)
 */
class Retard
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
    private $motifRetard;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

   
    /**
     * @ORM\ManyToOne(targetEntity=Matiere::class, inversedBy="retards")
     */
    private $matiere;

    /**
     * @ORM\ManyToOne(targetEntity=Apprenant::class, inversedBy="retards")
     */
    private $apprenant;

    /**
     * @ORM\ManyToOne(targetEntity=Module::class, inversedBy="retards")
     */
    private $module;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="retards")
     */
    private $user;

    /**
     * @ORM\Column(type="date")
     */
    private $dateRetard;

    /**
     * @ORM\Column(type="time")
     */
    private $heureRetard;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMotifRetard(): ?string
    {
        return $this->motifRetard;
    }

    public function setMotifRetard(string $motifRetard): self
    {
        $this->motifRetard = $motifRetard;

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

    public function getMatiere()
    {
        return $this->matiere;
    }

    public function setMatiere(Matiere $matiere): self
    {
        $this->matiere = $matiere;

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

    public function getDateRetard(): ?\DateTimeInterface
    {
        return $this->dateRetard;
    }

    public function setDateRetard(\DateTimeInterface $dateRetard): self
    {
        $this->dateRetard = $dateRetard;

        return $this;
    }

    public function getHeureRetard(): ?\DateTimeInterface
    {
        return $this->heureRetard;
    }

    public function setHeureRetard(\DateTimeInterface $heureRetard): self
    {
        $this->heureRetard = $heureRetard;

        return $this;
    }

}
