<?php
// src/Entity/Module.php
namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ModuleRepository::class)
 */
/**
 * @ORM\Entity
 * @ORM\Table(name="fos_module")
 * @ORM\Entity(repositoryClass=ModuleRepository::class)
 */

class Module
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
    private $intituleModule;

    /**
     * @ORM\Column(type="integer")
     */
    private $coeficient;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombreHeure;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

    /**
     * @ORM\ManyToMany(targetEntity=Filiere::class)
     */
    private $filiere;

    /**
     * @ORM\OneToMany(targetEntity=UniteEnseignement::class, mappedBy="module")
     */
    private $uniteEnseignement;

    /**
     * @ORM\OneToMany(targetEntity=Absence::class, mappedBy="module")
     */
    private $absences;

    /**
     * @ORM\OneToMany(targetEntity=Retard::class, mappedBy="module")
     */
    private $retards;

    /**
     * @ORM\OneToMany(targetEntity=InfosEmploi::class, mappedBy="module")
     */
    private $infosEmplois;

    public function __construct()
    {
        $this->filiere = new ArrayCollection();
        $this->uniteEnseignement = new ArrayCollection();
        $this->absences = new ArrayCollection();
        $this->retards = new ArrayCollection();
        $this->emploiTemps = new ArrayCollection();
        $this->infosEmplois = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntituleModule(): ?string
    {
        return $this->intituleModule;
    }

    public function setIntituleModule(string $intituleModule): self
    {
        $this->intituleModule = $intituleModule;

        return $this;
    }

    public function getCoeficient(): ?int
    {
        return $this->coeficient;
    }

    public function setCoeficient(int $coeficient): self
    {
        $this->coeficient = $coeficient;

        return $this;
    }

    public function getNombreHeure(): ?int
    {
        return $this->nombreHeure;
    }

    public function setNombreHeure(int $nombreHeure): self
    {
        $this->nombreHeure = $nombreHeure;

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
     * @return Collection|Filiere[]
     */
    public function getFiliere(): Collection
    {
        return $this->filiere;
    }

    public function addFiliere(Filiere $filiere): self
    {
        if (!$this->filiere->contains($filiere)) {
            $this->filiere[] = $filiere;
        }

        return $this;
    }

    public function removeFiliere(Filiere $filiere): self
    {
        $this->filiere->removeElement($filiere);

        return $this;
    }

    /**
     * @return Collection|UniteEnseignement[]
     */
    public function getUniteEnseignement(): Collection
    {
        return $this->uniteEnseignement;
    }

    public function addUniteEnseignement(UniteEnseignement $uniteEnseignement): self
    {
        if (!$this->uniteEnseignement->contains($uniteEnseignement)) {
            $this->uniteEnseignement[] = $uniteEnseignement;
            $uniteEnseignement->setModule($this);
        }

        return $this;
    }

    public function removeUniteEnseignement(UniteEnseignement $uniteEnseignement): self
    {
        if ($this->uniteEnseignement->removeElement($uniteEnseignement)) {
            // set the owning side to null (unless already changed)
            if ($uniteEnseignement->getModule() === $this) {
                $uniteEnseignement->setModule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Absence[]
     */
    public function getAbsences(): Collection
    {
        return $this->absences;
    }

    public function addAbsence(Absence $absence): self
    {
        if (!$this->absences->contains($absence)) {
            $this->absences[] = $absence;
            $absence->setModule($this);
        }

        return $this;
    }

    public function removeAbsence(Absence $absence): self
    {
        if ($this->absences->removeElement($absence)) {
            // set the owning side to null (unless already changed)
            if ($absence->getModule() === $this) {
                $absence->setModule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Retard[]
     */
    public function getRetards(): Collection
    {
        return $this->retards;
    }

    public function addRetard(Retard $retard): self
    {
        if (!$this->retards->contains($retard)) {
            $this->retards[] = $retard;
            $retard->setModule($this);
        }

        return $this;
    }

    public function removeRetard(Retard $retard): self
    {
        if ($this->retards->removeElement($retard)) {
            // set the owning side to null (unless already changed)
            if ($retard->getModule() === $this) {
                $retard->setModule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EmploiTemps[]
     */
    public function getEmploiTemps(): Collection
    {
        return $this->emploiTemps;
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
            $infosEmploi->setModule($this);
        }

        return $this;
    }

    public function removeInfosEmploi(InfosEmploi $infosEmploi): self
    {
        if ($this->infosEmplois->removeElement($infosEmploi)) {
            // set the owning side to null (unless already changed)
            if ($infosEmploi->getModule() === $this) {
                $infosEmploi->setModule(null);
            }
        }

        return $this;
    }

}
