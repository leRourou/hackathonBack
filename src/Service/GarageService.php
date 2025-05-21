<?php

namespace App\Service;

use App\Repository\GarageRepository;

class GarageService
{
    public function __construct(
        private GarageRepository $repository
    ) {}

    public function getAllGarages(): array
    {
        return $this->repository->findAll();
    }

    public function findNearestGarages($lat, $lng, $page): array
    {
        return $this->repository->findNearestGarages($lat, $lng, $page);
    }
}
