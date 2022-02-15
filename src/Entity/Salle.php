<?php

namespace App\Entity;

use App\Repository\SalleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SalleRepository::class)
 */
class Salle
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
    private $intituleSalle;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

    /**
     * @ORM\OneToMany(targetEntity=Niveau::class, mappedBy="classe")
     */
    private $niveaux;

    /**
     * @ORM\OneToMany(targetEntity=ApprenantClasse::class, mappedBy="salle")
     */
    private $apprenantClasses;

    /**
     * @ORM\ManyToOne(targetEntity=Structure::class, inversedBy="salles")
     */
    private $structure;

    /**
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="salle")
     */
    private $notes;

    public function __construct()
    {
        $this->niveaux = new ArrayCollection();
        $this->apprenantClasses = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntituleSalle(): ?string
    {
        return $this->intituleSalle;
    }

    public function setIntituleSalle(string $intituleSalle): self
    {
        $this->intituleSalle = $intituleSalle;

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


    /**
     * @return Collection|Niveau[]
     */
    public function getNiveaux(): Collection
    {
        return $this->niveaux;
    }

    public function addNiveau(Niveau $niveau): self
    {
        if (!$this->niveaux->contains($niveau)) {
            $this->niveaux[] = $niveau;
            $niveau->setClasse($this);
        }

        return $this;
    }

    public function removeNiveau(Niveau $niveau): self
    {
        if ($this->niveaux->removeElement($niveau)) {
            // set the owning side to null (unless already changed)
            if ($niveau->getClasse() === $this) {
                $niveau->setClasse(null);
            }
        }

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
            $apprenantClass->setSalle($this);
        }

        return $this;
    }

    public function removeApprenantClass(ApprenantClasse $apprenantClass): self
    {
        if ($this->apprenantClasses->removeElement($apprenantClass)) {
            // set the owning side to null (unless already changed)
            if ($apprenantClass->getSalle() === $this) {
                $apprenantClass->setSalle(null);
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

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setSalle($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getSalle() === $this) {
                $note->setSalle(null);
            }
        }

        return $this;
    }

}
