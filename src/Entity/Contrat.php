<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ContratRepository;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ContratRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource]
class Contrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $raisonSociale = null;

    #[ORM\ManyToOne(inversedBy: 'contrats')]
    private ?User $comrcl = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateSouscrpt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEffet = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $activite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imatriclt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $partenaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $compagnie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $formule = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datePrelvm = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fraction = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $frais = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typeConducteur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $conducteur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datePermis = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datePreleveAcompte = null;

    #[ORM\ManyToOne(inversedBy: 'contrats')]
    private ?Product $products = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $cotisation = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $acompte = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $firstReglement = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $secondReglement = null;

    // Méthode appelée automatiquement avant chaque update
    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getRaisonSociale(): ?string
    {
        return $this->raisonSociale;
    }

    public function setRaisonSociale(?string $raisonSociale): static
    {
        $this->raisonSociale = $raisonSociale;

        return $this;
    }

    public function getComrcl(): ?User
    {
        return $this->comrcl;
    }

    public function setComrcl(?User $comrcl): static
    {
        $this->comrcl = $comrcl;

        return $this;
    }

    public function getDateSouscrpt(): ?\DateTimeInterface
    {
        return $this->dateSouscrpt;
    }

    public function setDateSouscrpt(?\DateTimeInterface $dateSouscrpt): static
    {
        $this->dateSouscrpt = $dateSouscrpt;

        return $this;
    }

    public function getDateEffet(): ?\DateTimeInterface
    {
        return $this->dateEffet;
    }

    public function setDateEffet(?\DateTimeInterface $dateEffet): static
    {
        $this->dateEffet = $dateEffet;

        return $this;
    }



    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getActivite(): ?string
    {
        return $this->activite;
    }

    public function setActivite(?string $activite): static
    {
        $this->activite = $activite;

        return $this;
    }

    public function getImatriclt(): ?string
    {
        return $this->imatriclt;
    }

    public function setImatriclt(?string $imatriclt): static
    {
        $this->imatriclt = $imatriclt;

        return $this;
    }

    public function getPartenaire(): ?string
    {
        return $this->partenaire;
    }

    public function setPartenaire(?string $partenaire): static
    {
        $this->partenaire = $partenaire;

        return $this;
    }

    public function getCompagnie(): ?string
    {
        return $this->compagnie;
    }

    public function setCompagnie(?string $compagnie): static
    {
        $this->compagnie = $compagnie;

        return $this;
    }

    public function getFormule(): ?string
    {
        return $this->formule;
    }

    public function setFormule(?string $formule): static
    {
        $this->formule = $formule;

        return $this;
    }

    public function getDatePrelvm(): ?\DateTimeInterface
    {
        return $this->datePrelvm;
    }

    public function setDatePrelvm(?\DateTimeInterface $datePrelvm): static
    {
        $this->datePrelvm = $datePrelvm;

        return $this;
    }

    public function getFraction(): ?string
    {
        return $this->fraction;
    }

    public function setFraction(?string $fraction): static
    {
        $this->fraction = $fraction;

        return $this;
    }

    public function getFrais(): ?string
    {
        return $this->frais;
    }

    public function setFrais(?string $frais): static
    {
        $this->frais = $frais;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getTypeConducteur(): ?string
    {
        return $this->typeConducteur;
    }

    public function setTypeConducteur(?string $typeConducteur): static
    {
        $this->typeConducteur = $typeConducteur;

        return $this;
    }

    public function getConducteur(): ?string
    {
        return $this->conducteur;
    }

    public function setConducteur(?string $conducteur): static
    {
        $this->conducteur = $conducteur;

        return $this;
    }

    public function getDatePermis(): ?\DateTimeInterface
    {
        return $this->datePermis;
    }

    public function setDatePermis(?\DateTimeInterface $datePermis): static
    {
        $this->datePermis = $datePermis;

        return $this;
    }

    public function getDatePreleveAcompte(): ?\DateTimeInterface
    {
        return $this->datePreleveAcompte;
    }

    public function setDatePreleveAcompte(?\DateTimeInterface $datePreleveAcompte): static
    {
        $this->datePreleveAcompte = $datePreleveAcompte;

        return $this;
    }

    public function getProducts(): ?Product
    {
        return $this->products;
    }

    public function setProducts(?Product $products): static
    {
        $this->products = $products;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCotisation(): ?string
    {
        return $this->cotisation;
    }

    public function setCotisation(?string $cotisation): static
    {
        $this->cotisation = $cotisation;

        return $this;
    }

    public function getAcompte(): ?string
    {
        return $this->acompte;
    }

    public function setAcompte(?string $acompte): static
    {
        $this->acompte = $acompte;

        return $this;
    }

    public function getFirstReglement(): ?string
    {
        return $this->firstReglement;
    }

    public function setFirstReglement(?string $firstReglement): static
    {
        $this->firstReglement = $firstReglement;

        return $this;
    }

    public function getSecondReglement(): ?string
    {
        return $this->secondReglement;
    }

    public function setSecondReglement(?string $secondReglement): static
    {
        $this->secondReglement = $secondReglement;

        return $this;
    }
}
