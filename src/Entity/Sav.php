<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SavRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: SavRepository::class)]
class Sav
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column(length: 255, nullable: true)]
    private ?string $natureDemande = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'savs')]
    private Collection $afect;

    #[ORM\ManyToOne(inversedBy: 'savs')]
    private ?Contrat $contrat = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $CreatAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motifRelance = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $relanceAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comentTraiter = null;

    #[ORM\OneToMany(mappedBy: 'sav', targetEntity: RelanceSav::class)]
    private Collection $relanceSavs;

    public function __construct()
    {
        $this->afect = new ArrayCollection();
        $this->relanceSavs = new ArrayCollection();
    }

    /**
     * Permet de mettre en place la date de création
     * 
     * @ORM\PrePersist
     * 
     * @return void
     */
    #[ORM\PrePersist]
    public function prePersist(): void
    {
        if (empty($this->CreatAt)) {
            $timezone = new \DateTimeZone('Europe/London'); // Remplacez par le fuseau horaire approprié pour +1 heur
            $this->CreatAt = new \Datetime('now', $timezone);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getNatureDemande(): ?string
    {
        return $this->natureDemande;
    }

    public function setNatureDemande(?string $natureDemande): static
    {
        $this->natureDemande = $natureDemande;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAfect(): Collection
    {
        return $this->afect;
    }

    public function addAfect(User $afect): static
    {
        if (!$this->afect->contains($afect)) {
            $this->afect->add($afect);
        }

        return $this;
    }

    public function removeAfect(User $afect): static
    {
        $this->afect->removeElement($afect);

        return $this;
    }

    public function getContrat(): ?Contrat
    {
        return $this->contrat;
    }

    public function setContrat(?Contrat $contrat): static
    {
        $this->contrat = $contrat;

        return $this;
    }

    public function getCreatAt(): ?\DateTimeInterface
    {
        return $this->CreatAt;
    }

    public function setCreatAt(?\DateTimeInterface $CreatAt): static
    {
        $this->CreatAt = $CreatAt;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getMotifRelance(): ?string
    {
        return $this->motifRelance;
    }

    public function setMotifRelance(?string $motifRelance): static
    {
        $this->motifRelance = $motifRelance;

        return $this;
    }

    public function getRelanceAt(): ?\DateTimeInterface
    {
        return $this->relanceAt;
    }

    public function setRelanceAt(?\DateTimeInterface $relanceAt): static
    {
        $this->relanceAt = $relanceAt;

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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getComentTraiter(): ?string
    {
        return $this->comentTraiter;
    }

    public function setComentTraiter(?string $comentTraiter): static
    {
        $this->comentTraiter = $comentTraiter;

        return $this;
    }

    /**
     * @return Collection<int, RelanceSav>
     */
    public function getRelanceSavs(): Collection
    {
        return $this->relanceSavs;
    }

    public function addRelanceSav(RelanceSav $relanceSav): static
    {
        if (!$this->relanceSavs->contains($relanceSav)) {
            $this->relanceSavs->add($relanceSav);
            $relanceSav->setSav($this);
        }

        return $this;
    }

    public function removeRelanceSav(RelanceSav $relanceSav): static
    {
        if ($this->relanceSavs->removeElement($relanceSav)) {
            // set the owning side to null (unless already changed)
            if ($relanceSav->getSav() === $this) {
                $relanceSav->setSav(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        // Retournez une représentation significative de l'objet Sav, par exemple l'ID ou une autre propriété
        return (string) $this->getId();
        // Ou, si vous avez d'autres champs plus descriptifs, vous pouvez les utiliser à la place
    }
}
