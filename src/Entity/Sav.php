<?php

namespace App\Entity;

use App\Repository\SavRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SavRepository::class)]
class Sav
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $creatAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $natureDemande = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'savs')]
    private Collection $afect;

    #[ORM\ManyToOne(inversedBy: 'savs')]
    private ?Contrat $contrat = null;

    public function __construct()
    {
        $this->afect = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatAt(): ?\DateTimeImmutable
    {
        return $this->creatAt;
    }

    public function setCreatAt(?\DateTimeImmutable $creatAt): static
    {
        $this->creatAt = $creatAt;

        return $this;
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
}
