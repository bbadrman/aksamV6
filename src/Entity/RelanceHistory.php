<?php

namespace App\Entity;


use App\Repository\RelanceHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RelanceHistoryRepository::class)]

class RelanceHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motifRelanced = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $relacedAt = null;

    #[ORM\Column(length: 515, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'relanceHistories')]
    private ?Prospect $prospect = null;

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

    public function getRelacedAt(): ?\DateTimeImmutable
    {
        return $this->relacedAt;
    }

    public function setRelacedAt(?\DateTimeImmutable $relacedAt): static
    {
        $this->relacedAt = $relacedAt;

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
}
