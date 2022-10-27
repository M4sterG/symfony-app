<?php

namespace App\Entity;

use App\Repository\DamageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DamageRepository::class)
 */
class Damage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $relativePosition;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=13, nullable=true)
     */
    private $actionRequired;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\OneToOne(targetEntity=Picture::class, mappedBy="damage")
     * @ORM\JoinColumn(nullable=true)
     */
    private $picture;

    /**
     * @ORM\ManyToOne(targetEntity=Assessment::class, inversedBy="damage")
     */
    private $assessment;

    public function __construct(){
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getRelativePosition(): ?string
    {
        return $this->relativePosition;
    }

    public function setRelativePosition(string $relativePosition): self
    {
        $this->relativePosition = $relativePosition;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getActionRequired(): ?string
    {
        return $this->actionRequired;
    }

    public function setActionRequired(?string $actionRequired): self
    {
        $this->actionRequired = $actionRequired;

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

    public function setAssessment(Assessment $assessment): self
    {
        $this->assessment = $assessment;

        return $this;
    }

    public function getAssessment(): Assessment
    {
        return $this->assessment;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture(Picture $picture): self
    {
        if (!$this->picture->contains($picture)) {
            $this->picture[] = $picture;
            $picture->setDamage($this);
        }
        return $this;
    }


    public function toArray()
    {
        $array = get_object_vars($this);
        if ($array['picture'] !== null) {
            $array['picture'] = $this->picture->toArray();
        }    
        unset($array['assessment']);
        //$array['createdAt'] = $array['createdAt']->format('c');
        
        return $array;
    }
}
