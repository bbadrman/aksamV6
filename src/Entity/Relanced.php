<?php

namespace App\Entity;


use ORM\PreUpdate;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\RelancedRepository;


#[ORM\Entity(repositoryClass: RelancedRepository::class)]
#[ApiResource]

class Relanced
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motifRelanced = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $relacedAt = null;

    #[ORM\Column(length: 515, nullable: true)]
    private ?string $comment = null;


    #[ORM\ManyToOne(inversedBy: 'relanceds')]
    private ?Prospect $prospect = null;


    /**
     * Permet de mettre en place la date de création
     * 
     * @PrePersist
     * 
     * @return void
     */
    #[ORM\PrePersist]
    public function prePersist()
    {
        if (empty($this->relacedAt)) {
            $this->relacedAt = new \Datetime();
        }
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateRelacedAt(): void
    {
        if ($this->motifRelanced === '10') {
            $this->relacedAt = new \DateTime();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMotifRelanced(): ?string
    {
        return $this->motifRelanced;
    }

    public function setMotifRelanced(?string $motifRelanced): static
    {
        $this->motifRelanced = $motifRelanced;

        return $this;
    }

    public function getRelacedAt(): ?\DateTimeInterface
    {
        return $this->relacedAt;
    }
    // si le relance en rdv pursit avec la date donne sino avec datenow
    public function setRelacedAt(?\DateTimeInterface $relacedAt): static
    {
        if ($this->motifRelanced === '1') {
            $this->relacedAt = $relacedAt;
        } else {
            $this->relacedAt = new \DateTime();
        }

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

    public function getProspect(): ?Prospect
    {
        return $this->prospect;
    }

    public function setProspect(?Prospect $prospect): static
    {
        $this->prospect = $prospect;

        return $this;
    }
    public function __toString()
    {
        return $this->getId();
    }
}
