<?php

namespace App\Entity;

use App\Repository\CarrierRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Assessment;

/**
 * @ORM\Entity(repositoryClass=CarrierRepository::class)
 */
class Carrier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $licensePlate;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $immatriculation;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $vin;

    /**
     * @ORM\OneToMany(targetEntity=Picture::class, mappedBy="carrier")
     * @ORM\JoinColumn(nullable=true)
     */
    private $picture;
    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isInUse;

    /**
     * @ORM\OneToMany(targetEntity=Assessment::class, mappedBy="carrier")
     */
    private $assessment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLicensePlate(): ?string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $licensePlate): self
    {
        $this->licensePlate = $licensePlate;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): self
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getVin(): ?string
    {
        return $this->vin;
    }

    public function setVin(string $vin): self
    {
        $this->vin = $vin;

        return $this;
    }

    public function setIsActive(bool $isActive): self
    {   
        $this->isActive = $isActive;
        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsInUse(bool $isInUse): self
    {   
        $this->isInUse = $isInUse;
        return $this;
    }

    public function getIsInUse(): ?bool
    {
        return $this->isInUse;
    }

    public function getAssessment() :Assessment
    {
        return $this->assessment;
    }

    public function addAssessment(Assessment $assessment): self
    {
        if (!$this->assessment->contains($assessment)) {
            $this->assessment[] = $assessment;
            $assessment->setCarrier($this);
        }
        return $this;
    }

    public function getPicture() :string
    {
        return $this->picture;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->picture->contains($picture)) {
            $this->picture[] = $picture;
            $picture->setCarrier($this);
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
        unset($array['__initializer__']);
        unset($array['__cloner__']);
        unset($array['__isInitialized__']);
        return $array;
    }
}
