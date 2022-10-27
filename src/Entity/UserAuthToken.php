<?php

namespace App\Entity;

use App\Repository\UserAuthTokenRepository;
use DateInterval;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserAuthTokenRepository::class)
 */
class UserAuthToken
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $authToken;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiresAt;

    /**
     * @ORM\Column(type="string", length=36)
     */
    private $uuid;

    /**
     * @ORM\ManyToOne(targetEntity=BaseUser::class, inversedBy="authToken")
     */
    private $user; 

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->authToken = $this->genstr(64);
        $this->expiresAt = $this->createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthToken(): ?string
    {
        return $this->authToken;
    }

    public function setAuthToken(string $authToken): self
    {
        $this->authToken = $authToken;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uudid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
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

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function setUser(?BaseUser $user) :self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser() :?BaseUser
    {
        return $this->user;
    }

    private function genstr(int $len)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        if ($len > 0 && $len < 65)
            for ($i = 0; $i < $len; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }

        return $randomString;
    }
}
