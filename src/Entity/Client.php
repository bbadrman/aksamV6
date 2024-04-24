<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ClientRepository;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\Table(name: "client")]
#[ApiResource]

class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Length(min: 2, max: 10, minMessage: "Le prénom doit contenir au moins deux caractères", maxMessage: "Le prénom doit contenir au maximum dix caractères")]
    private $firstname;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(min: 2, max: 10, minMessage: "Le nom doit contenir au moins deux caractères", maxMessage: "Le nom doit contenir au maximum dix caractères")]
    private $lastname;

    #[ORM\Column(type: 'string', length: 20)]
    private $phone;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Email(message: "Adresse e-mail non valide")]
    private $email;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $raisonSociale = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adress = null;

    #[ORM\ManyToOne(inversedBy: 'clients')]
    private ?Team $team = null;

    #[ORM\ManyToOne(inversedBy: 'clients')]
    private ?User $cmrl = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $creatAt;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Prospect $prospect = null;


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
        if (empty($this->creatAt)) {
            $timezone = new \DateTimeZone('Europe/Paris'); // Remplacez par le fuseau horaire approprié pour +1 heur
            $this->creatAt = new \Datetime('now', $timezone);
        }

        // if (empty($this->creatAt)) {
        //     $this->creatAt = new \DateTime();
        // }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): static
    {
        $this->adress = $adress;

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

    public function getCmrl(): ?User
    {
        return $this->cmrl;
    }

    public function setCmrl(?User $cmrl): static
    {
        $this->cmrl = $cmrl;

        return $this;
    }

    public function getCreatAt(): ?\DateTimeInterface
    {
        return $this->creatAt;
    }

    public function setCreatAt(\DateTimeInterface $creatAt): self
    {
        $this->creatAt = $creatAt;

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
