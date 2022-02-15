<?php

namespace App\Entity;

use App\Repository\TrimestreRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrimestreRepository::class)
 */
class Trimestre
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
    private $LibelleTrimestre;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleTrimestre(): ?string
    {
        return $this->LibelleTrimestre;
    }

    public function setLibelleTrimestre(string $LibelleTrimestre): self
    {
        $this->LibelleTrimestre = $LibelleTrimestre;

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
