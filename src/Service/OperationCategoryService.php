<?php

namespace App\Service;

use App\Repository\OperationCategoryRepository;
use App\Entity\OperationCategory;

class OperationCategoryService
{

    public function __construct(
        private OperationCategoryRepository $repository
    ) {}


    public function getAllOperationCategories(): array
    {
        return $this->repository->findAll();
    }

    public function getOperationCategoryById(string $id): ?OperationCategory
    {
        return $this->repository->find($id);
    }
}
