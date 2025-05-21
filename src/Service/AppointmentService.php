<?php

namespace App\Service;

use App\Entity\Appointment;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Vehicule;
use App\Entity\Garage;
use App\Entity\Operation;
use App\Entity\User;
use App\Repository\AppointmentRepository;
use App\Repository\GarageRepository;
use App\Repository\VehiculeRepository;

class AppointmentService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GarageRepository $garageRepository,
        private VehiculeRepository $vehiculeRepository,
        private AppointmentRepository $appointmentRepository
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

    public function createAppointment(array $data): array
    {
        if (!isset($data['date'], $data['status'], $data['notes'], $data['vehicule_id'], $data['garage_id'], $data['operations'])) {
            throw new \InvalidArgumentException('Missing required data for creating an appointment.');
        }

        $appointment = new Appointment();

        $appointment->setDate(new \DateTimeImmutable($data['date']));
        $appointment->setStatus($data['status']);
        $appointment->setNotes($data['notes']);

        $vehicule = $this->vehiculeRepository->find($data['vehicule_id']);
        $garage = $this->garageRepository->find($data['garage_id']);

        if (!$vehicule || !$garage) {
            throw new \RuntimeException('Vehicule or Garage not found.');
        }
        $appointment->setVehicule($vehicule);
        $appointment->setGarage($garage);

        foreach ($data['operations'] as $operationId) {
            $operation = $this->entityManager->getRepository(Operation::class)->find($operationId);
            if ($operation) {
                $appointment->addOperation($operation);
            }
        }

        $this->entityManager->persist($appointment);
        $this->entityManager->flush();

        return [
            'id' => $appointment->getId(),
            'date' => $appointment->getDate()->format('Y-m-d H:i:s'),
            'status' => $appointment->getStatus(),
            'notes' => $appointment->getNotes(),
            'created_at' => $appointment->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $appointment->getUpdatedAt()->format('Y-m-d H:i:s'),
            'vehicule_id' => $appointment->getVehicule()->getId(),
            'garage_id' => $appointment->getGarage()->getId(),
            'operations' => array_map(function ($operation) {
                return $operation->getId();
            }, $appointment->getOperations()->toArray())
        ];
    }

    public function getAppointmentByVehicule(Vehicule $vehicule): array
    {
        return $this->appointmentRepository->findBy(['vehicule' => $vehicule]);
    }
}
