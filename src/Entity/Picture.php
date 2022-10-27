<?php

namespace App\Entity;

use App\Entity\BaseUser;

use App\Repository\PictureRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PictureRepository::class)
 */
class Picture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $takenAt;

    /**
     * @ORM\ManyToOne(targetEntity=Carrier::class, inversedBy="picture")
     */
    private $carrier;

    /**
     * @ORM\OneToOne(targetEntity=Damage::class, mappedBy="picture")
     */
    private $damage;

    /**
     * @ORM\OneToOne(targetEntity=Assessment::class, mappedBy="signature")
     */
    private $assessment;

    /**
     * @ORM\OneToOne(targetEntity=BaseUser::class, mappedBy="picture")
     */
    private $user;

    public function __construct()
    {
    
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getTakenAt(): ?\DateTimeInterface
    {
        return $this->takenAt;
    }

    public function setTakenAt(\DateTimeInterface $takenAt): self
    {
        $this->takenAt = $takenAt;

        return $this;
    }

    public function getDamage(): ?Damage
    {
        return $this->damage;
    }

    public function setDamage(Damage $damage): self
    {
        $this->damage = $damage;

        return $this;
    }

    public function getAssessment(): ?Assessment
    {
        return $this->assessment;
    }

    public function setassessment(Assessment $assessment): self
    {
        $this->assessment = $assessment;

        return $this;
    }

    public function getUser(): ?BaseUser
    {
        return $this->user;
    }

    public function setUser(BaseUser $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCarrier(): ?Carrier
    {
        return $this->carrier;
    }

    public function setCarrier(Carrier $carrier): self
    {
        $this->carrier = $carrier;

        return $this;
    }

    public function toArray()
    {
        $array = get_object_vars($this);
        $array['takenAt'] = $array['takenAt']->format('c');
        unset($array['damage']);
        unset($array['assessment']);
        unset($array['damage']);
        unset($array['carrier']);
        unset($array['user']);
        return $array;
    }
}
