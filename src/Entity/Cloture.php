<?php

namespace App\Entity;

use ORM\PreUpdate;

use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping\PrePersist;
use App\Repository\ClotureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClotureRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource]
class Cloture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motifCloture = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTime $clotureAt = null;

    #[ORM\ManyToOne(inversedBy: 'clotures')]
    private ?Prospect $prospect = null;

    #[ORM\Column(length: 515, nullable: true)]
    private ?string $comment = null;

    /**
     * Permet de mettre en place la date de création
     * 
     * @PrePersist
     * 
     * @return void
     */
    #[ORM\PrePersist]
    public function prePersist(): void
    {
        if (empty($this->clotureAt)) {
            $timezone = new \DateTimeZone('Europe/London');

            $this->clotureAt = new \Datetime('now', $timezone);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMotifCloture(): ?string
    {
        return $this->motifCloture;
    }

    public function setMotifCloture(?string $motifCloture): static
    {
        $this->motifCloture = $motifCloture;

        return $this;
    }

    public function getClotureAt(): ?\DateTimeImmutable
    {
        if ($this->clotureAt instanceof \DateTime) {
            return \DateTimeImmutable::createFromMutable($this->clotureAt);
        }
        return $this->clotureAt;
    }

    public function setClotureAt(?\DateTimeImmutable $clotureAt): static
    {
        $this->clotureAt = $clotureAt;

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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }
}
