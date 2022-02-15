<?php

namespace App\Entity;

use App\Repository\UniteEnseignementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UniteEnseignementRepository::class)
 */
class UniteEnseignement
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
    private $LibelleUniteEnseignement;

    /**
     * @ORM\ManyToOne(targetEntity=Matiere::class, inversedBy="uniteEnseignements")
     */
    private $matiere;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

    /**
     * @ORM\ManyToOne(targetEntity=Module::class, inversedBy="uniteEnseignement")
     */
    private $module;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleUniteEnseignement(): ?string
    {
        return $this->LibelleUniteEnseignement;
    }

    public function setLibelleUniteEnseignement(string $LibelleUniteEnseignement): self
    {
        $this->LibelleUniteEnseignement = $LibelleUniteEnseignement;

        return $this;
    }

    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): self
    {
        $this->matiere = $matiere;

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

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): self
    {
        $this->module = $module;

        return $this;
    }
}
