<?php

namespace App\Service;

use App\Entity\Garage;
use App\Repository\GarageRepository;
use Doctrine\ORM\EntityManagerInterface;

class GarageService
{
    private GarageRepository $garageRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        /** @var GarageRepository $repository */
        $repository = $entityManager->getRepository(Garage::class);
        $this->garageRepository = $repository;
    }

    public function getAllGarages(): array
    {
        return $this->garageRepository->findAll();
    }

    public function findNearestGarages($lat, $lng, $page): array
    {
        return $this->garageRepository->findNearestGarages($lat, $lng, $page);
    }
}
