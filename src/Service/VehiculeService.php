<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\VehiculeRepository;

class VehiculeService
{
    public function __construct(
        private VehiculeRepository $repository
    ) {}

    public function getVehiculesByUser(User $user): array
    {
        return $this->repository->findByUser($user->getId());
    }

    public function getVehiculeById(string $id): ?Vehicule
    {
        return $this->repository->find($id);
    }

    public function getNextOperations(Vehicule $vehicule): array
    {
        $operations = [
            ['label' => 'Purge liquide de refroidissement', 'unit' => 'days', 'range' => [30, 60]],
            ['label' => 'Purge des liquides de frein', 'unit' => 'days', 'range' => [30, 60]],
            ['label' => 'Usure pneu arrière', 'unit' => 'km', 'range' => [1000, 2000]],
            ['label' => 'Pression des pneus', 'unit' => 'days', 'range' => [5, 30]],
            ['label' => 'Usure pneu avant', 'unit' => 'km', 'range' => [3000, 5000]],
            ['label' => 'Contrôle plaquettes et disques', 'unit' => 'km', 'range' => [5000, 7000]],
            ['label' => 'Graissage de la chaîne', 'unit' => 'km', 'range' => [300, 800]],
            ['label' => 'Tension de la chaîne', 'unit' => 'days', 'range' => [100, 200]],
            ['label' => 'Vidange', 'unit' => 'days', 'range' => [300, 400]],
            ['label' => 'Entretien annuel', 'unit' => 'days', 'range' => [300, 400]],
        ];

        $mockedOperations = array_map(function ($op) {
            return [
                'label' => $op['label'],
                'next_in_value' => random_int($op['range'][0], $op['range'][1]),
                'next_in_unit' => $op['unit'],
                'criticality' => random_int(1, 9)
            ];
        }, $operations);

        return $mockedOperations;
    }
}
