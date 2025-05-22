<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Vehicule;
use App\Entity\Appointment;
use App\DTO\CreateAppointmentDTO;
use App\Repository\GarageRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AppointmentRepository;
use App\Service\Appointment\AppointmentCreatorService;

class AppointmentService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GarageRepository $garageRepository,
        private VehiculeRepository $vehiculeRepository,
        private AppointmentRepository $appointmentRepository,
        private readonly AppointmentCreatorService $appointmentCreator,

    ) {}

    public function getAvailabilities(int $page): array
    {
        $daysPerPage = 5;

        $startDate = (new \DateTime())->modify('+' . (($page - 1) * $daysPerPage) . ' days');
        $availabilities = [];

        for ($i = 0; $i < $daysPerPage; $i++) {
            $date = clone $startDate;
            $date->modify("+{$i} days");

            $slots = $this->generateRandomSlots();

            $availabilities[] = [
                'date' => $date->format('Y-m-d'),
                'slots' => $slots
            ];
        }
        return $availabilities;
    }

    public function getDateAvailabilities(string $date): array
    {
        $slots = $this->generateRandomSlots();

        return [
            'date' => $date,
            'slots' => $slots
        ];
    }

    public function generateRandomSlots(): array
    {
        $possibleSlots = [
            '08:00',
            '09:00',
            '10:00',
            '11:00',
            '12:00',
            '13:00',
            '14:00',
            '15:00',
            '16:00',
            '17:00'
        ];

        shuffle($possibleSlots);
        $count = random_int(0, 6);
        return array_slice($possibleSlots, 0, $count);
    }

    public function createAppointment(CreateAppointmentDTO $dto): Appointment
    {
        $appointment = $this->appointmentCreator->createFromDTO($dto);
        
        $this->entityManager->persist($appointment);
        $this->entityManager->flush();

        return $appointment;
    }

    public function getAppointmentsByVehicule(Vehicule $vehicule): array
    {
        return $this->appointmentRepository->findBy(['vehicule' => $vehicule]);
    }

    public function getAppointmentsByUser(User $user)
    {
        $vehicules = $this->vehiculeRepository->findByUser($user->getId());

        $appointments = [];

        foreach ($vehicules as $vehicule) {

            $vehiculeAppointments = $this->getAppointmentsByVehicule($vehicule);

            if (empty($vehiculeAppointments)) continue;

            $appointments = array_merge($appointments, $vehiculeAppointments);
        }

        return $appointments;
    }

    public function getAppointmentById(string $id): ?Appointment
    {
        return $this->appointmentRepository->find($id);
    }
}
