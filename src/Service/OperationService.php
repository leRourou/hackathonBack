<?php

namespace App\Service;

use App\Entity\Operation;
use App\Repository\OperationRepository;
use Doctrine\ORM\EntityManagerInterface;

class OperationService
{
    private OperationRepository $operationRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        /** @var OperationRepository $repository */
        $repository = $entityManager->getRepository(Operation::class);
        $this->operationRepository = $repository;
    }

    public function getAllOperations(): array
    {
        return $this->operationRepository->findAll();
    }

    public function getOperationsByCategory(string $categoryid): array
    {
        return $this->operationRepository->findBy(['category' => $categoryid]);
    }
}
