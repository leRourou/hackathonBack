<?php

namespace App\Controller;

use App\Service\OperationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class OperationController extends AbstractController
{

    #[Route('/operations', name: 'app_operations_get_all')]
    public function getAll(OperationService $operationService): JsonResponse
    {
        $operations = $operationService->getAllOperations();
        return $this->json($operations, context: [
            'groups' => ['operation:read'],
        ]);
    }

    #[Route('/operations/{categoryId}', name: 'app_operations_get_by_category')]
    public function getByCategory(OperationService $operationService, string $categoryId): JsonResponse
    {
        $operations = $operationService->getOperationsByCategory($categoryId);
        return $this->json($operations, context: [
            'groups' => ['operation:read'],
        ]);
    }
}
