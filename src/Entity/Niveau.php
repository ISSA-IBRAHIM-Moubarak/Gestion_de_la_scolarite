<?php

namespace App\Entity;

use App\Repository\NiveauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NiveauRepository::class)
 */
class Niveau
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
    private $libelleNiveau;

    /**
     * @ORM\Column(type="integer")
     */
    private $montant;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

    /**
     * @ORM\OneToMany(targetEntity=Inscription::class, mappedBy="niveau")
     */
    private $inscriptions;

    /**
     * @ORM\ManyToOne(targetEntity=Structure::class, inversedBy="niveaux")
     */
    private $structure;

    /**
     * @ORM\ManyToOne(targetEntity=Salle::class, inversedBy="niveaux")
     */
    private $classe;

    public function __construct()
    {
        $this->annee = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleNiveau(): ?string
    {
        return $this->libelleNiveau;
    }

    public function setLibelleNiveau(string $libelleNiveau): self
    {
        $this->libelleNiveau = $libelleNiveau;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

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
    

    public function __toString()
    {
        return $this->libelleNiveau;
    }

    /**
     * @return Collection|Inscription[]
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscription $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions[] = $inscription;
            $inscription->setNiveau($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getNiveau() === $this) {
                $inscription->setNiveau(null);
            }
        }

        return $this;
    }

    public function getStructure(): ?Structure
    {
        return $this->structure;
    }

    public function setStructure(?Structure $structure): self
    {
        $this->structure = $structure;

        return $this;
    }

    public function getClasse(): ?Salle
    {
        return $this->classe;
    }

    public function setClasse(?Salle $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

}
