<?php

namespace App\Entity;

use App\Repository\ModeVersementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ModeVersementRepository::class)
 */
class ModeVersement
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
    private $fraisInscription;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $typeVersement;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

    /**
     * @ORM\OneToMany(targetEntity=Inscription::class, mappedBy="modeVersement")
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

    public function getFraisInscription(): ?int
    {
        return $this->fraisInscription;
    }

    public function setFraisInscription(int $fraisInscription): self
    {
        $this->fraisInscription = $fraisInscription;

        return $this;
    }

    public function getTypeVersement(): ?string
    {
        return $this->typeVersement;
    }

    public function setTypeVersement(string $typeVersement): self
    {
        $this->typeVersement = $typeVersement;

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
            $inscription->setModeVersement($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getModeVersement() === $this) {
                $inscription->setModeVersement(null);
            }
        }

        return $this;
    }
}
