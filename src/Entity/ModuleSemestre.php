<?php

namespace App\Entity;

use App\Repository\ModuleSemestreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ModuleSemestreRepository::class)
 */
class ModuleSemestre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Module::class, inversedBy="moduleSemestres")
     * @ORM\JoinColumn(nullable=true)
     */
    private $module;

    /**
     * @ORM\ManyToOne(targetEntity=Semestre::class, inversedBy="moduleSemestres")
     * @ORM\JoinColumn(nullable=true)
     */
    private $semestre;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

    /**
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="modulesemestre")
     */
    private $notes;


    public function __toString()
    {
        return $this->getModule()->getIntituleModule();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    
    public function getSemestre(): ?Semestre
    {
        return $this->semestre;
    }

    public function setSemestre(?Semestre $semestre): self
    {
        $this->semestre = $semestre;

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
     * Get module
     * 
     * @return \Doctrine\Common\Collections\Collection
     */

    public function getModule()
    {
        return $this->module;
    }

    public function setModule(?Module $module): self
    {
        $this->module = $module;

        return $this;
    }
    
/**
 * Constructor
 */
public function __construct()
{
    $this->module = new \Doctrine\Common\Collections\ArrayCollection();

    $this->notes = new ArrayCollection();

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
        $note->setModulesemestre($this);
    }

    return $this;
}

public function removeNote(Note $note): self
{
    if ($this->notes->removeElement($note)) {
        // set the owning side to null (unless already changed)
        if ($note->getModulesemestre() === $this) {
            $note->setModulesemestre(null);
        }
    }

    return $this;
}


}
