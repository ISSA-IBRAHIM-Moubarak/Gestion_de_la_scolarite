<?php

namespace App\Entity;

use App\Repository\EmploiTempsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EmploiTempsRepository::class)
 */
class EmploiTemps
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
    private $LibelleEmploiTemps;

    /**
     * @ORM\Column(type="date")
     */
    private $DateDebutEmploiTemps;

    /**
     * @ORM\Column(type="date")
     */
    private $DateFinEmploiTemps;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleEmploiTemps(): ?string
    {
        return $this->LibelleEmploiTemps;
    }

    public function setLibelleEmploiTemps(string $LibelleEmploiTemps): self
    {
        $this->LibelleEmploiTemps = $LibelleEmploiTemps;

        return $this;
    }

    public function getDateDebutEmploiTemps(): ?\DateTimeInterface
    {
        return $this->DateDebutEmploiTemps;
    }

    public function setDateDebutEmploiTemps(\DateTimeInterface $DateDebutEmploiTemps): self
    {
        $this->DateDebutEmploiTemps = $DateDebutEmploiTemps;

        return $this;
    }

    public function getDateFinEmploiTemps(): ?\DateTimeInterface
    {
        return $this->DateFinEmploiTemps;
    }

    public function setDateFinEmploiTemps(\DateTimeInterface $DateFinEmploiTemps): self
    {
        $this->DateFinEmploiTemps = $DateFinEmploiTemps;

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
