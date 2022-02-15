<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InscriptionRepository::class)
 */
class Inscription
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
    private $dateVersement;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

    /**
     * @ORM\ManyToOne(targetEntity=Apprenant::class, inversedBy="inscriptions")
     */
    private $apprenant;

    /**
     * @ORM\ManyToOne(targetEntity=Niveau::class, inversedBy="inscriptions")
     */
    private $niveau;

    /**
     * @ORM\ManyToOne(targetEntity=Filiere::class, inversedBy="inscriptions")
     */
    private $filiere;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="inscriptions")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Versement::class, mappedBy="inscription")
     */
    private $versements;

    /**
     * @ORM\Column(type="integer")
     */
    private $montantInscription;

    /**
     * @ORM\ManyToOne(targetEntity=Annee::class, inversedBy="inscriptions")
     */
    private $annee;

    /**
     * @ORM\ManyToOne(targetEntity=Bourse::class, inversedBy="inscriptions")
     */
    private $bourse;

    /**
     * @ORM\OneToMany(targetEntity=ApprenantClasse::class, mappedBy="inscription")
     */
    private $apprenantClasses;

    public function __construct()
    {
        $this->versements = new ArrayCollection();
        $this->apprenantClasses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateVersement(): ?\DateTimeInterface
    {
        return $this->dateVersement;
    }

    public function setDateVersement(\DateTimeInterface $dateVersement): self
    {
        $this->dateVersement = $dateVersement;

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

    public function getNiveau(): ?Niveau
    {
        return $this->niveau;
    }

    public function setNiveau(?Niveau $niveau): self
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getFiliere(): ?Filiere
    {
        return $this->filiere;
    }

    public function setFiliere(?Filiere $filiere): self
    {
        $this->filiere = $filiere;

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

    /**
     * @return Collection|Versement[]
     */
    public function getVersements(): Collection
    {
        return $this->versements;
    }

    public function addVersement(Versement $versement): self
    {
        if (!$this->versements->contains($versement)) {
            $this->versements[] = $versement;
            $versement->setInscription($this);
        }

        return $this;
    }

    public function removeVersement(Versement $versement): self
    {
        if ($this->versements->removeElement($versement)) {
            // set the owning side to null (unless already changed)
            if ($versement->getInscription() === $this) {
                $versement->setInscription(null);
            }
        }

        return $this;
    }

    public function getMontantInscription(): ?int
    {
        return $this->montantInscription;
    }

    public function setMontantInscription(int $montantInscription): self
    {
        $this->montantInscription = $montantInscription;

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

    public function getBourse(): ?Bourse
    {
        return $this->bourse;
    }

    public function setBourse(?Bourse $bourse): self
    {
        $this->bourse = $bourse;

        return $this;
    }

    /**
     * @return Collection|ApprenantClasse[]
     */
    public function getApprenantClasses(): Collection
    {
        return $this->apprenantClasses;
    }

    public function addApprenantClass(ApprenantClasse $apprenantClass): self
    {
        if (!$this->apprenantClasses->contains($apprenantClass)) {
            $this->apprenantClasses[] = $apprenantClass;
            $apprenantClass->setInscription($this);
        }

        return $this;
    }

    public function removeApprenantClass(ApprenantClasse $apprenantClass): self
    {
        if ($this->apprenantClasses->removeElement($apprenantClass)) {
            // set the owning side to null (unless already changed)
            if ($apprenantClass->getInscription() === $this) {
                $apprenantClass->setInscription(null);
            }
        }

        return $this;
    }

}
