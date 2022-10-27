<?php

namespace App\Entity;

use App\Repository\AssessmentRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\BaseUser;
/**
 * @ORM\Entity(repositoryClass=AssessmentRepository::class)
 */
class Assessment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $notes;

    /**
     * @ORM\ManyToOne(targetEntity=BaseUser::class, inversedBy="assessment")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Carrier::class, inversedBy="assessment")
     */
    private $carrier;

    /**
     * @ORM\OneToMany(targetEntity=Damage::class, mappedBy="assessment")
     */
    private $damage;

    /**
     * @ORM\OneToOne(targetEntity=Picture::class, mappedBy="assessment")
     * @ORM\JoinColumn(nullable=true)
     */
    private $signature;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getSignature(): ?Picture
    {
        return $this->signature;
    }

    public function setSignatire(?Picture $signature): self
    {
        $this->signature = $signature;

        return $this;
    }


    public function setUser(BaseUser $user): ?self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser() :?BaseUser
    {
        return $this->user;
    }

    public function setCarrier(?Carrier $carrier) :self
    {
        $this->carrier = $carrier;

        return $this;
    }

    public function getCarrier() :?Carrier
    {
        return $this->carrier;
    }

    public function getDamages()
    {
        return $this->damage;
    }

    public function addDamage(Damage $damage): self
    {
        if (!$this->damage->contains($damage)) {
            $this->damage[] = $damage;
            $damage->setAssessment($this);
        }
        return $this;
    }

    public function toArray()
    {
        $array = get_object_vars($this);
        $out = array();
        foreach ($this->damage as $damage)
        {
            array_push($out, $damage->toArray());
        }
        $array['carrier'] = $this->carrier->toArray();
        $array['damage'] = $out;
        $array['user'] = $this->user->toArray();
        $array['createdAt'] = $array['createdAt']->format('c');
        return $array;
    }
}
