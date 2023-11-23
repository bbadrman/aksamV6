<?php

namespace App\Entity;

use App\Entity\Team;
use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\HistoryRepository;


#[ORM\Entity(repositoryClass: HistoryRepository::class)]
#[ApiResource]

class History
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $actionDate = null;

    #[ORM\ManyToOne(inversedBy: 'histories')]
    private ?Prospect $prospect = null;


    #[ORM\ManyToOne(inversedBy: 'histors')]
    private ?Team $team = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $actionType = null;

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
        if (empty($this->actionDate)) {
            $this->actionDate = new \Datetime();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getActionDate(): ?\DateTimeInterface
    {
        return $this->actionDate;
    }

    public function setActionDate(?\DateTimeInterface $actionDate): static
    {
        $this->actionDate = new \DateTime();

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

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): static
    {
        $this->team = $team;

        return $this;
    }
    public function getActionType(): ?string
    {
        return $this->actionType;
    }

    public function setActionType(?string $actionType): self
    {
        $this->actionType = $actionType;

        return $this;
    }
}
