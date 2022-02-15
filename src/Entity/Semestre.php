<?php

namespace App\Entity;

use App\Repository\SemestreRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SemestreRepository::class)
 */
class Semestre
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
    private $LibelleSemestre;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleSemestre(): ?string
    {
        return $this->LibelleSemestre;
    }

    public function setLibelleSemestre(string $LibelleSemestre): self
    {
        $this->LibelleSemestre = $LibelleSemestre;

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
        return $this->getLibelleSemestre();
    }
}
