<?php

namespace App\Entity;

use App\Repository\VersementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VersementRepository::class)
 */
class Versement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $DateVersements;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

    /**
     * @ORM\ManyToOne(targetEntity=Inscription::class, inversedBy="versements")
     */
    private $inscription;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="versements")
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $montantVersement;

    /**
     * @ORM\ManyToOne(targetEntity=Annee::class, inversedBy="versements")
     */
    private $annee;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateVersements(): ?\DateTimeInterface
    {
        return $this->DateVersements;
    }

    public function setDateVersements(\DateTimeInterface $DateVersements): self
    {
        $this->DateVersements = $DateVersements;

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

    public function getMontantVersement(): ?int
    {
        return $this->montantVersement;
    }

    public function setMontantVersement(int $montantVersement): self
    {
        $this->montantVersement = $montantVersement;

        return $this;
    }

    public function getAnnee(): ?Annee
    {
        return $this->annee;
    }

    public function setAnnee(?Annee $annee): self
    {
        $this->annee = $annee;

        return $this;
    }
}
