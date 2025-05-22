<?php

namespace App\Entity;

use App\Repository\GarageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GarageRepository::class)]
class Garage
{
    #[OA\Property(type: 'string', format: 'uuid', description: 'Identifiant unique du garage')]
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'string', length: 36)]
    #[Groups(['appointment:read', 'garage:read'])]
    private ?string $id = null;

    #[OA\Property(type: 'string', description: 'Nom du garage')]
    #[ORM\Column(name: 'name', length: 255)]
    #[Groups(['garage:read'])]
    private ?string $name = null;

    #[OA\Property(type: 'string', description: 'Ville du garage')]
    #[ORM\Column(name: 'city', length: 255)]
    #[Groups(['garage:read'])]
    private ?string $city = null;

    #[OA\Property(type: 'string', description: 'Code postal du garage', maxLength: 5)]
    #[ORM\Column(name: 'postal_code', length: 5)]
    #[Groups(['garage:read'])]
    private ?string $postalCode = null;

    #[OA\Property(type: 'number', format: 'float', description: 'Latitude du garage')]
    #[ORM\Column(name: 'latitude')]
    #[Groups(['garage:read'])]
    private ?float $latitude = null;

    #[OA\Property(type: 'number', format: 'float', description: 'Longitude du garage')]
    #[ORM\Column(name: 'longitude')]
    #[Groups(['garage:read'])]
    private ?float $longitude = null;

    #[OA\Property(type: 'string', format: 'date-time', description: 'Date de création du garage')]
    #[ORM\Column(name: 'created_at')]
    #[Groups(['garage:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[OA\Property(type: 'string', format: 'date-time', description: 'Date de dernière mise à jour du garage')]
    #[ORM\Column(name: 'updated_at')]
    #[Groups(['garage:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Appointment>
     */
    #[OA\Property(
        type: 'array',
        description: 'Liste des rendez-vous associés au garage',
        items: new OA\Items(ref: '#/components/schemas/Appointment')
    )]
    #[ORM\OneToMany(targetEntity: Appointment::class, mappedBy: 'garage', orphanRemoval: true)]
    private Collection $appointments;

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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;
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
            $appointment->setGarage($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): static
    {
        if ($this->appointments->removeElement($appointment)) {
            if ($appointment->getGarage() === $this) {
                $appointment->setGarage(null);
            }
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
}
