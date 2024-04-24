<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AccesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccesRepository::class)]
#[ORM\Table(name: "acces")]
#[ApiResource]
class Acces
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'acces')]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $accessDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $logoutDate = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAccessDate(): ?\DateTimeInterface
    {
        return $this->accessDate;
    }

    public function setAccessDate(?\DateTimeInterface $accessDate): static
    {
        $this->accessDate = $accessDate;

        return $this;
    }
    public function getLogoutDate(): ?\DateTimeInterface
    {
        return $this->logoutDate;
    }

    public function setLogoutDate(?\DateTimeInterface $logoutDate): self
    {
        $this->logoutDate = $logoutDate;

        return $this;
    }
}
