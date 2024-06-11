<?php

namespace App\Entity;



use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProspectRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;


#[ORM\Entity(repositoryClass: ProspectRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: "prospect")]
#[ApiResource]

class Prospect
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: 'string',  length: 255)]

    private $name;

    #[ORM\Column(type: 'string', length: 255)]

    private $lastname;

    #[ORM\Column(type: 'string', length: 255)]
    private $phone;

    #[ORM\Column(type: 'string', nullable: true, length: 255)]
    private $email;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $gender;

    #[ORM\Column(type: 'string', nullable: true, length: 255)]
    private $city;

    #[ORM\Column(type: 'text', nullable: true)]
    private $adress;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $brithAt;

    #[ORM\Column(type: 'string', nullable: true, length: 50)]
    private $source;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $typeProspect;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $raisonSociale;

    #[ORM\Column(type: 'string', nullable: true)]
    private $codePost;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $gsm;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $assure;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $lastAssure;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $motifResil;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'prospects')]
    private $autor;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'prospections')]
    private $comrcl;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'prospects')]
    private $team;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $motifSaise;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $creatAt;

    #[ORM\OneToMany(mappedBy: 'prospect', targetEntity: Relanced::class, cascade: ["persist"])]
    private Collection $relanceds;

    #[ORM\OneToMany(mappedBy: 'prospect', targetEntity: History::class)]
    private Collection $histories;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $activites = null;

    #[ORM\ManyToOne(inversedBy: 'prospects')]
    private ?Product $product = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;





    public function __construct()
    {


        $this->relanceds = new ArrayCollection();
        $this->histories = new ArrayCollection();
    }

    /**
     * Permet de mettre en place la date de création
     * 
     * @ORM\PrePersist
     * 
     * @return void
     */
    #[ORM\PrePersist]
    public function prePersist()
    {
        if (empty($this->creatAt)) {
            $timezone = new \DateTimeZone('Europe/London'); // Remplacez par le fuseau horaire approprié pour +1 heur
            $this->creatAt = new \Datetime('now', $timezone);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
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

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(int $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }


    public function getBrithAt(): ?\DateTimeInterface
    {
        return $this->brithAt;
    }

    public function setBrithAt(?\DateTimeInterface $brithAt): self
    {
        $this->brithAt = $brithAt;

        return $this;
    }


    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getTypeProspect(): ?string
    {
        return $this->typeProspect;
    }

    public function setTypeProspect(?string $typeProspect): self
    {
        $this->typeProspect = $typeProspect;

        return $this;
    }

    public function getRaisonSociale(): ?string
    {
        return $this->raisonSociale;
    }

    public function setRaisonSociale(?string $raisonSociale): self
    {
        $this->raisonSociale = $raisonSociale;

        return $this;
    }

    public function getCodePost(): ?string
    {
        return $this->codePost;
    }

    public function setCodePost(?string $codePost): self
    {
        $this->codePost = $codePost;

        return $this;
    }

    public function getGsm(): ?string
    {
        return $this->gsm;
    }

    public function setGsm(?string $gsm): self
    {
        $this->gsm = $gsm;

        return $this;
    }

    public function getAssure(): ?string
    {
        return $this->assure;
    }

    public function setAssure(?string $assure): self
    {
        $this->assure = $assure;

        return $this;
    }

    public function getLastAssure(): ?string
    {
        return $this->lastAssure;
    }

    public function setLastAssure(?string $lastAssure): self
    {
        $this->lastAssure = $lastAssure;

        return $this;
    }

    public function getMotifResil(): ?string
    {
        return $this->motifResil;
    }

    public function setMotifResil(?string $motifResil): self
    {
        $this->motifResil = $motifResil;

        return $this;
    }

    public function getAutor(): ?User
    {
        return $this->autor;
    }

    public function setAutor(?User $autor): self
    {
        $this->autor = $autor;

        return $this;
    }

    public function getComrcl(): ?User
    {
        return $this->comrcl;
    }

    public function setComrcl(?User $comrcl): self
    {
        $this->comrcl = $comrcl;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getMotifSaise(): ?string
    {
        return $this->motifSaise;
    }

    public function setMotifSaise(?string $motifSaise): self
    {
        $this->motifSaise = $motifSaise;

        return $this;
    }

    public function getCreatAt(): ?\DateTimeInterface
    {
        return $this->creatAt;
    }

    public function setCreatAt(\DateTimeInterface $creatAt): self
    {
        $this->creatAt = new \DateTime();

        return $this;
    }


    /**
     * @return Collection<int, Relanced>
     */
    public function getRelanceds(): Collection
    {
        return $this->relanceds;
    }

    public function addRelanced(Relanced $relanced): static
    {
        if (!$this->relanceds->contains($relanced)) {
            $this->relanceds->add($relanced);
            $relanced->setProspect($this);
        }

        return $this;
    }

    public function removeRelanced(Relanced $relanced): static
    {
        if ($this->relanceds->removeElement($relanced)) {
            // set the owning side to null (unless already changed)
            if ($relanced->getProspect() === $this) {
                $relanced->setProspect(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->getName() . ' ' . $this->getLastname();
    }

    /**
     * @return Collection<int, History>
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function addHistory(History $history): static
    {
        if (!$this->histories->contains($history)) {
            $this->histories->add($history);
            $history->setProspect($this);
        }

        return $this;
    }

    public function removeHistory(History $history): static
    {
        if ($this->histories->removeElement($history)) {
            // set the owning side to null (unless already changed)
            if ($history->getProspect() === $this) {
                $history->setProspect(null);
            }
        }

        return $this;
    }

    public function getActivites(): ?string
    {
        return $this->activites;
    }

    public function setActivites(?string $activites): static
    {
        $this->activites = $activites;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }
}
