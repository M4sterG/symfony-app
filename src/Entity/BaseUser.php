<?php

namespace App\Entity;

use App\Repository\BaseUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=BaseUserRepository::class)
 */
class BaseUser implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $cf;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $username;
    /**
     * @ORM\Column(type="string", length=30)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $license;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=UserAuthToken::class, mappedBy="user")
     */
    private $authToken;

    /**
     * @ORM\OneToOne(targetEntity=Picture::class, mappedBy="user")
     * @ORM\JoinColumn(nullable=true)
     */
    private $picture;

    /**
     * @ORM\OneToMany(targetEntity=Assessment::class, mappedBy="user")
     */
    private $assessment;

    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->isActive = true;
        //DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCf(): ?string
    {
        return $this->cf;
    }

    public function setCf(string $cf): self
    {
        $this->cf = $cf;

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

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

    public function getLicense(): ?string
    {
        return $this->license;
    }

    public function setLicense(string $license): self
    {
        $this->license = $license;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function isAdmin(): bool 
    {
        return (array_search('ROLE_ADMIN', $this->roles) !== false);
    }

    public function setIsActive(bool $active) {
        $this->isActive = $active;

        return $this;
    }

    public function getIsActive(): bool {
        
        return $this->isActive;
    }

    public function getAssessment()
    {
        return $this->assessment;
    }

    public function addAssessment(Assessment $assessment)
    {
        if (!$this->assessment->contains($assessment)) {
            $this->assessment[] = $assessment;
            $assessment->setUser($this);
        }
    }

    public function getAuthToken()
    {
        return $this->authToken;
    }

    public function addAuthToken(UserAuthToken $authToken)
    {
        if (!$this->authToken->contains($authToken)) {
            $this->authToken[] = $authToken;
            $authToken->setUser($this);
        }
    }

    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    public function setPicture(Picture $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function toArray()
    {
        $array = get_object_vars($this);
        $array['createdAt'] = $array['createdAt']->format('c');
        if($array['picture'] !== null) { 
            $array['picture'] = $this->picture->toArray();
        }
        unset($array['password']);
        unset($array['authToken']);
        unset($array['assessment']);
        unset($array['__initializer__']);
        unset($array['__cloner__']);
        unset($array['__isInitialized__']);
        
        return $array;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}

