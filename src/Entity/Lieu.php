<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LieuRepository::class)]
class Lieu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\Column(length: 30)]
    private ?string $rue = null;

    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    #[ORM\OneToMany(mappedBy: 'lieux', targetEntity: Sortie::class)]
    private Collection $sortiesLieu;

    #[ORM\ManyToOne(inversedBy: 'lieux')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ville $villes = null;

    public function __construct()
    {
        $this->sortiesLieu = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesLieu(): Collection
    {
        return $this->sortiesLieu;
    }

    public function addSortiesLieu(Sortie $sortiesLieu): self
    {
        if (!$this->sortiesLieu->contains($sortiesLieu)) {
            $this->sortiesLieu->add($sortiesLieu);
            $sortiesLieu->setLieux($this);
        }

        return $this;
    }

    public function removeSortiesLieu(Sortie $sortiesLieu): self
    {
        if ($this->sortiesLieu->removeElement($sortiesLieu)) {
            // set the owning side to null (unless already changed)
            if ($sortiesLieu->getLieux() === $this) {
                $sortiesLieu->setLieux(null);
            }
        }

        return $this;
    }

    public function getVilles(): ?Ville
    {
        return $this->villes;
    }

    public function setVilles(?Ville $villes): self
    {
        $this->villes = $villes;

        return $this;
    }
}
