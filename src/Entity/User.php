<?php
// src/Entity/User.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\ManyToMany(targetEntity=Annee::class)
     */
    private $annee;

    /**
     * @ORM\ManyToMany(targetEntity=Niveau::class)
     */
    private $niveau;

    /**
     * @ORM\ManyToOne(targetEntity=Structure::class, inversedBy="users")
     */
    private $structure;

    /**
     * @ORM\OneToMany(targetEntity=Inscription::class, mappedBy="user")
     */
    private $inscriptions;

    /**
     * @ORM\OneToMany(targetEntity=Versement::class, mappedBy="user")
     */
    private $versements;

    /**
     * @ORM\OneToMany(targetEntity=Absence::class, mappedBy="user")
     */
    private $absences;

    /**
     * @ORM\OneToMany(targetEntity=Retard::class, mappedBy="user")
     */
    private $retards;

    /**
     * @ORM\OneToMany(targetEntity=Cantine::class, mappedBy="user")
     */
    private $cantines;

    /**
     * @ORM\OneToMany(targetEntity=Transport::class, mappedBy="user")
     */
    private $transports;

     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @ORM\OneToMany(targetEntity=Apprenant::class, mappedBy="user")
     */
    private $apprenants;


    public function __construct()
    {
        parent::__construct();
        $this->annee = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
        $this->versements = new ArrayCollection();
        $this->absences = new ArrayCollection();
        $this->retards = new ArrayCollection();
        $this->cantines = new ArrayCollection();
        $this->transports = new ArrayCollection();
        $this->apprenants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return Collection|Annee[]
     */
    public function getAnnee(): Collection
    {
        return $this->annee;
    }

    public function addAnnee(Annee $annee): self
    {
        if (!$this->annee->contains($annee)) {
            $this->annee[] = $annee;
        }

        return $this;
    }

    public function removeAnnee(Annee $annee): self
    {
        $this->annee->removeElement($annee);

        return $this;
    }

    public function getStructure(): ?Structure
    {
        return $this->structure;
    }

    public function setStructure(?Structure $structure): self
    {
        $this->structure = $structure;

        return $this;
    }

    /**
     * @return Collection|Inscription[]
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscription $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions[] = $inscription;
            $inscription->setUser($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getUser() === $this) {
                $inscription->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Versement[]
     */
    public function getVersements(): Collection
    {
        return $this->versements;
    }

    public function addVersement(Versement $versement): self
    {
        if (!$this->versements->contains($versement)) {
            $this->versements[] = $versement;
            $versement->setUser($this);
        }

        return $this;
    }

    public function removeVersement(Versement $versement): self
    {
        if ($this->versements->removeElement($versement)) {
            // set the owning side to null (unless already changed)
            if ($versement->getUser() === $this) {
                $versement->setUser(null);
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
            $absence->setUser($this);
        }

        return $this;
    }

    public function removeAbsence(Absence $absence): self
    {
        if ($this->absences->removeElement($absence)) {
            // set the owning side to null (unless already changed)
            if ($absence->getUser() === $this) {
                $absence->setUser(null);
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
            $retard->setUser($this);
        }

        return $this;
    }

    public function removeRetard(Retard $retard): self
    {
        if ($this->retards->removeElement($retard)) {
            // set the owning side to null (unless already changed)
            if ($retard->getUser() === $this) {
                $retard->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Cantine[]
     */
    public function getCantines(): Collection
    {
        return $this->cantines;
    }

    public function addCantine(Cantine $cantine): self
    {
        if (!$this->cantines->contains($cantine)) {
            $this->cantines[] = $cantine;
            $cantine->setUser($this);
        }

        return $this;
    }

    public function removeCantine(Cantine $cantine): self
    {
        if ($this->cantines->removeElement($cantine)) {
            // set the owning side to null (unless already changed)
            if ($cantine->getUser() === $this) {
                $cantine->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transport[]
     */
    public function getTransports(): Collection
    {
        return $this->transports;
    }

    public function addTransport(Transport $transport): self
    {
        if (!$this->transports->contains($transport)) {
            $this->transports[] = $transport;
            $transport->setUser($this);
        }

        return $this;
    }

    public function removeTransport(Transport $transport): self
    {
        if ($this->transports->removeElement($transport)) {
            // set the owning side to null (unless already changed)
            if ($transport->getUser() === $this) {
                $transport->setUser(null);
            }
        }

        return $this;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir() . '/' . $this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../public/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/logoStucture';
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }



    /**
     * Set path
     *
     * @param string $path
     *
     * @return User
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->getFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->path = $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * @return Collection|Apprenant[]
     */
    public function getApprenants(): Collection
    {
        return $this->apprenants;
    }

    public function addApprenant(Apprenant $apprenant): self
    {
        if (!$this->apprenants->contains($apprenant)) {
            $this->apprenants[] = $apprenant;
            $apprenant->setUser($this);
        }

        return $this;
    }

    public function removeApprenant(Apprenant $apprenant): self
    {
        if ($this->apprenants->removeElement($apprenant)) {
            // set the owning side to null (unless already changed)
            if ($apprenant->getUser() === $this) {
                $apprenant->setUser(null);
            }
        }

        return $this;
    }
}
