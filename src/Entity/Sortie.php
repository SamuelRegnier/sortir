<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message:'Merci d\'ajouter un titre')]
    #[Assert\Length(
        max: 30,
        maxMessage: "Le nom ne doit pas comporter plus de 30 charactères"
    )]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThan('today', message:'La date de création d\'un évènement est à J+1 ')]
    #[Assert\NotBlank(message:'Merci de saisir une date de début d\'évènement')]
    private ?\DateTimeInterface $dateHeureDebut = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message:'La durée de l\'évènement ne peut pas être négatif')]
    private ?int $duree = null;

    #[Assert\GreaterThan('today',  message:'La date limite d\'inscription doit être fixée au minimum à J+1 ')]
    #[Assert\Expression('this.getDateLimiteInscription() < this.getDateHeureDebut()', message: 'La date limite d\'inscription doit être inférieur à la date de la sortie proposée')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]

    #[Assert\NotBlank(message:'Merci d\'indiquer la date limite d\'inscription')]
    private ?\DateTimeInterface $dateLimiteInscription = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Positive(message:'Le nombre de participant ne peut pas être négatif')]
    private ?int $nbInscriptionsMax = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message:'Merci d\'ajouter une description de l\'évènement')]
    private ?string $infosSortie = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    #[ORM\ManyToOne(inversedBy: 'organisateur')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $organisateur = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etat $etats = null;

    #[ORM\ManyToOne(inversedBy: 'sortiesLieu')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lieu $lieux = null;

    #[ORM\OneToMany(mappedBy: 'sortie', targetEntity: Inscription::class, orphanRemoval: true)]
    private Collection $inscriptions_sortie;

    #[ORM\Column]
    private ?int $nombreParticipants = null;

    #[ORM\Column(length: 255)]
    private ?string $motifAnnulation = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->inscriptions_sortie = new ArrayCollection();
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

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeInterface $dateHeureDebut): self
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(int $nbInscriptionsMax): self
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(?string $infosSortie): self
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getOrganisateur(): ?Participant
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Participant $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getEtats(): ?Etat
    {
        return $this->etats;
    }

    public function setEtats(?Etat $etats): self
    {
        $this->etats = $etats;

        return $this;
    }

    public function getLieux(): ?Lieu
    {
        return $this->lieux;
    }

    public function setLieux(?Lieu $lieux): self
    {
        $this->lieux = $lieux;

        return $this;
    }

    /**
     * @return Collection<int, Inscription>
     */
    public function getInscriptionsSortie(): Collection
    {
        return $this->inscriptions_sortie;
    }

    public function addInscriptionsSortie(Inscription $inscriptionsSortie): self
    {
        if (!$this->inscriptions_sortie->contains($inscriptionsSortie)) {
            $this->inscriptions_sortie->add($inscriptionsSortie);
            $inscriptionsSortie->setSortie($this);
        }

        return $this;
    }

    public function removeInscriptionsSortie(Inscription $inscriptionsSortie): self
    {
        if ($this->inscriptions_sortie->removeElement($inscriptionsSortie)) {
            // set the owning side to null (unless already changed)
            if ($inscriptionsSortie->getSortie() === $this) {
                $inscriptionsSortie->setSortie(null);
            }
        }

        return $this;
    }

    public function getNombreParticipants(): ?int
    {
        return $this->nombreParticipants;
    }

    public function setNombreParticipants(int $nombreParticipants): self
    {
        $this->nombreParticipants = $nombreParticipants;

        return $this;
    }

    public function getMotifAnnulation(): ?string
    {
        return $this->motifAnnulation;
    }

    public function setMotifAnnulation(string $motifAnnulation): self
    {
        $this->motifAnnulation = $motifAnnulation;

        return $this;
    }
}
