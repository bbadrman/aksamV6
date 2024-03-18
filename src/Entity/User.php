<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use ORM\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: "user")]
#[UniqueEntity('username', message: "Ce nom d'utilisateur a déjà été utilisé!")]
#[ApiResource]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;


    #[Assert\NotBlank(message: "Le nom d'utilisateur est obligatoire")]
    #[Assert\Length(
        min: 4,
        max: 15,
        minMessage: "Le nom d'utilisateur doit contenir au moins quatre caractères",
        maxMessage: "Le nom d'utilisateur doit contenir au maximum quinze caractères"
    )]
    #[Assert\Regex(
        pattern: "/^[a-z0-9_-]{3,15}$/",
        message: "Votre prénom ne doit pas contenir d'espaces, de virgules, de points-virgules ou de deux-points"
    )]

    #[ORM\Column(type: "string", length: 180, nullable: false, unique: true)]
    #[Assert\Type(type: "string", message: "Le nom d'utilisateur doit être une chaîne de caractères")]
    private $username;

    #[ORM\Column(type: "json")]
    private $roles = [];

    #[ORM\Column(type: "string", length: 255)]
    private $password;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\Length(
        min: 2,
        max: 10,
        minMessage: "Le prénom doit contenir au moins deux caractères",
        maxMessage: "Le prénom doit contenir au maximum dix caractères"
    )]
    #[Assert\Regex(
        pattern: "/^[^\s,;:]+$/",
        message: "Votre prénom ne doit pas contenir d'espaces, de virgules, de points-virgules ou de deux-points"
    )]
    private $firstname;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\Length(
        min: 2,
        max: 10,
        minMessage: "Le nom doit contenir au moins deux caractères",
        maxMessage: "Le nom doit contenir au maximum dix caractères"
    )]
    #[Assert\Regex(
        pattern: "/^[^\s,;:]+$/",
        message: "Le nom ne doit pas contenir d'espaces, de virgules, de points-virgules ou de deux-points"
    )]
    private $lastname;

    #[ORM\Column(type: "smallint", nullable: true)]
    private $gender;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $embuchAt;

    #[ORM\Column(type: "integer", nullable: true)]
    private $remuneration;

    #[ORM\ManyToMany(targetEntity: Fonction::class, inversedBy: "users", cascade: ["persist"])]

    private $fonctions;

    #[ORM\Column(type: "smallint")]
    private $status = true;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: "users", cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: true)]

    private $teams;

    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: "users", cascade: ["persist"])]

    private $products;

    #[ORM\OneToMany(targetEntity: Prospect::class, mappedBy: "autor")]

    private $prospects;

    #[ORM\OneToMany(targetEntity: Prospect::class, mappedBy: "comrcl")]
    #[ORM\JoinColumn(nullable: true)]


    private $prospections;

    #[ORM\OneToMany(mappedBy: 'cmrl', targetEntity: Client::class)]
    private Collection $clients;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $creatAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastLoginDate = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Acces::class)]
    private Collection $acces;




    public function __construct()
    {
        $this->fonctions = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->prospects = new ArrayCollection();
        $this->prospections = new ArrayCollection();
        $this->clients = new ArrayCollection();
        $this->acces = new ArrayCollection();
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
            $timezone = new \DateTimeZone('Europe/Paris'); // Remplacez par le fuseau horaire approprié pour +1 heur
            $this->creatAt = new \Datetime('now', $timezone);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function isGender(): ?bool
    {
        return $this->gender;
    }

    public function setGender(bool $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getEmbuchAt(): ?\DateTimeInterface
    {
        return $this->embuchAt;
    }

    public function setEmbuchAt(\DateTimeInterface $embuchAt): self
    {
        $this->embuchAt = $embuchAt;

        return $this;
    }

    public function getRemuneration(): ?int
    {
        return $this->remuneration;
    }

    public function setRemuneration(int $remuneration): self
    {
        $this->remuneration = $remuneration;

        return $this;
    }

    /**
     * @return Collection<int, Fonction>
     */
    public function getFonctions(): Collection
    {
        return $this->fonctions;
    }

    public function addFonction(Fonction $fonction): self
    {
        if (!$this->fonctions->contains($fonction)) {
            $this->fonctions[] = $fonction;
            $fonction->addUser($this);
        }

        return $this;
    }

    public function removeFonction(Fonction $fonction): self
    {
        if ($this->fonctions->removeElement($fonction)) {
            $fonction->removeUser($this);
        }

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTeams(): ?Team
    {
        return $this->teams;
    }

    public function setTeams(?Team $teams): self
    {
        $this->teams = $teams;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        $this->products->removeElement($product);

        return $this;
    }





    public function __toString()
    {
        return strval($this->getUserIdentifier());
    }

    /**
     * @return Collection<int, Prospect>
     */
    public function getProspects(): Collection
    {
        return $this->prospects;
    }

    public function addProspect(Prospect $prospect): self
    {
        if (!$this->prospects->contains($prospect)) {
            $this->prospects[] = $prospect;
            $prospect->setAutor($this);
        }

        return $this;
    }

    public function removeProspect(Prospect $prospect): self
    {
        if ($this->prospects->removeElement($prospect)) {
            // set the owning side to null (unless already changed)
            if ($prospect->getAutor() === $this) {
                $prospect->setAutor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Prospect>
     */
    public function getProspections(): Collection
    {
        return $this->prospections;
    }

    public function addProspection(Prospect $prospection): self
    {
        if (!$this->prospections->contains($prospection)) {
            $this->prospections[] = $prospection;
            $prospection->setComrcl($this);
        }

        return $this;
    }

    public function removeProspection(Prospect $prospection): self
    {
        if ($this->prospections->removeElement($prospection)) {
            // set the owning side to null (unless already changed)
            if ($prospection->getComrcl() === $this) {
                $prospection->setComrcl(null);
            }
        }

        return $this;
    }


    //  /**
    //  * @return int
    //  */
    // public function getProspectCount(): int
    // {
    //     // Utilisez la méthode count() de la collection prospects pour obtenir le nombre de prospects
    //     return $this->prospects->count();
    // }

    /**
     * @return Collection<int, Client>
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): static
    {
        if (!$this->clients->contains($client)) {
            $this->clients->add($client);
            $client->setCmrl($this);
        }

        return $this;
    }

    public function removeClient(Client $client): static
    {
        if ($this->clients->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getCmrl() === $this) {
                $client->setCmrl(null);
            }
        }

        return $this;
    }

    public function getCreatAt(): ?\DateTimeInterface
    {
        return $this->creatAt;
    }

    public function setCreatAt(\DateTimeInterface $creatAt): static
    {
        $this->creatAt = new DateTime();

        return $this;
    }

    public function getLastLoginDate(): ?\DateTimeInterface
    {
        return $this->lastLoginDate;
    }

    public function setLastLoginDate(?\DateTimeInterface $lastLoginDate): static
    {
        $this->lastLoginDate = $lastLoginDate;

        return $this;
    }

    /**
     * @return Collection<int, Acces>
     */
    public function getAcces(): Collection
    {
        return $this->acces;
    }

    public function addAcce(Acces $acce): static
    {
        if (!$this->acces->contains($acce)) {
            $this->acces->add($acce);
            $acce->setUser($this);
        }

        return $this;
    }

    public function removeAcce(Acces $acce): static
    {
        if ($this->acces->removeElement($acce)) {
            // set the owning side to null (unless already changed)
            if ($acce->getUser() === $this) {
                $acce->setUser(null);
            }
        }

        return $this;
    }
}
