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
    #[ORM\Column(type: 'string', length: 36)]
    #[Groups(['operation:read'])]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['operation:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['operation:read'])]
    private ?string $additionnal_help = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['operation:read'])]
    private ?string $additionnal_comment = null;

    #[ORM\Column]
    #[Groups(['operation:read'])]
    private ?int $time_unit = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['operation:read'])]
    private ?string $price = null;

    #[ORM\Column]
    #[Groups(['operation:read'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(['operation:read'])]
    private ?\DateTimeImmutable $updated_at = null;

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
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
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
        return $this->additionnal_help;
    }

    public function setAdditionnalHelp(?string $additionnal_help): static
    {
        $this->additionnal_help = $additionnal_help;

        return $this;
    }

    public function getAdditionnalComment(): ?string
    {
        return $this->additionnal_comment;
    }

    public function setAdditionnalComment(?string $additionnal_comment): static
    {
        $this->additionnal_comment = $additionnal_comment;

        return $this;
    }

    public function getTimeUnit(): ?int
    {
        return $this->time_unit;
    }

    public function setTimeUnit(int $time_unit): static
    {
        $this->time_unit = $time_unit;

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
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

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
