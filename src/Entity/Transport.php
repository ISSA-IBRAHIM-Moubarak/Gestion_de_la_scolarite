<?php

namespace App\Entity;

use App\Repository\TransportRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransportRepository::class)
 */
class Transport
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
    private $intitileTransport;

    /**
     * @ORM\Column(type="date")
     */
    private $DateDebutTransport;

    /**
     * @ORM\Column(type="date")
     */
    private $DateFinTransport;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transports")
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $montantTransport;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntitileTransport(): ?string
    {
        return $this->intitileTransport;
    }

    public function setIntitileTransport(string $intitileTransport): self
    {
        $this->intitileTransport = $intitileTransport;

        return $this;
    }

    public function getDateDebutTransport(): ?\DateTimeInterface
    {
        return $this->DateDebutTransport;
    }

    public function setDateDebutTransport(\DateTimeInterface $DateDebutTransport): self
    {
        $this->DateDebutTransport = $DateDebutTransport;

        return $this;
    }

    public function getDateFinTransport(): ?\DateTimeInterface
    {
        return $this->DateFinTransport;
    }

    public function setDateFinTransport(\DateTimeInterface $DateFinTransport): self
    {
        $this->DateFinTransport = $DateFinTransport;

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

    public function getMontantTransport(): ?int
    {
        return $this->montantTransport;
    }

    public function setMontantTransport(int $montantTransport): self
    {
        $this->montantTransport = $montantTransport;

        return $this;
    }
}
