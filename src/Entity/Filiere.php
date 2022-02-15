<?php

// src/Entity/Filiere.php

namespace App\Entity;

use App\Repository\FiliereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="filiere")
 * @ORM\Entity(repositoryClass=FiliereRepository::class)
 */
class Filiere
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
    private $intituleFiliere;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

    /**
     * @ORM\OneToMany(targetEntity=Inscription::class, mappedBy="filiere")
     */
    private $inscriptions;


    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntituleFiliere(): ?string
    {
        return $this->intituleFiliere;
    }

    public function setIntituleFiliere(string $intituleFiliere): self
    {
        $this->intituleFiliere = $intituleFiliere;

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
        return $this->intituleFiliere;
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
            $inscription->setFiliere($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getFiliere() === $this) {
                $inscription->setFiliere(null);
            }
        }

        return $this;
    }
}
