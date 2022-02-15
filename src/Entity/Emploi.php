<?php

namespace App\Entity;

use App\Repository\EmploiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EmploiRepository::class)
 */
class Emploi
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
    private $dateDebut;

    /**
     * @ORM\Column(type="date")
     */
    private $dateFin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelleEmploi;

    /**
     * @ORM\OneToMany(targetEntity=InfosEmploi::class, mappedBy="emploi")
     */
    private $infosEmplois;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

    public function __construct()
    {
        $this->infosEmplois = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getLibelleEmploi(): ?string
    {
        return $this->libelleEmploi;
    }

    public function setLibelleEmploi(string $libelleEmploi): self
    {
        $this->libelleEmploi = $libelleEmploi;

        return $this;
    }

    /**
     * @return Collection|InfosEmploi[]
     */
    public function getInfosEmplois(): Collection
    {
        return $this->infosEmplois;
    }

    public function addInfosEmploi(InfosEmploi $infosEmploi): self
    {
        if (!$this->infosEmplois->contains($infosEmploi)) {
            $this->infosEmplois[] = $infosEmploi;
            $infosEmploi->setEmploi($this);
        }

        return $this;
    }

    public function removeInfosEmploi(InfosEmploi $infosEmploi): self
    {
        if ($this->infosEmplois->removeElement($infosEmploi)) {
            // set the owning side to null (unless already changed)
            if ($infosEmploi->getEmploi() === $this) {
                $infosEmploi->setEmploi(null);
            }
        }

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
}
