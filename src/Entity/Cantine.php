<?php

namespace App\Entity;

use App\Repository\CantineRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CantineRepository::class)
 */
class Cantine
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
    private $DateDebutCantine;

    /**
     * @ORM\Column(type="date")
     */
    private $DateFinCantine;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="cantines")
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $montantCantine;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebutCantine(): ?\DateTimeInterface
    {
        return $this->DateDebutCantine;
    }

    public function setDateDebutCantine(\DateTimeInterface $DateDebutCantine): self
    {
        $this->DateDebutCantine = $DateDebutCantine;

        return $this;
    }

    public function getDateFinCantine(): ?\DateTimeInterface
    {
        return $this->DateFinCantine;
    }

    public function setDateFinCantine(\DateTimeInterface $DateFinCantine): self
    {
        $this->DateFinCantine = $DateFinCantine;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMontantCantine(): ?int
    {
        return $this->montantCantine;
    }

    public function setMontantCantine(int $montantCantine): self
    {
        $this->montantCantine = $montantCantine;

        return $this;
    }
}
