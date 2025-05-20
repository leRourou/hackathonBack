<?php

namespace App\Service;

use App\Repository\OperationCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\OperationCategory;

class OperationCategoryService
{

    private OperationCategoryRepository $operationCategoryRepository;
    public function __construct(EntityManagerInterface $entityManager)
    {
        /** @var OperationCategoryRepository $repository */
        $repository = $entityManager->getRepository(OperationCategory::class);
        $this->operationCategoryRepository = $repository;
    }

    public function getAllOperationCategories(): array
    {
        return $this->operationCategoryRepository->findAll();
    }
}
