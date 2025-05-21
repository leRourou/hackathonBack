<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\VehiculeRepository;

class VehiculeService 
{
    public function __construct(
        private VehiculeRepository $repository
    )
    { }

    public function getVehiculeByUser(User $user): array
    {
        return $this->repository->findByUser($user->getId());
    }

}

