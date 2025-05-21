<?php

namespace App\Controller;

use App\Service\OperationCategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route(path: '/api/operations')]
final class OperationCategoryController extends AbstractController
{
    #[Route('/category', name: 'app_operation_category_get_all', methods: ['GET'])]
    #[OA\Get(
        path: '/api/operations/category',
        summary: 'Récupère toutes les catégories d\'opérations',
        description: 'Retourne l’ensemble des catégories d’opérations disponibles.'
    )]
    public function getCategories(OperationCategoryService $operationCategoryService): JsonResponse
    {
        $operations = $operationCategoryService->getAllOperationCategories();

        return $this->json($operations, context: [
            'groups' => ['operation_category:read'],
        ]);
    }

    #[Route('/category/{id}', name: 'app_operation_category_get_by_id', methods: ['GET'])]
    public function getCategoryById(OperationCategoryService $operationCategoryService, string $id): JsonResponse
    {
        $operation = $operationCategoryService->getOperationCategoryById($id);

        return $this->json($operation, context: [
            'groups' => ['operation_category:read'],
        ]);
    }
}
