<?php
// src/Entity/User.php
namespace App\Entity;

use App\Repository\MatiereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MatiereRepository::class)
 */
/**
 * @ORM\Entity
 * @ORM\Table(name="fos_matiere")
 * @ORM\Entity(repositoryClass=MatiereRepository::class)
 */
class Matiere 
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
    private $intituleMatiere;

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
     * @ORM\OneToMany(targetEntity=UniteEnseignement::class, mappedBy="matiere")
     */
    private $uniteEnseignements;

    /**
     * @ORM\OneToMany(targetEntity=InfosEmploi::class, mappedBy="matiere")
     */
    private $infosEmplois;

    public function __construct()
    {
        $this->uniteEnseignements = new ArrayCollection();
        $this->infosEmplois = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntituleMatiere(): ?string
    {
        return $this->intituleMatiere;
    }

    public function setIntituleMatiere(string $intituleMatiere): self
    {
        $this->intituleMatiere = $intituleMatiere;

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
     * @return Collection|UniteEnseignement[]
     */
    public function getUniteEnseignements(): Collection
    {
        return $this->uniteEnseignements;
    }

    public function addUniteEnseignement(UniteEnseignement $uniteEnseignement): self
    {
        if (!$this->uniteEnseignements->contains($uniteEnseignement)) {
            $this->uniteEnseignements[] = $uniteEnseignement;
            $uniteEnseignement->setMatiere($this);
        }

        return $this;
    }

    public function removeUniteEnseignement(UniteEnseignement $uniteEnseignement): self
    {
        if ($this->uniteEnseignements->removeElement($uniteEnseignement)) {
            // set the owning side to null (unless already changed)
            if ($uniteEnseignement->getMatiere() === $this) {
                $uniteEnseignement->setMatiere(null);
            }
        }

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
            $infosEmploi->setMatiere($this);
        }

        return $this;
    }

    public function removeInfosEmploi(InfosEmploi $infosEmploi): self
    {
        if ($this->infosEmplois->removeElement($infosEmploi)) {
            // set the owning side to null (unless already changed)
            if ($infosEmploi->getMatiere() === $this) {
                $infosEmploi->setMatiere(null);
            }
        }

        return $this;
    }
}
