<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Vehicule;
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
}
