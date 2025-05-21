<?php

namespace App\Entity;

use App\Repository\GarageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[OA\Schema(schema: "Garage", description: "Garage avec ses coordonnées et informations de localisation")]
#[ORM\Entity(repositoryClass: GarageRepository::class)]
class Garage
{
    #[OA\Property(
        type: 'string',
        format: 'uuid',
        description: 'Identifiant unique du garage'
    )]
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private ?string $id = null;

    #[OA\Property(
        type: 'string',
        description: 'Nom du garage'
    )]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[OA\Property(
        type: 'string',
        description: 'Ville du garage'
    )]
    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[OA\Property(
        type: 'string',
        description: 'Code postal du garage',
        maxLength: 5
    )]
    #[ORM\Column(length: 5)]
    private ?string $postal_code = null;

    #[OA\Property(
        type: 'number',
        format: 'float',
        description: 'Latitude du garage'
    )]
    #[ORM\Column]
    private ?float $latitude = null;

    #[OA\Property(
        type: 'number',
        format: 'float',
        description: 'Longitude du garage'
    )]
    #[ORM\Column]
    private ?float $longitude = null;

    #[OA\Property(
        type: 'string',
        format: 'date-time',
        description: 'Date de création du garage'
    )]
    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[OA\Property(
        type: 'string',
        format: 'date-time',
        description: 'Date de dernière mise à jour du garage'
    )]
    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

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
        return $this->postal_code;
    }

    public function setPostalCode(string $postal_code): static
    {
        $this->postal_code = $postal_code;

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
            // set the owning side to null (unless already changed)
            if ($appointment->getGarage() === $this) {
                $appointment->setGarage(null);
            }
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
}
