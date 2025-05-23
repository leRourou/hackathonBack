<?php

namespace App\Controller;

use App\Service\OperationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Nelmio\ApiDocBundle\Attribute\Model;
use App\Entity\Operation;
use OpenApi\Attributes as OA;

#[Route(path: '/api/operations')]
final class OperationController extends AbstractController
{

    #[Route('/', name: 'app_operations_get_all', methods: ['GET'])]
    #[OA\Get(
        path: '/api/operations/',
        summary: 'Récupère toutes les opérations',
        description: "Retourne la liste complète de toutes les opérations existantes.",
        tags: ['Opération'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des opérations trouvées',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Operation::class, groups: ['operation:read']))
                )
            ),
        ]
    )]
    public function getAll(OperationService $operationService): JsonResponse
    {
        $operations = $operationService->getAllOperations();
        return $this->json($operations, context: [
            'groups' => ['operation:read'],
        ]);
    }

    #[Route('/{categoryId}', name: 'app_operations_get_by_category', methods: ['GET'])]
    #[OA\Get(
        path: '/api/operations/{categoryId}',
        summary: 'Récupère les opérations par catégorie',
        description: "Retourne la liste des opérations associées à une catégorie donnée.",
        tags: ['Opération'],
        parameters: [
            new OA\PathParameter(
                name: 'categoryId',
                description: 'Identifiant de la catégorie',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des opérations trouvées',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Operation::class, groups: ['operation:read']))
                )
            ),
        ]
    )]
    public function getByCategory(OperationService $operationService, string $categoryId): JsonResponse
    {
        $operations = $operationService->getOperationsByCategory($categoryId);
        return $this->json($operations, context: [
            'groups' => ['operation:read'],
        ]);
    }
}
