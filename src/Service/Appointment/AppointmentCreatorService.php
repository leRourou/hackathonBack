<?php

namespace App\Service\Appointment;

use App\Entity\Appointment;
use App\DTO\CreateAppointmentDTO;
use App\Repository\GarageRepository;
use App\Repository\VehiculeRepository;
use App\Repository\OperationRepository;
use App\Exception\Appointment\GarageNotFoundException;
use App\Exception\Appointment\VehiculeNotFoundException;
use App\Exception\Appointment\OperationNotFoundException;

class AppointmentCreatorService {

    public function __construct(
        private readonly VehiculeRepository $vehiculeRepository,
        private readonly GarageRepository $garageRepository,
        private readonly OperationRepository $operationRepository
    ) { }


    public function createFromDto(CreateAppointmentDTO $dto): Appointment
    {
        $appointment = new Appointment();
        $appointment->setDate($dto->date);
        $appointment->setStatus($dto->status);
        $appointment->setNotes($dto->notes);

        $this->attachVehicule($appointment, $dto->vehiculeId);
        $this->attachGarage($appointment, $dto->garageId);
        $this->attachOperations($appointment, $dto->operationIds);

        return $appointment;
    }

    private function attachVehicule(Appointment $appointment, string $vehiculeId): void
    {
        $vehicule = $this->vehiculeRepository->find($vehiculeId);
        if (!$vehicule) {
            throw new VehiculeNotFoundException($vehiculeId);
        }
        $appointment->setVehicule($vehicule);
    }

    private function attachGarage(Appointment $appointment, string $garageId): void
    {
        $garage = $this->garageRepository->find($garageId);
        if (!$garage) {
            throw new GarageNotFoundException($garageId);
        }
        $appointment->setGarage($garage);
    }

    private function attachOperations(Appointment $appointment, array $operationIds): void
    {
        foreach ($operationIds as $operationId) {
            $operation = $this->operationRepository->find($operationId);
            if (!$operation) {
                throw new OperationNotFoundException($operationId);
            }
            $appointment->addOperation($operation);
        }
    }
}
