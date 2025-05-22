<?php

namespace App\Controller;

use App\Entity\OperationCategory;
use App\Service\OperationCategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

#[Route(path: '/api/operations')]
final class OperationCategoryController extends AbstractController
{
    #[Route('/category', name: 'app_operation_category_get_all', methods: ['GET'])]
    #[OA\Get(
        path: '/api/operations/category',
        summary: 'Récupère toutes les catégories d\'opérations',
        description: 'Retourne l\'ensemble des catégories d\'opérations disponibles.',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des catégories d\'opérations trouvées',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: OperationCategory::class, groups: ['operation_category:read']))
                )
            ),
        ]
    )]
    public function getCategories(OperationCategoryService $operationCategoryService): JsonResponse
    {
        $operations = $operationCategoryService->getAllOperationCategories();

        return $this->json($operations, context: [
            'groups' => ['operation_category:read'],
        ]);
    }

    #[Route('/category/{id}', name: 'app_operation_category_get_by_id', methods: ['GET'])]
    #[OA\Get(
        path: '/api/operations/category/{id}',
        summary: 'Récupère une catégorie d\'opération par son identifiant',
        description: 'Retourne la catégorie d\'opération correspondant à l\'identifiant donné.',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Catégorie d\'opération trouvée',
                content: new OA\JsonContent(
                    ref: new Model(type: OperationCategory::class, groups: ['operation_category:read'])
                )
            ),
        ]
    )]
    public function getCategoryById(OperationCategoryService $operationCategoryService, string $id): JsonResponse
    {
        $operation = $operationCategoryService->getOperationCategoryById($id);

        return $this->json($operation, context: [
            'groups' => ['operation_category:read'],
        ]);
    }
}
