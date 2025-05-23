<?php

namespace App\Entity;

use App\Repository\OperationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: OperationRepository::class)]
class Operation
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'string', length: 36)]
    #[Groups(['operation:read', 'appointment:read'])]
    private ?string $id = null;

    #[ORM\Column(name: 'name', length: 255)]
    #[Groups(['operation:read', 'appointment:read'])]
    private ?string $name = null;

    #[ORM\Column(name: 'additionnal_help', type: Types::TEXT, nullable: true)]
    #[Groups(['operation:read'])]
    private ?string $additionnalHelp = null;

    #[ORM\Column(name: 'additionnal_comment', type: Types::TEXT, nullable: true)]
    #[Groups(['operation:read'])]
    private ?string $additionnalComment = null;

    #[ORM\Column(name: 'time_unit')]
    #[Groups(['operation:read'])]
    private ?int $timeUnit = null;

    #[ORM\Column(name: 'price', type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['operation:read'])]
    private ?string $price = null;

    #[ORM\Column(name: 'created_at')]
    #[Groups(['operation:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'updated_at')]
    #[Groups(['operation:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Appointment>
     */
    #[ORM\ManyToMany(targetEntity: Appointment::class, mappedBy: 'operations')]
    private Collection $appointments;

    #[ORM\ManyToOne(targetEntity: OperationCategory::class, inversedBy: 'operations')]
    #[ORM\JoinColumn(nullable: false)]
    private OperationCategory $category;

    public function __construct()
    {
        $this->appointments = new ArrayCollection();
        $this->id = Uuid::v7()->toRfc4122();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getAdditionnalHelp(): ?string
    {
        return $this->additionnalHelp;
    }

    public function setAdditionnalHelp(?string $additionnalHelp): static
    {
        $this->additionnalHelp = $additionnalHelp;
        return $this;
    }

    public function getAdditionnalComment(): ?string
    {
        return $this->additionnalComment;
    }

    public function setAdditionnalComment(?string $additionnalComment): static
    {
        $this->additionnalComment = $additionnalComment;
        return $this;
    }

    public function getTimeUnit(): ?int
    {
        return $this->timeUnit;
    }

    public function setTimeUnit(int $timeUnit): static
    {
        $this->timeUnit = $timeUnit;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): static
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments->add($appointment);
            $appointment->addOperation($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): static
    {
        if ($this->appointments->removeElement($appointment)) {
            $appointment->removeOperation($this);
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    #[Groups(['operation:read'])]
    #[SerializedName('category')]
    public function getCategoryId(): ?string
    {
        return $this->category?->getId();
    }

    public function getCategory(): ?OperationCategory
    {
        return $this->category;
    }

    public function setCategory(?OperationCategory $category): self
    {
        $this->category = $category;
        return $this;
    }
}
